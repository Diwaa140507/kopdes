<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\PembayaranCicilan;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CicilanController extends Controller
{
    // Formula bisnis (asumsi, karena belum diatur di dokumen resmi — lihat catatan diskusi):
    const PERSENTASE_DENDA_PER_HARI = 0.1; // 0.1% dari nominal_pinjaman, per hari keterlambatan

    /**
     * Bangun jadwal angsuran lengkap untuk 1 pinjaman aktif, digabung dengan
     * data pembayaran yang sudah tercatat (Terverifikasi / Menunggu Konfirmasi / Ditolak).
     * Dipakai bersama oleh D-13 (Tagihan) dan D-14 (Bayar Cicilan).
     */
    private function buildJadwalAngsuran(Pinjaman $pinjaman)
    {
        $pembayaran = PembayaranCicilan::where('id_pinjaman', $pinjaman->id_pinjaman)
            ->orderBy('no_angsuran')
            ->get()
            ->keyBy('no_angsuran');

        $tanggalMulai = $pinjaman->tanggal_pencairan ? Carbon::parse($pinjaman->tanggal_pencairan) : Carbon::now();

        $jadwal = collect();

        for ($i = 1; $i <= $pinjaman->tenor_bulan; $i++) {
            $jatuhTempo = $tanggalMulai->copy()->addMonths($i);
            $bayar = $pembayaran->get($i);

            if ($bayar && $bayar->status_pembayaran === 'Terverifikasi') {
                $status = 'Lunas';
                $denda = $bayar->jumlah_denda;
            } elseif ($bayar && $bayar->status_pembayaran === 'Menunggu Konfirmasi') {
                $status = 'Menunggu Konfirmasi';
                $denda = $bayar->jumlah_denda;
            } elseif ($jatuhTempo->isPast()) {
                $status = 'Terlambat';
                $hariTerlambat = $jatuhTempo->diffInDays(Carbon::now());
                $denda = round($pinjaman->nominal_pinjaman * self::PERSENTASE_DENDA_PER_HARI / 100 * $hariTerlambat);
            } else {
                $status = 'Belum Bayar';
                $denda = 0;
            }

            $jadwal->push([
                'no_angsuran' => $i,
                'jatuh_tempo' => $jatuhTempo,
                'cicilan' => $pinjaman->cicilan_per_bulan,
                'denda' => $denda,
                'total_bayar' => $pinjaman->cicilan_per_bulan + $denda,
                'status' => $status,
            ]);
        }

        return $jadwal;
    }

    /**
     * Hitung total sudah terbayar (Terverifikasi) untuk 1 pinjaman: jumlah_pembayaran
     * dikurangi jumlah_denda, dijumlahkan lintas semua baris Terverifikasi.
     */
    private function hitungTotalTerbayar(Pinjaman $pinjaman): float
    {
        $terverifikasi = PembayaranCicilan::where('id_pinjaman', $pinjaman->id_pinjaman)
            ->where('status_pembayaran', 'Terverifikasi')
            ->get();

        return $terverifikasi->sum('jumlah_pembayaran') - $terverifikasi->sum('jumlah_denda');
    }

    /**
     * D-13 - Cicilan Anggota (Tab Tagihan)
     */
    public function tagihan()
    {
        $idAnggota = Auth::guard('web')->id();

        $pinjamanAktif = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->orderByDesc('tanggal_pencairan')
            ->first();

        $jadwalAngsuran = collect();
        $tagihanBerjalan = null;
        $sisaHutang = 0;

        if ($pinjamanAktif) {
            $jadwalAngsuran = $this->buildJadwalAngsuran($pinjamanAktif);

            $tagihanBerjalan = $jadwalAngsuran->firstWhere('status', 'Terlambat')
                ?? $jadwalAngsuran->firstWhere('status', 'Belum Bayar');

            $totalTerbayar = $this->hitungTotalTerbayar($pinjamanAktif);

            $sisaHutang = max($pinjamanAktif->total_pengembalian - $totalTerbayar, 0);
        }

        // Ambil riwayat pinjaman yang baru saja lunas juga (kalau ada), supaya anggota
        // tetap bisa lihat riwayat pembayaran walau statusnya sudah pindah dari Aktif.
        $pinjamanUntukRiwayat = $pinjamanAktif ?? Pinjaman::where('id_anggota', $idAnggota)
            ->orderByDesc('tanggal_pencairan')
            ->first();

        $riwayatPembayaran = $pinjamanUntukRiwayat
            ? PembayaranCicilan::where('id_pinjaman', $pinjamanUntukRiwayat->id_pinjaman)
                ->where('status_pembayaran', 'Terverifikasi')
                ->orderBy('no_angsuran')
                ->get()
            : collect();

        return view('anggota.cicilan.tagihan', [
            'pinjamanAktif' => $pinjamanAktif,
            'jadwalAngsuran' => $jadwalAngsuran,
            'tagihanBerjalan' => $tagihanBerjalan,
            'sisaHutang' => $sisaHutang,
            'riwayatPembayaran' => $riwayatPembayaran,
        ]);
    }

    /**
     * D-14 - Form Pembayaran Cicilan (Tab Bayar Cicilan)
     */
    public function bayar()
    {
        $idAnggota = Auth::guard('web')->id();

        $pinjamanAktif = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->orderByDesc('tanggal_pencairan')
            ->first();

        if (! $pinjamanAktif) {
            return redirect()->route('cicilan.tagihan')
                ->with('info', 'Anda tidak memiliki pinjaman aktif untuk dibayar.');
        }

        $jadwalAngsuran = $this->buildJadwalAngsuran($pinjamanAktif);

        $tagihanBerjalan = $jadwalAngsuran->firstWhere('status', 'Terlambat')
            ?? $jadwalAngsuran->firstWhere('status', 'Belum Bayar');

        if (! $tagihanBerjalan) {
            return redirect()->route('cicilan.tagihan')
                ->with('info', 'Semua cicilan sudah lunas atau sedang menunggu konfirmasi.');
        }

        $totalTerbayar = $this->hitungTotalTerbayar($pinjamanAktif);

        $sisaHutangTotal = max($pinjamanAktif->total_pengembalian - $totalTerbayar, 0) + $tagihanBerjalan['denda'];

        return view('anggota.cicilan.bayar', [
            'pinjamanAktif' => $pinjamanAktif,
            'tagihanBerjalan' => $tagihanBerjalan,
            'sisaHutangTotal' => $sisaHutangTotal,
        ]);
    }

    public function bayarStore(Request $request)
    {
        $idAnggota = Auth::guard('web')->id();

        $pinjamanAktif = Pinjaman::where('id_anggota', $idAnggota)
            ->where('status_pinjaman', 'Aktif')
            ->orderByDesc('tanggal_pencairan')
            ->first();

        if (! $pinjamanAktif) {
            return redirect()->route('cicilan.tagihan');
        }

        $request->validate([
            'metode_pembayaran' => ['required', 'in:per_angsuran,pelunasan_sekaligus'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'metode_setoran' => ['required', 'in:QRIS,Tunai'],
            'bukti_transaksi' => [$request->metode_setoran === 'QRIS' ? 'required' : 'nullable', 'file', 'image', 'max:4096'],
        ]);

        $jadwalAngsuran = $this->buildJadwalAngsuran($pinjamanAktif);
        $tagihanBerjalan = $jadwalAngsuran->firstWhere('status', 'Terlambat')
            ?? $jadwalAngsuran->firstWhere('status', 'Belum Bayar');

        if (! $tagihanBerjalan) {
            return redirect()->route('cicilan.tagihan');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('bukti-cicilan', 'public');
        }

        // QRIS terverifikasi otomatis (pembayaran instan via payment gateway),
        // Tunai tetap harus dikonfirmasi manual oleh Bendahara.
        $statusPembayaran = $request->metode_setoran === 'QRIS' ? 'Terverifikasi' : 'Menunggu Konfirmasi';

        // Total yang sudah terverifikasi SEBELUM transaksi ini (jadi acuan awal untuk
        // menghitung sisa_hutang berjalan per baris kalau langsung Terverifikasi/QRIS).
        $totalTerbayarSebelumnya = $this->hitungTotalTerbayar($pinjamanAktif);

        if ($request->metode_pembayaran === 'pelunasan_sekaligus') {
            $this->prosesPelunasanSekaligus(
                $pinjamanAktif,
                $jadwalAngsuran,
                $tagihanBerjalan,
                $idAnggota,
                $request->metode_setoran,
                $buktiPath,
                $statusPembayaran,
                $totalTerbayarSebelumnya
            );
        } else {
            $sisaHutangSetelah = null;
            if ($statusPembayaran === 'Terverifikasi') {
                $totalTerbayarSetelah = $totalTerbayarSebelumnya
                    + $request->jumlah_bayar
                    - $tagihanBerjalan['denda'];
                $sisaHutangSetelah = max($pinjamanAktif->total_pengembalian - $totalTerbayarSetelah, 0);
            }

            PembayaranCicilan::create([
                'id_cicilan' => PembayaranCicilan::generateId(),
                'id_pinjaman' => $pinjamanAktif->id_pinjaman,
                'id_anggota' => $idAnggota,
                'no_angsuran' => $tagihanBerjalan['no_angsuran'],
                'tanggal_pembayaran' => now(),
                'jumlah_pembayaran' => $request->jumlah_bayar,
                'metode_setoran' => $request->metode_setoran,
                'bukti_transaksi' => $buktiPath,
                'status_pembayaran' => $statusPembayaran,
                'jumlah_denda' => $tagihanBerjalan['denda'],
                'sisa_hutang' => $sisaHutangSetelah,
            ]);

            if ($sisaHutangSetelah !== null && $sisaHutangSetelah <= 0) {
                $pinjamanAktif->update(['status_pinjaman' => 'Lunas']);
            }
        }

        $pesanSukses = $statusPembayaran === 'Terverifikasi'
            ? 'Pembayaran cicilan via QRIS berhasil dan sudah terverifikasi.'
            : 'Pembayaran cicilan berhasil dikirim, menunggu konfirmasi Bendahara.';

        return redirect()->route('cicilan.tagihan')
            ->with('success', $pesanSukses);
    }

    /**
     * Untuk metode "Pelunasan Sekaligus": tandai SEMUA angsuran yang tersisa
     * (mulai dari angsuran yang sedang berjalan sampai akhir tenor) sebagai
     * lunas/menunggu konfirmasi, masing-masing sebagai baris tersendiri di
     * pembayaran_cicilan. Bukti transaksi hanya dilampirkan di baris pertama.
     *
     * Kalau metode setoran QRIS (langsung Terverifikasi), sisa_hutang dihitung
     * berjalan turun per baris, dan begitu baris terakhir selesai dibuat & sisa
     * hutang mencapai Rp0, status pinjaman otomatis diubah jadi "Lunas".
     */
    private function prosesPelunasanSekaligus(
        Pinjaman $pinjamanAktif,
        $jadwalAngsuran,
        array $tagihanBerjalan,
        string $idAnggota,
        string $metodeSetoran,
        ?string $buktiPath,
        string $statusPembayaran,
        float $totalTerbayarSebelumnya
    ): void {
        $sisaAngsuran = $jadwalAngsuran->filter(function ($baris) use ($tagihanBerjalan) {
            return $baris['no_angsuran'] >= $tagihanBerjalan['no_angsuran'];
        })->values();

        $totalTerbayarBerjalan = $totalTerbayarSebelumnya;
        $sisaHutangTerakhir = null;

        foreach ($sisaAngsuran as $index => $baris) {
            $sisaHutangBaris = null;

            if ($statusPembayaran === 'Terverifikasi') {
                $totalTerbayarBerjalan += $baris['cicilan'] - $baris['denda'];
                $sisaHutangBaris = max($pinjamanAktif->total_pengembalian - $totalTerbayarBerjalan, 0);
                $sisaHutangTerakhir = $sisaHutangBaris;
            }

            PembayaranCicilan::create([
                'id_cicilan' => PembayaranCicilan::generateId(),
                'id_pinjaman' => $pinjamanAktif->id_pinjaman,
                'id_anggota' => $idAnggota,
                'no_angsuran' => $baris['no_angsuran'],
                'tanggal_pembayaran' => now(),
                'jumlah_pembayaran' => $baris['cicilan'],
                'metode_setoran' => $metodeSetoran,
                // hanya baris pertama yang bawa bukti transaksi
                'bukti_transaksi' => $index === 0 ? $buktiPath : null,
                'status_pembayaran' => $statusPembayaran,
                // denda cuma relevan untuk angsuran yang memang sudah telat
                'jumlah_denda' => $baris['denda'],
                'sisa_hutang' => $sisaHutangBaris,
            ]);
        }

        if ($sisaHutangTerakhir !== null && $sisaHutangTerakhir <= 0) {
            $pinjamanAktif->update(['status_pinjaman' => 'Lunas']);
        }
    }
}