<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\LaporanKeuangan;
use App\Models\PembayaranCicilan;
use App\Models\Pengurus;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $pengurus = Auth::guard('pengurus')->user();

        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $totalAnggotaAktif = Anggota::where('status_keanggotaan', 'Terverifikasi')->count();
        $totalPengurusAktif = Pengurus::where('status', 'Menjabat')->count();
        $laporanBulanIni = LaporanKeuangan::where('periode_bulan', $bulanIni)
            ->where('periode_tahun', $tahunIni)
            ->count();

        $totalSimpananMasuk = Simpanan::where('jenis_transaksi', 'Setoran')
            ->where('status_transaksi', 'Berhasil')
            ->whereMonth('tanggal_transaksi', $bulanIni)
            ->whereYear('tanggal_transaksi', $tahunIni)
            ->sum('jumlah');

        $totalPinjamanDicairkan = Pinjaman::whereMonth('tanggal_pencairan', $bulanIni)
            ->whereYear('tanggal_pencairan', $tahunIni)
            ->sum('nominal_pinjaman');

        $cicilanBulanIni = PembayaranCicilan::where('status_pembayaran', 'Terverifikasi')
            ->whereMonth('tanggal_pembayaran', $bulanIni)
            ->whereYear('tanggal_pembayaran', $tahunIni)
            ->get();

        $totalCicilanMasuk = $cicilanBulanIni->sum('jumlah_pembayaran') - $cicilanBulanIni->sum('jumlah_denda');
        $totalDendaMasuk = $cicilanBulanIni->sum('jumlah_denda');

        $daftarPengurus = Pengurus::where('status', 'Menjabat')
            ->orderBy('id_pengurus')
            ->get();

        return view('ketua.dashboard', [
            'pengurus' => $pengurus,
            'totalAnggotaAktif' => $totalAnggotaAktif,
            'totalPengurusAktif' => $totalPengurusAktif,
            'laporanBulanIni' => $laporanBulanIni,
            'totalSimpananMasuk' => $totalSimpananMasuk,
            'totalPinjamanDicairkan' => $totalPinjamanDicairkan,
            'totalCicilanMasuk' => $totalCicilanMasuk,
            'totalDendaMasuk' => $totalDendaMasuk,
            'daftarPengurus' => $daftarPengurus,
            'periodeLabel' => now()->translatedFormat('M Y'),
        ]);
    }
}
