<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $simpananMenungguCount = Simpanan::where('status_transaksi', 'Menunggu')->count();

        // Tabel pinjaman diakses via DB::table (belum ada model Pinjaman yang dicek langsung di sini,
        // biar aman dari asumsi nama relasi/method yang belum tentu ada).
        $pinjamanMenungguCount = DB::table('pinjaman')
            ->where('status_pinjaman', 'Menunggu Persetujuan')
            ->count();

        $transaksiSimpananTerbaru = Simpanan::with('anggota')
            ->where('status_transaksi', 'Menunggu')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $pengajuanPinjamanTerbaru = DB::table('pinjaman')
            ->join('anggota', 'anggota.id_anggota', '=', 'pinjaman.id_anggota')
            ->where('pinjaman.status_pinjaman', 'Menunggu Persetujuan')
            ->select(
                'pinjaman.id_pinjaman',
                'anggota.nama_lengkap',
                'pinjaman.nominal_pinjaman',
                'pinjaman.tujuan_pinjaman'
            )
            ->orderByDesc('pinjaman.created_at')
            ->take(3)
            ->get();

        return view('bendahara.dashboard', [
            'simpananMenungguCount' => $simpananMenungguCount,
            'pinjamanMenungguCount' => $pinjamanMenungguCount,
            'transaksiSimpananTerbaru' => $transaksiSimpananTerbaru,
            'pengajuanPinjamanTerbaru' => $pengajuanPinjamanTerbaru,
        ]);
    }
}
