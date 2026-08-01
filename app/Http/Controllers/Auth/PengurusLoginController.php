<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PengurusLoginController extends Controller
{
    public function create()
    {
        return view('auth.login-pengurus');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('pengurus')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah.',
            ]);
        }

        $request->session()->regenerate();

        $pengurus = Auth::guard('pengurus')->user();

        if ($pengurus->status !== 'Menjabat') {
            Auth::guard('pengurus')->logout();
            throw ValidationException::withMessages([
                'email' => 'Akun ini sudah tidak menjabat.',
            ]);
        }

        return match ($pengurus->jabatan) {
    'Ketua Koperasi' => redirect()->intended(route('ketua.laporan.pilih'))->with('login_success', true),
    'Sekretaris' => redirect()->intended(route('sekretaris.verifikasi'))->with('login_success', true),
    'Bendahara' => redirect()->intended(route('bendahara.simpanan.setoran'))->with('login_success', true),
};
    }
}