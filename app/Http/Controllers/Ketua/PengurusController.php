<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengurusController extends Controller
{
    /**
     * D-40 - Kelola Pengurus
     */
    public function index()
    {
        $daftarPengurus = Pengurus::orderByRaw("FIELD(jabatan, 'Ketua Koperasi', 'Sekretaris', 'Bendahara')")
            ->orderByDesc('status')
            ->orderBy('id_pengurus')
            ->get();

        $jabatanKosong = collect(['Sekretaris', 'Bendahara'])
            ->filter(function ($jabatan) use ($daftarPengurus) {
                return ! $daftarPengurus
                    ->where('jabatan', $jabatan)
                    ->where('status', 'Menjabat')
                    ->count();
            })
            ->values();

        return view('ketua.pengurus.index', [
            'daftarPengurus' => $daftarPengurus,
            'jabatanKosong' => $jabatanKosong,
        ]);
    }

    /**
     * D-41 - Form Tambah Pengurus
     */
    public function create()
    {
        return view('ketua.pengurus.create');
    }

    /**
     * D-41 - Simpan Pengurus Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengurus' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'in:Sekretaris,Bendahara'],
            'email' => ['required', 'email', 'max:255', 'unique:pengurus,email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'required' => 'Harap isi semua kolom — semua field wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar sebagai pengurus.',
        ]);

        Pengurus::create([
            'id_pengurus' => Pengurus::generateId($request->jabatan),
            'nama_pengurus' => $request->nama_pengurus,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'status' => 'Menjabat',
        ]);

        return redirect()->route('ketua.pengurus.index')
            ->with('success', 'Pengurus baru berhasil ditambahkan.');
    }

    /**
     * D-42 - Konfirmasi Berhentikan Pengurus (tampilan review sebelum submit)
     */
    public function confirmBerhentikan(string $id)
    {
        $pengurus = Pengurus::findOrFail($id);

        if ($pengurus->jabatan === 'Ketua Koperasi') {
            return redirect()->route('ketua.pengurus.index')
                ->with('error', 'Ketua Koperasi tidak dapat diberhentikan.');
        }

        return view('ketua.pengurus.berhentikan', [
            'pengurus' => $pengurus,
        ]);
    }

    /**
     * D-42 - Eksekusi Berhentikan Pengurus
     */
    public function berhentikan(string $id)
    {
        $pengurus = Pengurus::findOrFail($id);

        if ($pengurus->jabatan === 'Ketua Koperasi') {
            return redirect()->route('ketua.pengurus.index')
                ->with('error', 'Ketua Koperasi tidak dapat diberhentikan.');
        }

        $pengurus->update([
            'status' => 'Diberhentikan',
            'tanggal_diberhentikan' => now(),
        ]);

        return redirect()->route('ketua.pengurus.index')
            ->with('success', 'Pengurus berhasil diberhentikan.');
    }
}