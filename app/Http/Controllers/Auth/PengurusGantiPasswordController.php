<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PengurusGantiPasswordController extends Controller
{
    /**
     * Tampilkan form paksa ganti password untuk Pengurus baru (setelah D-41).
     */
    public function create(): View|RedirectResponse
    {
        $pengurus = Auth::guard('pengurus')->user();

        // Kalau ternyata sudah tidak wajib ganti password, langsung ke dashboard sesuai jabatan
        if (! $pengurus->wajib_ganti_password) {
            return redirect($this->dashboardPath($pengurus->jabatan));
        }

        return view('auth.ganti-password-pengurus');
    }

    /**
     * Simpan password baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $pengurus = Auth::guard('pengurus')->user();

        $pengurus->update([
            'password' => Hash::make($request->password),
            'wajib_ganti_password' => false,
        ]);

        return redirect($this->dashboardPath($pengurus->jabatan))
            ->with('status', 'Password berhasil diubah.');
    }

    private function dashboardPath(string $jabatan): string
    {
        return match ($jabatan) {
            'Ketua Koperasi' => '/dashboard/ketua',
            'Sekretaris' => '/dashboard/sekretaris',
            'Bendahara' => '/dashboard/bendahara',
        };
    }
}
