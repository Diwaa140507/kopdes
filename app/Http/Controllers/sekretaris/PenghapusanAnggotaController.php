<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenghapusanAnggotaController extends Controller
{
    /**
     * D-24 - Antrian Pengajuan Penghapusan Akun + Panel Tinjau (?detail=ID_ANGGOTA)
     */
    public function index(Request $request)
    {
        $antrian = Anggota::whereNotNull('alasan_penghapusan')
            ->where('alasan_penghapusan', '!=', '')
            ->where('status_keanggotaan', '!=', 'Terhapus')
            ->orderBy('tanggal_perubahan_terakhir', 'asc')
            ->get();

        $selected = null;
        $syarat = null;

        if ($request->filled('detail')) {
            $selected = Anggota::whereNotNull('alasan_penghapusan')
                ->where('alasan_penghapusan', '!=', '')
                ->where('status_keanggotaan', '!=', 'Terhapus')
                ->where('id_anggota', $request->detail)
                ->first();

            if ($selected) {
                $syarat = $this->cekSyaratKelayakan($selected->id_anggota);
            }
        }

        return view('sekretaris.kelola-data-anggota.penghapusan-anggota', [
            'antrian' => $antrian,
            'daftarSelesai' => collect(),
            'selected' => $selected,
            'syarat' => $syarat,
            'toggleAktif' => 'menunggu',
        ]);
    }

    /**
     * Riwayat Penghapusan — daftar anggota yang statusnya sudah "Terhapus".
     * Pola toggle sama seperti Reset Kata Sandi (Menunggu / Sudah Diproses),
     * bukan tab besar terpisah.
     */
    public function sudahDiproses(Request $request)
    {
        $daftar = Anggota::where('status_keanggotaan', 'Terhapus')
            ->orderByDesc('tanggal_perubahan_terakhir')
            ->get();

        return view('sekretaris.kelola-data-anggota.penghapusan-anggota', [
            'antrian' => collect(),
            'daftarSelesai' => $daftar,
            'selected' => null,
            'syarat' => null,
            'toggleAktif' => 'selesai',
        ]);
    }

    /**
     * Aksi "Hapus Anggota" di Panel Tinjau D-24
     */
    public function hapus(Request $request, string $id)
    {
        $anggota = Anggota::whereNotNull('alasan_penghapusan')
            ->where('alasan_penghapusan', '!=', '')
            ->where('status_keanggotaan', '!=', 'Terhapus')
            ->where('id_anggota', $id)
            ->firstOrFail();

        $syarat = $this->cekSyaratKelayakan($anggota->id_anggota);

        if (!$syarat['semua_terpenuhi']) {
            return redirect()
                ->route('sekretaris.kelola-data-anggota.penghapusan', ['detail' => $id])
                ->with('error', 'Anggota belum memenuhi seluruh syarat kelayakan penghapusan.');
        }

        $anggota->update([
            'status_keanggotaan' => 'Terhapus',
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        return redirect()
            ->route('sekretaris.kelola-data-anggota.penghapusan')
            ->with('success', 'Akun ' . $anggota->nama_lengkap . ' (' . $anggota->id_anggota . ') berhasil dihapus.');
    }

    /**
     * Cek otomatis 3 syarat kelayakan penghapusan.
     * Keputusan user: "Tidak ada cicilan tertunggak" ikut hasil cek "Tidak ada pinjaman aktif"
     * (tidak dicek terpisah, karena tabel pembayaran_cicilan tidak punya status "tertunggak").
     */
    private function cekSyaratKelayakan(string $idAnggota): array
    {
        $tidakAdaPinjamanAktif = !Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->exists();

        $saldo = Simpanan::currentSaldo($idAnggota);
        $tidakAdaSaldoTersisa = (float) ($saldo['wajib'] ?? 0) == 0.0
            && (float) ($saldo['sukarela'] ?? 0) == 0.0;

        $tidakAdaCicilanTertunggak = $tidakAdaPinjamanAktif;

        return [
            'tidak_ada_pinjaman_aktif' => $tidakAdaPinjamanAktif,
            'tidak_ada_saldo_tersisa' => $tidakAdaSaldoTersisa,
            'tidak_ada_cicilan_tertunggak' => $tidakAdaCicilanTertunggak,
            'semua_terpenuhi' => $tidakAdaPinjamanAktif && $tidakAdaSaldoTersisa,
        ];
    }
}