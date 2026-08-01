<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PengurusLoginController extends Controller
{
    /**
     * Tampilkan form login pengurus (D-02).
     */
    public function create()
    {
        return view('auth.login-pengurus');
    }

    /**
     * Proses login pengurus.
     * Email format: [jabatan].[id]@koperasimerahputih.id
     * Role yang diizinkan: ketua, sekretaris, bendahara
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Pastikan yang login memang pengurus, bukan anggota biasa
        if (! in_array($user->role, ['ketua', 'sekretaris', 'bendahara'])) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun ini bukan akun pengurus.',
            ]);
        }

        // Arahkan sesuai role (lihat NAVIGASI di wireframe D-02)
        return match ($user->role) {
            'ketua' => redirect()->intended('/dashboard/ketua'),      // D-16
            'sekretaris' => redirect()->intended('/dashboard/sekretaris'), // D-08
            'bendahara' => redirect()->intended('/dashboard/bendahara'),  // D-12
        };
    }
}