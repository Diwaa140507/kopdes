<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $anggota = Auth::guard('web')->user();

        // Blokir anggota yang statusnya sudah Terhapus — walau email+password cocok,
        // akun ini tidak boleh bisa mengakses sistem lagi.
        if ($anggota->status_keanggotaan === 'Terhapus') {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun ini sudah dihapus dan tidak dapat digunakan lagi.',
            ]);
        }

        $request->session()->regenerate();

        // Paksa ganti password kalau flag wajib_ganti_password aktif (mis. habis
        // di-reset Sekretaris di D-22) — sebelum masuk ke Simpanan (landing page anggota).
        if ($anggota->wajib_ganti_password) {
            return redirect()->route('ganti-password');
        }

        return redirect()->intended(RouteServiceProvider::HOME)->with('login_success', true);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}