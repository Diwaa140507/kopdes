<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\PembayaranCicilan;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $anggota = Auth::guard('web')->user();
        $idAnggota = $anggota->id_anggota;

        // Saldo simpanan -> ambil dari transaksi Berhasil terakhir (running balance)
        $simpananTerakhir = Simpanan::where('id_anggota', $idAnggota)
            ->where('status_transaksi', 'Berhasil')
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id_simpanan')
            ->first();

        $saldoWajib = $simpananTerakhir->saldo_simpanan_wajib ?? 0;
        $saldoSukarela = $simpananTerakhir->saldo_simpanan_sukarela ?? 0;

        // Pinjaman aktif (kalau ada lebih dari 1 -> ambil yang terbaru)
        $pinjamanAktif = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->orderByDesc('tanggal_pencairan')
            ->first();

        $sisaHutang = 0;
        $pengingatCicilan = null;

        if ($pinjamanAktif) {
            $cicilanTerverifikasi = PembayaranCicilan::where('id_pinjaman', $pinjamanAktif->id_pinjaman)
                ->where('status_pembayaran', 'Terverifikasi')
                ->orderByDesc('no_angsuran')
                ->first();

            $sisaHutang = $cicilanTerverifikasi->sisa_hutang ?? $pinjamanAktif->total_pengembalian;
            $angsuranTerakhirLunas = $cicilanTerverifikasi->no_angsuran ?? 0;
            $angsuranBerikutnya = $angsuranTerakhirLunas + 1;

            if ($angsuranBerikutnya <= $pinjamanAktif->tenor_bulan && $pinjamanAktif->jadwal_jatuh_tempo) {
                $jatuhTempo = \Carbon\Carbon::parse($pinjamanAktif->jadwal_jatuh_tempo)
                    ->addMonths($angsuranTerakhirLunas);

                $pengingatCicilan = [
                    'angsuran_ke' => $angsuranBerikutnya,
                    'total_angsuran' => $pinjamanAktif->tenor_bulan,
                    'jatuh_tempo' => $jatuhTempo,
                    'nominal' => $pinjamanAktif->cicilan_per_bulan,
                    'terlambat' => $jatuhTempo->isPast(),
                ];
            }
        }

        // Riwayat transaksi terakhir (gabungan Simpanan + Pinjaman + Cicilan), 5 terbaru
        $riwayatSimpanan = Simpanan::where('id_anggota', $idAnggota)
            ->where('status_transaksi', 'Berhasil')
            ->get()
            ->map(fn($item) => [
                'tanggal' => $item->tanggal_transaksi,
                'jenis' => $item->jenis_simpanan,
                'keterangan' => $item->jenis_transaksi,
                'nominal' => $item->jumlah,
            ]);

        $riwayatPinjaman = Pinjaman::where('id_anggota', $idAnggota)
            ->whereNotNull('tanggal_pencairan')
            ->get()
            ->map(fn($item) => [
                'tanggal' => $item->tanggal_pencairan,
                'jenis' => 'Pinjaman',
                'keterangan' => 'Pencairan Dana',
                'nominal' => $item->nominal_pinjaman,
            ]);

        $riwayatCicilan = PembayaranCicilan::where('id_anggota', $idAnggota)
            ->where('status_pembayaran', 'Terverifikasi')
            ->get()
            ->map(fn($item) => [
                'tanggal' => $item->tanggal_pembayaran,
                'jenis' => 'Cicilan',
                'keterangan' => 'Angsuran ke-' . $item->no_angsuran,
                'nominal' => $item->jumlah_pembayaran,
            ]);

        $riwayatTransaksi = $riwayatSimpanan
            ->concat($riwayatPinjaman)
            ->concat($riwayatCicilan)
            ->sortByDesc('tanggal')
            ->take(5)
            ->values();

        return view('anggota.dashboard', [
            'anggota' => $anggota,
            'saldoWajib' => $saldoWajib,
            'saldoSukarela' => $saldoSukarela,
            'pinjamanAktif' => $pinjamanAktif,
            'sisaHutang' => $sisaHutang,
            'pengingatCicilan' => $pengingatCicilan,
            'riwayatTransaksi' => $riwayatTransaksi,
        ]);
    }
}
