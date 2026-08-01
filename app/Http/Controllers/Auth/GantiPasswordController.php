<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GantiPasswordController extends Controller
{
    /**
     * Tampilkan form paksa ganti password (D-05).
     */
    public function create(): View|RedirectResponse
    {
        $anggota = Auth::guard('web')->user();

        // Kalau ternyata sudah tidak wajib ganti password, langsung ke dashboard
        if (! $anggota->wajib_ganti_password) {
            return redirect('/dashboard');
        }

        return view('auth.ganti-password');
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

        $anggota = Auth::guard('web')->user();

        $anggota->update([
            'password' => Hash::make($request->password),
            'wajib_ganti_password' => false,
            // Password sementara (dari D-22 reset) sudah tidak berlaku lagi -> kosongkan
            // supaya tidak nyangkut di detail D-23 setelah anggota ganti password sendiri.
            'password_sementara_plain' => null,
        ]);

        return redirect('/dashboard')->with('status', 'Password berhasil diubah.');
    }
}
