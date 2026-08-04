<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VerifikasiPendaftaranController extends Controller
{
    /**
     * D-19 - Tab "Menunggu Verifikasi"
     */
    public function index(Request $request)
    {
        $query = Anggota::where('status_keanggotaan', 'Menunggu Verifikasi');

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhere('nik', 'like', "%{$keyword}%");
            });
        }

        $daftarCalonAnggota = $query->orderBy('tanggal_daftar', 'asc')->get();

        $selected = null;
        $nikSudahTerdaftar = false;

        if ($request->filled('detail')) {
            $selected = Anggota::where('id_anggota', $request->detail)
                ->where('status_keanggotaan', 'Menunggu Verifikasi')
                ->first();

            if ($selected) {
                // Validasi otomatis: cek NIK sudah terdaftar di anggota lain yang Terverifikasi
                $nikSudahTerdaftar = Anggota::where('nik', $selected->nik)
                    ->where('id_anggota', '!=', $selected->id_anggota)
                    ->where('status_keanggotaan', 'Terverifikasi')
                    ->exists();
            }
        }

        return view('sekretaris.verifikasi-pendaftaran', [
            'daftarCalonAnggota' => $daftarCalonAnggota,
            'selected' => $selected,
            'nikSudahTerdaftar' => $nikSudahTerdaftar,
            'cari' => $request->cari,
        ]);
    }

    /**
     * Aksi "Setujui" di D-19
     */
    public function setujui(Request $request, string $id)
    {
        $anggota = Anggota::where('id_anggota', $id)
            ->where('status_keanggotaan', 'Menunggu Verifikasi')
            ->firstOrFail();

        // Password default dengan format [ID_Anggota]-[DDMMYY]
        $tanggalVerifikasi = now();
        $passwordDefault = $anggota->id_anggota . '-' . $tanggalVerifikasi->format('dmy');

        $anggota->update([
            'status_keanggotaan' => 'Terverifikasi',
            'password' => bcrypt($passwordDefault),
            'wajib_ganti_password' => true,
            'tanggal_verifikasi' => $tanggalVerifikasi->toDateString(),
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
            'catatan_penolakan' => null,
        ]);

        return redirect()
            ->route('sekretaris.verifikasi')
            ->with('success', 'Pendaftaran ' . $anggota->nama_lengkap . ' berhasil disetujui. ID Anggota: ' . $anggota->id_anggota);
    }

    /**
     * Aksi "Tolak" di D-19
     */
    public function tolak(Request $request, string $id)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|max:255',
        ], [
            'catatan_penolakan.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $anggota = Anggota::where('id_anggota', $id)
            ->where('status_keanggotaan', 'Menunggu Verifikasi')
            ->firstOrFail();

        $anggota->update([
            'status_keanggotaan' => 'Ditolak',
            'tanggal_verifikasi' => now()->toDateString(),
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
            'catatan_penolakan' => $request->catatan_penolakan,
        ]);

        return redirect()
            ->route('sekretaris.verifikasi')
            ->with('success', 'Pendaftaran ' . $anggota->nama_lengkap . ' telah ditolak.');
    }

    /**
     * D-20 - Tab "Sudah Diproses"
     */
    public function sudahDiproses(Request $request)
    {
        $query = Anggota::whereIn('status_keanggotaan', ['Terverifikasi', 'Ditolak'])
            ->whereNotNull('tanggal_verifikasi');

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhere('nik', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('filter') && $request->filter !== 'Semua Status') {
            $query->where('status_keanggotaan', $request->filter);
        }

        $riwayat = $query->orderBy('tanggal_verifikasi', 'desc')->get();

        $selected = null;
        $passwordDefault = null;

        if ($request->filled('detail')) {
            $selected = Anggota::where('id_anggota', $request->detail)
                ->whereIn('status_keanggotaan', ['Terverifikasi', 'Ditolak'])
                ->first();

            if ($selected && $selected->status_keanggotaan === 'Terverifikasi' && $selected->tanggal_verifikasi) {
                $passwordDefault = $selected->id_anggota . '-' . \Carbon\Carbon::parse($selected->tanggal_verifikasi)->format('dmy');
            }
        }

        return view('sekretaris.verifikasi-sudah-diproses', [
            'riwayat' => $riwayat,
            'selected' => $selected,
            'passwordDefault' => $passwordDefault,
            'cari' => $request->cari,
            'filter' => $request->filter,
        ]);
    }
}