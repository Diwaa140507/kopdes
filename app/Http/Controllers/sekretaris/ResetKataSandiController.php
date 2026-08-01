<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResetKataSandiController extends Controller
{
    /**
     * D-22 - Tab "Menunggu"
     */
    public function index(Request $request)
    {
        $antrian = Anggota::where('status_permintaan_reset', 'menunggu_diproses')
            ->orderBy('tanggal_perubahan_terakhir', 'asc')
            ->get();

        $selected = null;

        if ($request->filled('detail')) {
            $selected = Anggota::where('id_anggota', $request->detail)
                ->where('status_permintaan_reset', 'menunggu_diproses')
                ->first();
        }

        return view('sekretaris.kelola-data-anggota.reset-kata-sandi-menunggu', [
            'antrian' => $antrian,
            'selected' => $selected,
        ]);
    }

    /**
     * Aksi "Konfirmasi Reset" di D-22
     */
    public function konfirmasi(Request $request, string $id)
    {
        $request->validate([
            'password_baru' => ['required', 'string', 'min:8'],
        ], [
            'password_baru.required' => 'Password baru wajib diisi (klik Generate atau isi manual).',
            'password_baru.min' => 'Password baru minimal 8 karakter.',
        ]);

        $anggota = Anggota::where('id_anggota', $id)
            ->where('status_permintaan_reset', 'menunggu_diproses')
            ->firstOrFail();

        $anggota->update([
            'password' => bcrypt($request->password_baru),
            // Disimpan plaintext juga (atas persetujuan user) supaya bisa dilihat lagi di detail D-23.
            'password_sementara_plain' => $request->password_baru,
            'wajib_ganti_password' => true,
            'status_permintaan_reset' => 'selesai',
            'tanggal_perubahan_terakhir' => now()->toDateString(),
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        return redirect()
            ->route('sekretaris.kelola-data-anggota.reset-kata-sandi')
            ->with('success', 'Password baru untuk ' . $anggota->nama_lengkap . ' berhasil diset. Sampaikan secara manual/luring ke anggota.');
    }

    /**
     * D-23 - Tab "Sudah Diproses"
     */
    public function sudahDiproses(Request $request)
    {
        $riwayat = Anggota::where('status_permintaan_reset', 'selesai')
            ->orderBy('tanggal_perubahan_terakhir', 'desc')
            ->get();

        $selected = null;

        if ($request->filled('detail')) {
            $selected = Anggota::where('id_anggota', $request->detail)
                ->where('status_permintaan_reset', 'selesai')
                ->first();
        }

        return view('sekretaris.kelola-data-anggota.reset-kata-sandi-sudah-diproses', [
            'riwayat' => $riwayat,
            'selected' => $selected,
        ]);
    }
}
