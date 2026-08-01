<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredAnggotaController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'digits:16', 'unique:anggota,nik'],
            'name' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:anggota,email'],
            'dokumen_pendukung' => ['nullable', 'image', 'max:2048'],
        ], [
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPath = $request->file('dokumen_pendukung')->store('dokumen-anggota', 'public');
        }

        $passwordSementara = Str::random(10);

        Anggota::create([
            'id_anggota' => Anggota::generateId(),
            'nik' => $request->nik,
            'nama_lengkap' => $request->name,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat_lengkap' => $request->alamat,
            'email' => $request->email,
            'dokumen_pendukung' => $dokumenPath,
            'password' => Hash::make($passwordSementara),
            'status_keanggotaan' => 'Menunggu Verifikasi',
            'wajib_ganti_password' => true,
        ]);

        return redirect()->route('login')
            ->with('status', 'Pendaftaran berhasil dikirim. Status: Menunggu Verifikasi Sekretaris.');
    }
}
