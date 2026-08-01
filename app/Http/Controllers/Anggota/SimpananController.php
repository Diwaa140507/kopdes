<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpananController extends Controller
{
    /**
     * D-07 - Form Setoran Simpanan
     */
    public function setor()
    {
        $idAnggota = Auth::guard('web')->id();
        $saldo = Simpanan::currentSaldo($idAnggota);

        return view('anggota.simpanan.setor', [
            'saldoWajib' => $saldo['wajib'],
            'saldoSukarela' => $saldo['sukarela'],
        ]);
    }

    public function setorStore(Request $request)
    {
        $idAnggota = Auth::guard('web')->id();

        $request->validate([
            'jenis_simpanan' => ['required', 'in:Wajib,Sukarela'],
            'jumlah' => ['required', 'numeric'],
            'metode_setoran' => ['required', 'in:QRIS,Tunai'],
            'bukti_qris' => [$request->metode_setoran === 'QRIS' ? 'required' : 'nullable', 'image', 'max:2048'],
        ], [
            'bukti_qris.required' => 'Bukti pembayaran QRIS wajib diupload.',
        ]);

        $minimal = $request->jenis_simpanan === 'Wajib' ? 50000 : 10000;
        if ($request->jumlah < $minimal) {
            return back()->withErrors([
                'jumlah' => 'Jumlah setoran ' . $request->jenis_simpanan . ' minimal Rp ' . number_format($minimal, 0, ',', '.'),
            ])->withInput();
        }

        $saldo = Simpanan::currentSaldo($idAnggota);
        $buktiPath = null;

        if ($request->hasFile('bukti_qris')) {
            $buktiPath = $request->file('bukti_qris')->store('bukti-simpanan', 'public');
        }

        // QRIS disimulasikan auto-verifikasi instan. Tunai menunggu konfirmasi Bendahara (D-26, belum dibangun).
        $langsungBerhasil = $request->metode_setoran === 'QRIS';

        $saldoWajibBaru = $saldo['wajib'] + ($request->jenis_simpanan === 'Wajib' && $langsungBerhasil ? $request->jumlah : 0);
        $saldoSukarelaBaru = $saldo['sukarela'] + ($request->jenis_simpanan === 'Sukarela' && $langsungBerhasil ? $request->jumlah : 0);

        Simpanan::create([
            'id_simpanan' => Simpanan::generateId(),
            'id_anggota' => $idAnggota,
            'jenis_simpanan' => $request->jenis_simpanan,
            'jenis_transaksi' => 'Setoran',
            'metode_setoran' => $request->metode_setoran,
            'bukti_transaksi' => $buktiPath,
            'status_transaksi' => $langsungBerhasil ? 'Berhasil' : 'Menunggu',
            'jumlah' => $request->jumlah,
            'tanggal_transaksi' => $langsungBerhasil ? now()->toDateString() : null,
            'saldo_simpanan_wajib' => $langsungBerhasil ? $saldoWajibBaru : $saldo['wajib'],
            'saldo_simpanan_sukarela' => $langsungBerhasil ? $saldoSukarelaBaru : $saldo['sukarela'],
        ]);

        $pesan = $langsungBerhasil
            ? 'Setoran berhasil diverifikasi otomatis, saldo sudah diperbarui.'
            : 'Setoran tunai tercatat, menunggu konfirmasi Bendahara.';

        return redirect()->route('simpanan.riwayat')->with('success', $pesan);
    }

    /**
     * D-08 - Form Penarikan Simpanan Sukarela
     */
    public function tarik()
    {
        $idAnggota = Auth::guard('web')->id();
        $saldo = Simpanan::currentSaldo($idAnggota);

        return view('anggota.simpanan.tarik', [
            'saldoWajib' => $saldo['wajib'],
            'saldoSukarela' => $saldo['sukarela'],
        ]);
    }

    public function tarikStore(Request $request)
    {
        $idAnggota = Auth::guard('web')->id();
        $saldo = Simpanan::currentSaldo($idAnggota);

        $request->validate([
            'jumlah' => ['required', 'numeric', 'min:50000'],
            'metode_penarikan' => ['required', 'in:Transfer Bank,Tunai'],
            'nama_bank_ewallet' => [$request->metode_penarikan === 'Transfer Bank' ? 'required' : 'nullable', 'string', 'max:100'],
            'no_rekening_tujuan' => [$request->metode_penarikan === 'Transfer Bank' ? 'required' : 'nullable', 'string', 'max:50'],
            'nama_pemilik_rekening' => [
                $request->metode_penarikan === 'Transfer Bank' ? 'required' : 'nullable',
                'string',
                'max:100'
            ],
        ]);

        if ($request->jumlah > $saldo['sukarela']) {
            return back()->withErrors([
                'jumlah' => 'Saldo tidak mencukupi. Saldo Sukarela saat ini: Rp ' . number_format($saldo['sukarela'], 0, ',', '.'),
            ])->withInput();
        }

        Simpanan::create([
            'id_simpanan' => Simpanan::generateId(),
            'id_anggota' => $idAnggota,
            'jenis_simpanan' => 'Sukarela',
            'jenis_transaksi' => 'Penarikan',
            'metode_penarikan' => $request->metode_penarikan,
            'nama_bank_ewallet' => $request->nama_bank_ewallet,
            'no_rekening_tujuan' => $request->no_rekening_tujuan,
            'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
            'status_transaksi' => 'Menunggu',
            'jumlah' => $request->jumlah,
            'saldo_simpanan_wajib' => $saldo['wajib'],
            'saldo_simpanan_sukarela' => $saldo['sukarela'],
        ]);

        return redirect()->route('simpanan.riwayat')
            ->with('success', 'Pengajuan penarikan berhasil dikirim, menunggu diproses Bendahara.');
    }

    /**
     * D-09 - Riwayat Simpanan (read-only)
     */
    public function riwayat(Request $request)
    {
        $idAnggota = Auth::guard('web')->id();
        $saldo = Simpanan::currentSaldo($idAnggota);

        $query = Simpanan::where('id_anggota', $idAnggota);

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('jenis_simpanan', 'like', '%' . $request->cari . '%')
                  ->orWhere('jenis_transaksi', 'like', '%' . $request->cari . '%');
            });
        }

        if ($request->filled('filter') && $request->filter !== 'Semua') {
            $query->where('jenis_simpanan', $request->filter);
        }

        $riwayat = $query->orderByDesc('created_at')->get();

        return view('anggota.simpanan.riwayat', [
            'saldoWajib' => $saldo['wajib'],
            'saldoSukarela' => $saldo['sukarela'],
            'riwayat' => $riwayat,
            'cari' => $request->cari,
            'filter' => $request->filter,
        ]);
    }
}
