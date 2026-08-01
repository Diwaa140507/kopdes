<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\LaporanKeuangan;
use App\Models\PembayaranCicilan;
use App\Models\Pengurus;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    const ROUTE_JENIS = [
        'Anggota' => 'ketua.laporan.anggota',
        'Simpanan' => 'ketua.laporan.simpanan',
        'Pinjaman' => 'ketua.laporan.pinjaman',
        'Cicilan' => 'ketua.laporan.cicilan',
        'Pengurus' => 'ketua.laporan.pengurus',
        'Keseluruhan' => 'ketua.laporan.keseluruhan',
    ];

    public function pilih()
    {
        $riwayat = LaporanKeuangan::orderByDesc('created_at')->limit(10)->get();

        return view('ketua.laporan.pilih', [
            'riwayat' => $riwayat,
            'bulanSekarang' => now()->month,
            'tahunSekarang' => now()->year,
        ]);
    }

    public function tampilkan(Request $request)
    {
        $request->validate([
            'jenis_laporan' => ['required', 'in:Anggota,Simpanan,Pinjaman,Cicilan,Pengurus,Keseluruhan'],
            'periode_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'periode_tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
        ], [
            'jenis_laporan.required' => 'Harap isi semua kolom — Jenis Laporan dan Periode wajib dipilih.',
        ]);

        $this->catatRiwayat($request->jenis_laporan, (int) $request->periode_bulan, (int) $request->periode_tahun);

        $routeName = self::ROUTE_JENIS[$request->jenis_laporan];

        return redirect()->route($routeName, [
            'bulan' => $request->periode_bulan,
            'tahun' => $request->periode_tahun,
        ]);
    }

    public function lihat(string $id)
    {
        $laporan = LaporanKeuangan::findOrFail($id);

        $routeName = self::ROUTE_JENIS[$laporan->jenis_laporan];

        return redirect()->route($routeName, [
            'bulan' => $laporan->periode_bulan,
            'tahun' => $laporan->periode_tahun,
        ]);
    }

    public function pinjaman(Request $request)
    {
        [$bulan, $tahun] = $this->periode($request);

        $daftarPinjaman = Pinjaman::with('anggota')
            ->whereMonth('tanggal_pencairan', $bulan)
            ->whereYear('tanggal_pencairan', $tahun)
            ->orderBy('id_anggota')
            ->get()
            ->map(function ($pinjaman) {
                $pinjaman->sisa_hutang_computed = $this->hitungSisaHutang($pinjaman);
                return $pinjaman;
            });

        $totalDicairkan = $daftarPinjaman->sum('nominal_pinjaman');
        $pinjamanAktif = $daftarPinjaman->where('status_pinjaman', 'Aktif')->count();
        $totalSisaHutang = $daftarPinjaman->sum('sisa_hutang_computed');

        return view('ketua.laporan.pinjaman', [
            'daftarPinjaman' => $daftarPinjaman,
            'totalDicairkan' => $totalDicairkan,
            'pinjamanAktif' => $pinjamanAktif,
            'totalSisaHutang' => $totalSisaHutang,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periodeLabel' => $this->periodeLabel($bulan, $tahun),
        ]);
    }

    public function simpanan(Request $request)
    {
        [$bulan, $tahun] = $this->periode($request);

        $transaksi = Simpanan::with('anggota')
            ->where('status_transaksi', 'Berhasil')
            ->whereMonth('tanggal_transaksi', $bulan)
            ->whereYear('tanggal_transaksi', $tahun)
            ->orderBy('tanggal_transaksi')
            ->get();

        $totalSimpananWajib = $transaksi->where('jenis_transaksi', 'Setoran')->where('jenis_simpanan', 'Wajib')->sum('jumlah');
        $totalSimpananSukarela = $transaksi->where('jenis_transaksi', 'Setoran')->where('jenis_simpanan', 'Sukarela')->sum('jumlah');
        $totalPenarikan = $transaksi->where('jenis_transaksi', 'Penarikan')->sum('jumlah');

        return view('ketua.laporan.simpanan', [
            'transaksi' => $transaksi,
            'totalSimpananWajib' => $totalSimpananWajib,
            'totalSimpananSukarela' => $totalSimpananSukarela,
            'totalPenarikan' => $totalPenarikan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periodeLabel' => $this->periodeLabel($bulan, $tahun),
        ]);
    }

    public function cicilan(Request $request)
    {
        [$bulan, $tahun] = $this->periode($request);

        $cicilan = PembayaranCicilan::with('anggota')
            ->where('status_pembayaran', 'Terverifikasi')
            ->whereMonth('tanggal_pembayaran', $bulan)
            ->whereYear('tanggal_pembayaran', $tahun)
            ->orderBy('tanggal_pembayaran')
            ->get();

        $totalCicilanMasuk = $cicilan->sum('jumlah_pembayaran') - $cicilan->sum('jumlah_denda');
        $totalDendaMasuk = $cicilan->sum('jumlah_denda');
        $cicilanTerlambat = $cicilan->where('jumlah_denda', '>', 0)->count();

        return view('ketua.laporan.cicilan', [
            'cicilan' => $cicilan,
            'totalCicilanMasuk' => $totalCicilanMasuk,
            'totalDendaMasuk' => $totalDendaMasuk,
            'cicilanTerlambat' => $cicilanTerlambat,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periodeLabel' => $this->periodeLabel($bulan, $tahun),
        ]);
    }

    public function anggota(Request $request)
    {
        [$bulan, $tahun] = $this->periode($request);

        $totalAnggota = Anggota::count();
        $anggotaAktif = Anggota::where('status_keanggotaan', 'Terverifikasi')->count();
        $anggotaBaru = Anggota::whereMonth('tanggal_daftar', $bulan)
            ->whereYear('tanggal_daftar', $tahun)
            ->count();

        $daftarAnggota = Anggota::orderBy('id_anggota')->get()->map(function ($a) {
            $setoran = Simpanan::where('id_anggota', $a->id_anggota)
                ->where('jenis_transaksi', 'Setoran')
                ->where('status_transaksi', 'Berhasil')
                ->sum('jumlah');
            $penarikan = Simpanan::where('id_anggota', $a->id_anggota)
                ->where('jenis_transaksi', 'Penarikan')
                ->where('status_transaksi', 'Berhasil')
                ->sum('jumlah');

            $a->total_simpanan_computed = $setoran - $penarikan;
            return $a;
        });

        return view('ketua.laporan.anggota', [
            'daftarAnggota' => $daftarAnggota,
            'totalAnggota' => $totalAnggota,
            'anggotaAktif' => $anggotaAktif,
            'anggotaBaru' => $anggotaBaru,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periodeLabel' => $this->periodeLabel($bulan, $tahun),
        ]);
    }

    /**
     * D-36 - Laporan Pengurus
     * Kolom "Tgl Menjabat" & histori riwayat pakai kolom fisik
     * tanggal_diangkat / tanggal_diberhentikan (bukan lagi memanfaatkan
     * created_at/updated_at bawaan Laravel).
     */
    public function pengurus(Request $request)
    {
        [$bulan, $tahun] = $this->periode($request);

        $totalPengurusAktif = Pengurus::where('status', 'Menjabat')->count();

        $daftarPengurus = Pengurus::where('status', 'Menjabat')
            ->orderBy('id_pengurus')
            ->get();

        $diangkatPeriode = Pengurus::whereMonth('tanggal_diangkat', $bulan)
            ->whereYear('tanggal_diangkat', $tahun)
            ->get()
            ->map(function ($p) {
                return [
                    'tanggal' => $p->tanggal_diangkat,
                    'teks' => "{$p->nama_pengurus} diangkat sebagai {$p->jabatan} pada " . $p->tanggal_diangkat->format('d/m/Y'),
                ];
            });

        $diberhentikanPeriode = Pengurus::where('status', 'Diberhentikan')
            ->whereNotNull('tanggal_diberhentikan')
            ->whereMonth('tanggal_diberhentikan', $bulan)
            ->whereYear('tanggal_diberhentikan', $tahun)
            ->get()
            ->map(function ($p) {
                return [
                    'tanggal' => $p->tanggal_diberhentikan,
                    'teks' => "{$p->nama_pengurus} diberhentikan dari jabatan {$p->jabatan} pada " . $p->tanggal_diberhentikan->format('d/m/Y'),
                ];
            });

        $riwayatPerubahan = $diangkatPeriode->concat($diberhentikanPeriode)
            ->sortByDesc('tanggal')
            ->pluck('teks')
            ->values();

        $pergantianBulanIni = $riwayatPerubahan->count();

        return view('ketua.laporan.pengurus', [
            'daftarPengurus' => $daftarPengurus,
            'riwayatPerubahan' => $riwayatPerubahan,
            'totalPengurusAktif' => $totalPengurusAktif,
            'pergantianBulanIni' => $pergantianBulanIni,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periodeLabel' => $this->periodeLabel($bulan, $tahun),
        ]);
    }

    /**
     * D-38 - Laporan Keseluruhan
     */
    public function keseluruhan(Request $request)
    {
        [$bulan, $tahun] = $this->periode($request);

        $simpananMasuk = Simpanan::where('jenis_transaksi', 'Setoran')
            ->where('status_transaksi', 'Berhasil')
            ->whereMonth('tanggal_transaksi', $bulan)
            ->whereYear('tanggal_transaksi', $tahun)
            ->sum('jumlah');

        $pinjamanDicairkan = Pinjaman::whereMonth('tanggal_pencairan', $bulan)
            ->whereYear('tanggal_pencairan', $tahun)
            ->sum('nominal_pinjaman');

        $cicilanPeriode = PembayaranCicilan::where('status_pembayaran', 'Terverifikasi')
            ->whereMonth('tanggal_pembayaran', $bulan)
            ->whereYear('tanggal_pembayaran', $tahun)
            ->get();

        $cicilanMasuk = $cicilanPeriode->sum('jumlah_pembayaran') - $cicilanPeriode->sum('jumlah_denda');
        $dendaMasuk = $cicilanPeriode->sum('jumlah_denda');

        $anggotaAktif = Anggota::where('status_keanggotaan', 'Terverifikasi')->count();

        $totalSetoranSemua = Simpanan::where('jenis_transaksi', 'Setoran')->where('status_transaksi', 'Berhasil')->sum('jumlah');
        $totalPenarikanSemua = Simpanan::where('jenis_transaksi', 'Penarikan')->where('status_transaksi', 'Berhasil')->sum('jumlah');
        $totalTerkumpul = $totalSetoranSemua - $totalPenarikanSemua;

        $pinjamanAktifCount = Pinjaman::where('status_pinjaman', 'Aktif')->count();
        $pinjamanDicairkanSemua = Pinjaman::sum('nominal_pinjaman');

        $cicilanTerverifikasiSemua = PembayaranCicilan::where('status_pembayaran', 'Terverifikasi')->get();
        $totalCicilanTerverifikasi = $cicilanTerverifikasiSemua->count();
        $cicilanTepatWaktu = $cicilanTerverifikasiSemua->where('jumlah_denda', 0)->count();
        $ketepatanBayar = $totalCicilanTerverifikasi > 0
            ? round(($cicilanTepatWaktu / $totalCicilanTerverifikasi) * 100)
            : 0;

        $totalPengurusAktif = Pengurus::where('status', 'Menjabat')->count();

        $totalCicilanMasukSemua = $cicilanTerverifikasiSemua->sum('jumlah_pembayaran') - $cicilanTerverifikasiSemua->sum('jumlah_denda');
        $totalDendaMasukSemua = $cicilanTerverifikasiSemua->sum('jumlah_denda');

        $saldoKasKoperasi = config('koperasi.modal_awal')
            + $totalSetoranSemua
            + $totalCicilanMasukSemua
            + $totalDendaMasukSemua
            - $pinjamanDicairkanSemua
            - $totalPenarikanSemua;

        return view('ketua.laporan.keseluruhan', [
            'simpananMasuk' => $simpananMasuk,
            'pinjamanDicairkan' => $pinjamanDicairkan,
            'cicilanMasuk' => $cicilanMasuk,
            'dendaMasuk' => $dendaMasuk,
            'saldoKasKoperasi' => $saldoKasKoperasi,
            'anggotaAktif' => $anggotaAktif,
            'totalTerkumpul' => $totalTerkumpul,
            'pinjamanAktifCount' => $pinjamanAktifCount,
            'ketepatanBayar' => $ketepatanBayar,
            'totalPengurusAktif' => $totalPengurusAktif,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periodeLabel' => $this->periodeLabel($bulan, $tahun),
        ]);
    }

    private function hitungSisaHutang(Pinjaman $pinjaman): float
    {
        if ($pinjaman->status_pinjaman === 'Lunas') {
            return 0;
        }

        if (! in_array($pinjaman->status_pinjaman, ['Aktif'])) {
            return 0;
        }

        $cicilanTerbayar = PembayaranCicilan::where('id_pinjaman', $pinjaman->id_pinjaman)
            ->where('status_pembayaran', 'Terverifikasi')
            ->get();

        $totalTerbayar = $cicilanTerbayar->sum('jumlah_pembayaran') - $cicilanTerbayar->sum('jumlah_denda');

        return max($pinjaman->total_pengembalian - $totalTerbayar, 0);
    }

    private function periode(Request $request): array
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        return [$bulan, $tahun];
    }

    private function periodeLabel(int $bulan, int $tahun): string
    {
        return \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
    }

    private function catatRiwayat(string $jenis, int $bulan, int $tahun): void
    {
        $sudahAda = LaporanKeuangan::where('jenis_laporan', $jenis)
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->exists();

        if ($sudahAda) {
            return;
        }

        LaporanKeuangan::create([
            'id_laporan' => LaporanKeuangan::generateId($jenis),
            'jenis_laporan' => $jenis,
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'tanggal_dibuat' => now()->toDateString(),
            'id_pengurus_pembuat' => Auth::guard('pengurus')->id(),
        ]);
    }
}