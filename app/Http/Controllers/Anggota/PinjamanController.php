<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\PembayaranCicilan;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PinjamanController extends Controller
{
    // Formula bisnis (asumsi, karena belum diatur di dokumen resmi - lihat catatan diskusi):
    const KELIPATAN_BATAS_MAKSIMAL = 3;   // batas maksimal pinjaman = 3x total saldo simpanan
    const PERSENTASE_JASA = 1.5;          // flat 1.5% untuk semua pinjaman
    const TOLERANSI_TERLAMBAT = 2;        // maksimal 2x terlambat bayar sebelum dianggap "Tidak Layak"
    const TENOR_MAKSIMAL = 12;            // batas maksimal tenor pinjaman (bulan)

    /**
     * D-10 - Cek Kelayakan Pinjaman
     */
    public function cekKelayakan(Request $request)
    {
        $idAnggota = Auth::guard('web')->id();
        $hasil = session('kelayakan_pinjaman');

        return view('anggota.pinjaman.cek-kelayakan', [
            'hasil' => $hasil,
        ]);
    }

    public function cekKelayakanStore(Request $request)
    {
        $idAnggota = Auth::guard('web')->id();

        $request->validate([
            'tujuan_pinjaman' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'tenor_bulan' => ['required', 'integer', 'min:1', 'max:' . self::TENOR_MAKSIMAL],
        ]);

        $saldo = Simpanan::currentSaldo($idAnggota);
        $totalSimpanan = $saldo['wajib'] + $saldo['sukarela'];
        $batasMaksimal = $totalSimpanan * self::KELIPATAN_BATAS_MAKSIMAL;

        $pinjamanAktif = Pinjaman::where('id_anggota', $idAnggota)->where('status_pinjaman', 'Aktif')->exists();

        // Parameter 1: Tunggakan Cicilan
        $adaTunggakan = false;
        $pinjamanBerjalan = Pinjaman::where('id_anggota', $idAnggota)->where('status_pinjaman', 'Aktif')->first();
        if ($pinjamanBerjalan && $pinjamanBerjalan->jadwal_jatuh_tempo) {
            $angsuranLunas = PembayaranCicilan::where('id_pinjaman', $pinjamanBerjalan->id_pinjaman)
                ->where('status_pembayaran', 'Terverifikasi')->count();
            $jatuhTempoBerikutnya = \Carbon\Carbon::parse($pinjamanBerjalan->jadwal_jatuh_tempo)->addMonths($angsuranLunas);
            $adaTunggakan = $jatuhTempoBerikutnya->isPast();
        }

        // Parameter 2: Rasio Simpanan (nominal yang diajukan vs batas maksimal)
        $rasioPersen = $batasMaksimal > 0 ? round(($request->nominal / $batasMaksimal) * 100) : 100;
        $rasioAman = $request->nominal <= $batasMaksimal;

        // Parameter 3: Riwayat Pembayaran (hitung berapa kali telat dari semua pinjaman lunas sebelumnya)
        $jumlahTerlambat = PembayaranCicilan::where('id_anggota', $idAnggota)
            ->where('status_pembayaran', 'Terverifikasi')
            ->where('jumlah_denda', '>', 0)
            ->count();
        $riwayatAman = $jumlahTerlambat <= self::TOLERANSI_TERLAMBAT;

        $layak = ! $pinjamanAktif && ! $adaTunggakan && $rasioAman && $riwayatAman;

        $hasil = [
            'layak' => $layak,
            'punya_pinjaman_aktif' => $pinjamanAktif,
            'ada_tunggakan' => $adaTunggakan,
            'rasio_persen' => $rasioPersen,
            'rasio_aman' => $rasioAman,
            'jumlah_terlambat' => $jumlahTerlambat,
            'riwayat_aman' => $riwayatAman,
            'batas_maksimal' => $batasMaksimal,
            'tujuan_pinjaman' => $request->tujuan_pinjaman,
            'nominal' => $request->nominal,
            'tenor_bulan' => $request->tenor_bulan,
        ];

        session(['kelayakan_pinjaman' => $hasil]);

        return redirect()->route('pinjaman.cek-kelayakan');
    }

    /**
     * D-11 - Form Ajukan Pinjaman
     */
    public function ajukan()
    {
        $hasil = session('kelayakan_pinjaman');

        if (! $hasil || ! $hasil['layak']) {
            return redirect()->route('pinjaman.cek-kelayakan')
                ->with('info', 'Silakan cek kelayakan pinjaman terlebih dahulu.');
        }

        return view('anggota.pinjaman.ajukan', [
            'hasil' => $hasil,
            'persentaseJasa' => self::PERSENTASE_JASA,
        ]);
    }

    public function ajukanStore(Request $request)
    {
        $idAnggota = Auth::guard('web')->id();
        $hasil = session('kelayakan_pinjaman');

        if (! $hasil || ! $hasil['layak']) {
            return redirect()->route('pinjaman.cek-kelayakan');
        }

        $request->validate([
            'tujuan_pinjaman' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:1', 'max:' . $hasil['batas_maksimal']],
            'tenor_bulan' => ['required', 'integer', 'min:1', 'max:' . self::TENOR_MAKSIMAL],
            'rekening_tujuan' => ['required', 'string', 'max:50'],
        ]);

        $jumlahJasa = round($request->nominal * self::PERSENTASE_JASA / 100);
        $totalPengembalian = $request->nominal + $jumlahJasa;
        $cicilanPerBulan = round($totalPengembalian / $request->tenor_bulan);

        Pinjaman::create([
            'id_pinjaman' => Pinjaman::generateId(),
            'id_anggota' => $idAnggota,
            'rekening_tujuan' => $request->rekening_tujuan,
            'tujuan_pinjaman' => $request->tujuan_pinjaman,
            'nominal_pinjaman' => $request->nominal,
            'persentase_jasa' => self::PERSENTASE_JASA,
            'jumlah_jasa' => $jumlahJasa,
            'total_pengembalian' => $totalPengembalian,
            'cicilan_per_bulan' => $cicilanPerBulan,
            'tenor_bulan' => $request->tenor_bulan,
            'status_pinjaman' => 'Menunggu Persetujuan',
        ]);

        session()->forget('kelayakan_pinjaman');

        return redirect()->route('pinjaman.detail')
            ->with('success', 'Pengajuan pinjaman berhasil dikirim, menunggu persetujuan Bendahara.');
    }

    /**
     * D-12 - Detail Pinjaman Aktif
     */
    public function detail()
    {
        $idAnggota = Auth::guard('web')->id();

        $pinjamanAktif = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->orderByDesc('tanggal_pencairan')
            ->first();

        $riwayatCicilan = collect();
        $sisaHutang = 0;
        $angsuranLunas = 0;
        $jatuhTempoBerikutnya = null;

        if ($pinjamanAktif) {
            $riwayatCicilan = PembayaranCicilan::where('id_pinjaman', $pinjamanAktif->id_pinjaman)
                ->where('status_pembayaran', 'Terverifikasi')
                ->orderBy('no_angsuran')
                ->get();

            $totalTerbayar = $riwayatCicilan->sum('jumlah_pembayaran') - $riwayatCicilan->sum('jumlah_denda');
            $sisaHutang = max($pinjamanAktif->total_pengembalian - $totalTerbayar, 0);
            $angsuranLunas = $riwayatCicilan->count();

            if ($pinjamanAktif->jadwal_jatuh_tempo && $angsuranLunas < $pinjamanAktif->tenor_bulan) {
                $jatuhTempoBerikutnya = \Carbon\Carbon::parse($pinjamanAktif->jadwal_jatuh_tempo)->addMonths($angsuranLunas);
            }
        }

        return view('anggota.pinjaman.detail', [
            'pinjamanAktif' => $pinjamanAktif,
            'riwayatCicilan' => $riwayatCicilan,
            'sisaHutang' => $sisaHutang,
            'jatuhTempoBerikutnya' => $jatuhTempoBerikutnya,
        ]);
    }
}