<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $menungguVerifikasiCount = Anggota::where('status_keanggotaan', 'Menunggu Verifikasi')->count();
        $resetMenungguCount = Anggota::where('status_permintaan_reset', 'menunggu_diproses')->count();

        // Antrian pengajuan penghapusan akun — kondisi sama persis dengan D-24 (PenghapusanAnggotaController)
        $penghapusanCount = Anggota::whereNotNull('alasan_penghapusan')
            ->where('alasan_penghapusan', '!=', '')
            ->where('status_keanggotaan', '!=', 'Terhapus')
            ->count();

        $antrianVerifikasi = Anggota::where('status_keanggotaan', 'Menunggu Verifikasi')
            ->orderBy('tanggal_daftar', 'desc')
            ->take(5)
            ->get();

        return view('sekretaris.dashboard', [
            'menungguVerifikasiCount' => $menungguVerifikasiCount,
            'resetMenungguCount' => $resetMenungguCount,
            'penghapusanCount' => $penghapusanCount,
            'antrianVerifikasi' => $antrianVerifikasi,
        ]);
    }
}
