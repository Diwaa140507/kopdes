<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\PembayaranCicilan;
use App\Models\PerubahanAnggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    /**
     * Hitung ringkasan pinjaman aktif (dipakai bersama D-15 & D-17).
     */
    private function ringkasanPinjaman(string $idAnggota)
    {
        $pinjamanAktif = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->orderByDesc('tanggal_pencairan')
            ->first();

        $sisaHutang = 0;

        if ($pinjamanAktif) {
            $totalTerbayar = PembayaranCicilan::where('id_pinjaman', $pinjamanAktif->id_pinjaman)
                ->where('status_pembayaran', 'Terverifikasi')
                ->sum('jumlah_pembayaran')
                - PembayaranCicilan::where('id_pinjaman', $pinjamanAktif->id_pinjaman)
                ->where('status_pembayaran', 'Terverifikasi')
                ->sum('jumlah_denda');

            $sisaHutang = max($pinjamanAktif->total_pengembalian - $totalTerbayar, 0);
        }

        return [$pinjamanAktif, $sisaHutang];
    }

    /**
     * D-15 - Detail Profil Anggota (read-only)
     */
    public function detail()
    {
        $anggota = Auth::guard('web')->user();

        $saldo = Simpanan::currentSaldo($anggota->id_anggota);
        $totalSimpanan = $saldo['wajib'] + $saldo['sukarela'];

        [$pinjamanAktif, $sisaHutang] = $this->ringkasanPinjaman($anggota->id_anggota);

        return view('anggota.profil.detail', [
            'anggota' => $anggota,
            'totalSimpanan' => $totalSimpanan,
            'statusPinjaman' => $pinjamanAktif ? 'Aktif' : 'Tidak Ada',
            'sisaHutang' => $sisaHutang,
        ]);
    }

    /**
     * D-16 - Ubah Data Diri (self-service, langsung tersimpan tanpa approval Sekretaris)
     */
    public function ubahDataDiri()
    {
        $anggota = Auth::guard('web')->user();

        return view('anggota.profil.ubah-data-diri', [
            'anggota' => $anggota,
        ]);
    }

    public function ubahDataDiriStore(Request $request)
    {
        $anggota = Auth::guard('web')->user();

        $request->validate([
            'email' => ['required', 'email', Rule::unique('anggota', 'email')->ignore($anggota->id_anggota, 'id_anggota')],
            'alamat_lengkap' => ['required', 'string', 'max:255'],
            'kata_sandi_lama' => ['nullable', 'required_with:kata_sandi_baru,konfirmasi_kata_sandi', 'string'],
            'kata_sandi_baru' => ['nullable', 'string', 'min:8', 'required_with:kata_sandi_lama', 'same:konfirmasi_kata_sandi'],
            'konfirmasi_kata_sandi' => ['nullable', 'string'],
        ], [
            'kata_sandi_baru.same' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi baru.',
            'kata_sandi_lama.required_with' => 'Kata sandi lama wajib diisi jika ingin mengganti kata sandi.',
        ]);

        if ($request->filled('kata_sandi_lama')) {
            if (! Hash::check($request->kata_sandi_lama, $anggota->password)) {
                return back()->withErrors(['kata_sandi_lama' => 'Kata sandi lama tidak sesuai.'])->withInput();
            }
        }

        // Catat perubahan Email (kalau berubah)
        if ($request->email !== $anggota->email) {
            PerubahanAnggota::create([
                'id_perubahan' => PerubahanAnggota::generateId(),
                'id_anggota' => $anggota->id_anggota,
                'jenis_perubahan' => 'Email',
                'data_lama' => $anggota->email,
                'data_baru' => $request->email,
                'tanggal_perubahan' => now(),
            ]);
            $anggota->email = $request->email;
        }

        // Catat perubahan Alamat (kalau berubah)
        if ($request->alamat_lengkap !== $anggota->alamat_lengkap) {
            PerubahanAnggota::create([
                'id_perubahan' => PerubahanAnggota::generateId(),
                'id_anggota' => $anggota->id_anggota,
                'jenis_perubahan' => 'Alamat',
                'data_lama' => $anggota->alamat_lengkap,
                'data_baru' => $request->alamat_lengkap,
                'tanggal_perubahan' => now(),
            ]);
            $anggota->alamat_lengkap = $request->alamat_lengkap;
        }

        // Catat perubahan Kata Sandi (data_lama/data_baru dikosongkan demi keamanan)
        if ($request->filled('kata_sandi_baru')) {
            PerubahanAnggota::create([
                'id_perubahan' => PerubahanAnggota::generateId(),
                'id_anggota' => $anggota->id_anggota,
                'jenis_perubahan' => 'Kata_Sandi',
                'data_lama' => null,
                'data_baru' => null,
                'tanggal_perubahan' => now(),
            ]);
            $anggota->password = Hash::make($request->kata_sandi_baru);
        }

        $anggota->tanggal_perubahan_terakhir = now();
        $anggota->save();

        return redirect()->route('profil.ubah-data-diri')
            ->with('success', 'Data berhasil diubah.');
    }

    /**
     * D-17 - Ajukan Penghapusan Akun
     */
    public function ajukanPenghapusan()
    {
        $anggota = Auth::guard('web')->user();

        $saldo = Simpanan::currentSaldo($anggota->id_anggota);
        $totalSimpanan = $saldo['wajib'] + $saldo['sukarela'];

        [$pinjamanAktif, $sisaHutang] = $this->ringkasanPinjaman($anggota->id_anggota);

        $tidakAdaPinjamanAktif = ! $pinjamanAktif;
        // Syarat baru: cukup simpanan SUKARELA = 0. Simpanan wajib boleh masih ada sisa,
        // nanti otomatis ikut ditarik lewat pengajuan penghapusan ini.
        $saldoNol = $saldo['sukarela'] == 0;
        $tidakAdaTunggakan = ! $pinjamanAktif; // tunggakan cicilan hanya mungkin ada jika ada pinjaman aktif

        $memenuhiSyarat = $tidakAdaPinjamanAktif && $saldoNol && $tidakAdaTunggakan;

        $sedangDiajukan = ! empty($anggota->alasan_penghapusan);

        return view('anggota.profil.ajukan-penghapusan', [
            'anggota' => $anggota,
            'tidakAdaPinjamanAktif' => $tidakAdaPinjamanAktif,
            'saldoNol' => $saldoNol,
            'tidakAdaTunggakan' => $tidakAdaTunggakan,
            'memenuhiSyarat' => $memenuhiSyarat,
            'sedangDiajukan' => $sedangDiajukan,
            'totalSimpanan' => $totalSimpanan,
            'saldoWajib' => $saldo['wajib'],
            'sisaHutang' => $sisaHutang,
        ]);
    }

    public function ajukanPenghapusanStore(Request $request)
    {
        $anggota = Auth::guard('web')->user();

        $saldo = Simpanan::currentSaldo($anggota->id_anggota);
        [$pinjamanAktif] = $this->ringkasanPinjaman($anggota->id_anggota);

        if ($pinjamanAktif || $saldo['sukarela'] != 0) {
            return redirect()->route('profil.ajukan-penghapusan')
                ->with('info', 'Syarat penghapusan akun belum terpenuhi.');
        }

        $rules = [
            'alasan_penghapusan' => ['required', 'string', 'max:500'],
        ];

        // Kalau masih ada sisa simpanan wajib, wajibkan metode & tujuan penarikan
        // supaya Bendahara tahu ke mana harus mengirim dana penarikan otomatis ini.
        if ($saldo['wajib'] > 0) {
            $rules['metode_penarikan_wajib'] = ['required', 'in:Transfer Bank,E-Wallet'];
            $rules['no_rekening_tujuan_wajib'] = ['required', 'string', 'max:50'];
        }

        $request->validate($rules, [
            'metode_penarikan_wajib.required' => 'Metode penarikan wajib dipilih karena masih ada sisa simpanan wajib.',
            'no_rekening_tujuan_wajib.required' => 'No. rekening/e-wallet tujuan wajib diisi karena masih ada sisa simpanan wajib.',
        ]);

        // Alasan_penghapusan dipakai sebagai penanda "menunggu persetujuan Sekretaris"
        // (bukan kolom status terpisah) — begitu terisi, permintaan otomatis masuk antrian D-24.
        $anggota->alasan_penghapusan = $request->alasan_penghapusan;

        if ($saldo['wajib'] > 0) {
            $anggota->nominal_wajib_ditarik = $saldo['wajib'];
            $anggota->metode_penarikan_wajib = $request->metode_penarikan_wajib;
            $anggota->no_rekening_tujuan_wajib = $request->no_rekening_tujuan_wajib;

            // PENTING: baris ini yang sebelumnya HILANG — tanpa insert ke tabel Simpanan,
            // permintaan penarikan wajib ini tidak pernah muncul di antrian Bendahara
            // (tab "Konfirmasi Penarikan"), sehingga saldo wajib tidak akan pernah berkurang
            // dan syarat "Tidak ada saldo tersisa" di panel Sekretaris tidak akan pernah terpenuhi.
            Simpanan::create([
                'id_simpanan' => Simpanan::generateId(),
                'id_anggota' => $anggota->id_anggota,
                'jenis_simpanan' => 'Wajib',
                'jenis_transaksi' => 'Penarikan',
                'metode_penarikan' => $request->metode_penarikan_wajib,
                'no_rekening_tujuan' => $request->no_rekening_tujuan_wajib,
                'jumlah' => $saldo['wajib'],
                'status_transaksi' => 'Menunggu',
            ]);
        }

        $anggota->save();

        $pesan = $saldo['wajib'] > 0
            ? 'Pengajuan penghapusan akun berhasil dikirim beserta permintaan penarikan sisa simpanan wajib Rp ' . number_format($saldo['wajib'], 0, ',', '.') . ', menunggu konfirmasi Bendahara & persetujuan Sekretaris.'
            : 'Pengajuan penghapusan akun berhasil dikirim, menunggu persetujuan Sekretaris.';

        return redirect()->route('profil.ajukan-penghapusan')->with('success', $pesan);
    }
}