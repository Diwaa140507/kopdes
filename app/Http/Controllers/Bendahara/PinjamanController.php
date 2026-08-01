<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\PembayaranCicilan;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PinjamanController extends Controller
{
    // Formula HARUS SAMA PERSIS dengan App\Http\Controllers\Anggota\PinjamanController
    const KELIPATAN_BATAS_MAKSIMAL = 3;
    const PERSENTASE_JASA = 1.5;
    const TOLERANSI_TERLAMBAT = 2;

    /**
     * D-28 - Tab "Tinjau Pengajuan"
     */
    public function tinjau(Request $request)
    {
        $keyword = $request->input('cari');

        $antrian = Pinjaman::with('anggota')
            ->where('status_pinjaman', 'Menunggu Persetujuan')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('id_anggota', 'like', "%{$keyword}%")
                        ->orWhereHas('anggota', function ($qq) use ($keyword) {
                            $qq->where('nama_lengkap', 'like', "%{$keyword}%");
                        });
                });
            })
            ->orderBy('created_at')
            ->get();

        $selected = null;
        $kelayakan = null;

        if ($request->filled('detail')) {
            $selected = Pinjaman::with('anggota')
                ->where('status_pinjaman', 'Menunggu Persetujuan')
                ->where('id_pinjaman', $request->detail)
                ->first();

            if ($selected) {
                $kelayakan = $this->cekKelayakan($selected);
            }
        }

        return view('bendahara.pinjaman.tinjau', [
            'antrian' => $antrian,
            'selected' => $selected,
            'kelayakan' => $kelayakan,
            'cari' => $keyword,
        ]);
    }

    /**
     * Aksi "Setujui" di D-28 — pinjaman pindah ke antrian pencairan (D-29).
     */
    public function setujui(Request $request, string $id)
    {
        $pinjaman = Pinjaman::where('status_pinjaman', 'Menunggu Persetujuan')
            ->where('id_pinjaman', $id)
            ->firstOrFail();

        $kelayakan = $this->cekKelayakan($pinjaman);

        if (! $kelayakan['layak']) {
            return redirect()
                ->route('bendahara.pinjaman.tinjau', ['detail' => $id])
                ->with('error', 'Pengajuan ini berstatus "Tidak Layak" menurut sistem, tidak bisa disetujui.');
        }

        $pinjaman->update([
            'status_pinjaman' => 'Menunggu Pencairan',
        ]);

        return redirect()
            ->route('bendahara.pinjaman.tinjau')
            ->with('success', 'Pengajuan ' . $pinjaman->id_pinjaman . ' disetujui, masuk antrian Proses Pencairan.');
    }

    /**
     * Aksi "Tolak" di D-28 — catatan penolakan wajib diisi.
     */
    public function tolak(Request $request, string $id)
    {
        $request->validate([
            'alasan_penolakan' => ['required', 'string', 'max:255'],
        ], [
            'alasan_penolakan.required' => 'Harap isi semua kolom — Catatan Penolakan wajib diisi.',
        ]);

        $pinjaman = Pinjaman::where('status_pinjaman', 'Menunggu Persetujuan')
            ->where('id_pinjaman', $id)
            ->firstOrFail();

        $pinjaman->update([
            'status_pinjaman' => 'Ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return redirect()
            ->route('bendahara.pinjaman.tinjau')
            ->with('success', 'Pengajuan ' . $pinjaman->id_pinjaman . ' ditolak.');
    }

    /**
     * D-29 - Tab "Proses Pencairan"
     */
    public function pencairan(Request $request)
    {
        $antrian = Pinjaman::with('anggota')
            ->where('status_pinjaman', 'Menunggu Pencairan')
            ->orderBy('updated_at')
            ->get();

        $selected = null;

        if ($request->filled('detail')) {
            $selected = Pinjaman::with('anggota')
                ->where('status_pinjaman', 'Menunggu Pencairan')
                ->where('id_pinjaman', $request->detail)
                ->first();
        }

        return view('bendahara.pinjaman.pencairan', [
            'antrian' => $antrian,
            'selected' => $selected,
        ]);
    }

    /**
     * Aksi "Konfirmasi Pencairan" di D-29 — status jadi Aktif, jadwal cicilan mulai berjalan
     * (jadwal_jatuh_tempo = tanggal jatuh tempo angsuran ke-1, dihitung otomatis dari
     * tanggal_pencairan + 1 bulan; angsuran berikutnya dihitung on-the-fly di sisi Anggota
     * seperti yang sudah berjalan di PinjamanController@detail).
     */
    public function cairkan(Request $request, string $id)
    {
        $request->validate([
            'bukti_pencairan' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'catatan_pencairan' => ['nullable', 'string', 'max:255'],
        ], [
            'bukti_pencairan.required' => 'Bukti Pencairan wajib diunggah sebelum konfirmasi.',
        ]);

        $pinjaman = Pinjaman::where('status_pinjaman', 'Menunggu Pencairan')
            ->where('id_pinjaman', $id)
            ->firstOrFail();

        $path = $request->file('bukti_pencairan')->store('bukti-pencairan', 'public');

        $tanggalPencairan = now()->toDateString();

        $pinjaman->update([
            'status_pinjaman' => 'Aktif',
            'tanggal_pencairan' => $tanggalPencairan,
            'jadwal_jatuh_tempo' => now()->addMonth()->toDateString(),
            'bukti_pencairan' => $path,
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        return redirect()
            ->route('bendahara.pinjaman.pencairan')
            ->with('success', 'Pinjaman ' . $pinjaman->id_pinjaman . ' berhasil dicairkan, status menjadi Aktif.');
    }

    /**
     * D-30 - Tab "Riwayat Pinjaman" (read-only, lintas status)
     */
    public function riwayat(Request $request)
    {
        $keyword = $request->input('cari');
        $status = $request->input('status');

        $daftar = Pinjaman::with('anggota', 'pengurusPencatat')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('id_anggota', 'like', "%{$keyword}%")
                        ->orWhereHas('anggota', function ($qq) use ($keyword) {
                            $qq->where('nama_lengkap', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($status && $status !== 'Semua', function ($query) use ($status) {
                $query->where('status_pinjaman', $status);
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($pinjaman) {
                $pinjaman->sisa_hutang_computed = $this->hitungSisaHutang($pinjaman);
                return $pinjaman;
            });

        $selected = null;

        if ($request->filled('detail')) {
            $selected = $daftar->firstWhere('id_pinjaman', $request->detail);
        }

        return view('bendahara.pinjaman.riwayat', [
            'daftar' => $daftar,
            'selected' => $selected,
            'cari' => $keyword,
            'status' => $status ?? 'Semua',
        ]);
    }

    /**
     * Helper: hitung sisa hutang (computed, bukan kolom fisik).
     */
    private function hitungSisaHutang(Pinjaman $pinjaman): ?float
    {
        if ($pinjaman->status_pinjaman === 'Lunas') {
            return 0;
        }

        if (! in_array($pinjaman->status_pinjaman, ['Aktif', 'Lunas'])) {
            return null;
        }

        $cicilanTerbayar = PembayaranCicilan::where('id_pinjaman', $pinjaman->id_pinjaman)
            ->where('status_pembayaran', 'Terverifikasi')
            ->get();

        $totalTerbayar = $cicilanTerbayar->sum('jumlah_pembayaran') - $cicilanTerbayar->sum('jumlah_denda');

        return max($pinjaman->total_pengembalian - $totalTerbayar, 0);
    }

    /**
     * Helper: replikasi PERSIS formula kelayakan dari Anggota\PinjamanController@cekKelayakanStore,
     * dipakai untuk hitung ulang badge Layak/Tdk Layak di sisi Bendahara (D-28) supaya konsisten.
     */
    private function cekKelayakan(Pinjaman $pinjaman): array
    {
        $idAnggota = $pinjaman->id_anggota;

        $saldo = Simpanan::currentSaldo($idAnggota);
        $totalSimpanan = $saldo['wajib'] + $saldo['sukarela'];
        $batasMaksimal = $totalSimpanan * self::KELIPATAN_BATAS_MAKSIMAL;

        // Pinjaman aktif LAIN selain pengajuan yang sedang ditinjau ini
        $pinjamanAktif = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->exists();

        $adaTunggakan = false;
        $pinjamanBerjalan = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->first();
        if ($pinjamanBerjalan && $pinjamanBerjalan->jadwal_jatuh_tempo) {
            $angsuranLunas = PembayaranCicilan::where('id_pinjaman', $pinjamanBerjalan->id_pinjaman)
                ->where('status_pembayaran', 'Terverifikasi')->count();
            $jatuhTempoBerikutnya = \Carbon\Carbon::parse($pinjamanBerjalan->jadwal_jatuh_tempo)->addMonths($angsuranLunas);
            $adaTunggakan = $jatuhTempoBerikutnya->isPast();
        }

        $rasioPersen = $batasMaksimal > 0 ? round(($pinjaman->nominal_pinjaman / $batasMaksimal) * 100) : 100;
        $rasioAman = $pinjaman->nominal_pinjaman <= $batasMaksimal;

        $jumlahTerlambat = PembayaranCicilan::where('id_anggota', $idAnggota)
            ->where('status_pembayaran', 'Terverifikasi')
            ->where('jumlah_denda', '>', 0)
            ->count();
        $riwayatAman = $jumlahTerlambat <= self::TOLERANSI_TERLAMBAT;

        $layak = ! $pinjamanAktif && ! $adaTunggakan && $rasioAman && $riwayatAman;

        return [
            'layak' => $layak,
            'punya_pinjaman_aktif' => $pinjamanAktif,
            'ada_tunggakan' => $adaTunggakan,
            'rasio_persen' => $rasioPersen,
            'rasio_aman' => $rasioAman,
            'jumlah_terlambat' => $jumlahTerlambat,
            'riwayat_aman' => $riwayatAman,
            'batas_maksimal' => $batasMaksimal,
            'saldo_simpanan' => $totalSimpanan,
        ];
    }
}
