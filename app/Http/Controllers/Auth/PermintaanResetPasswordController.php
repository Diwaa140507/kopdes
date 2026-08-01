<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PermintaanResetPasswordController extends Controller
{
    public function create()
    {
        return view('auth.lupa-kata-sandi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'nik' => ['required', 'digits:16'],
        ]);

        $anggota = Anggota::where('email', $request->email)
            ->where('nik', $request->nik)
            ->first();

        if (! $anggota) {
            throw ValidationException::withMessages([
                'email' => 'Data tidak ditemukan. Email dan NIK tidak cocok dengan data anggota manapun.',
            ]);
        }

        $anggota->update([
            'status_permintaan_reset' => 'menunggu_diproses',
        ]);

        return back()->with('reset_terkirim', true);
    }
}
