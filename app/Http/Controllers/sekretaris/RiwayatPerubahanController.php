<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatPerubahanController extends Controller
{
    /**
     * D-21 - Riwayat Perubahan Data Anggota (read-only)
     * Sumber data: tabel perubahan_anggota, hasil log otomatis dari D-16 (Ubah Data Diri, self-service).
     */
    public function index(Request $request)
    {
        $keyword = $request->input('cari');

        $riwayat = DB::table('perubahan_anggota')
            ->join('anggota', 'anggota.id_anggota', '=', 'perubahan_anggota.id_anggota')
            ->select(
                'perubahan_anggota.id_perubahan',
                'perubahan_anggota.id_anggota',
                'anggota.nama_lengkap',
                'perubahan_anggota.jenis_perubahan',
                'perubahan_anggota.data_lama',
                'perubahan_anggota.data_baru',
                'perubahan_anggota.tanggal_perubahan'
            )
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('perubahan_anggota.id_anggota', 'like', "%{$keyword}%")
                        ->orWhere('anggota.nama_lengkap', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('perubahan_anggota.tanggal_perubahan', 'desc')
            ->get();

        return view('sekretaris.kelola-data-anggota.riwayat-perubahan', [
            'riwayat' => $riwayat,
            'cari' => $keyword,
        ]);
    }
}
