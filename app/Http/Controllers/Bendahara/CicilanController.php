<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\PembayaranCicilan;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CicilanController extends Controller
{
    /**
     * D-31 - Cicilan Bendahara (Konfirmasi Pembayaran Cicilan)
     */
    public function index(Request $request)
    {
        $keyword = $request->input('cari');

        $antrian = PembayaranCicilan::with(['anggota', 'pinjaman'])
            ->where('status_pembayaran', 'Menunggu Konfirmasi')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('id_anggota', 'like', "%{$keyword}%")
                        ->orWhereHas('anggota', function ($qq) use ($keyword) {
                            $qq->where('nama_lengkap', 'like', "%{$keyword}%");
                        });
                });
            })
            ->orderBy('created_at')
            ->get();

        $selected = null;

        if ($request->filled('detail')) {
            $selected = PembayaranCicilan::with(['anggota', 'pinjaman'])
                ->where('status_pembayaran', 'Menunggu Konfirmasi')
                ->where('id_cicilan', $request->detail)
                ->first();
        }

        return view('bendahara.cicilan.index', [
            'antrian' => $antrian,
            'selected' => $selected,
            'cari' => $keyword,
        ]);
    }

    /**
     * Riwayat Cicilan (read-only, lintas status) — pola sama dengan
     * Bendahara\PinjamanController@riwayat.
     */
    public function riwayat(Request $request)
    {
        $keyword = $request->input('cari');
        $status = $request->input('status');

        $daftar = PembayaranCicilan::with(['anggota', 'pinjaman'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('id_anggota', 'like', "%{$keyword}%")
                        ->orWhereHas('anggota', function ($qq) use ($keyword) {
                            $qq->where('nama_lengkap', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($status && $status !== 'Semua', function ($query) use ($status) {
                $query->where('status_pembayaran', $status);
            })
            ->orderByDesc('created_at')
            ->get();

        $selected = null;

        if ($request->filled('detail')) {
            $selected = $daftar->firstWhere('id_cicilan', $request->detail);
        }

        return view('bendahara.cicilan.riwayat', [
            'daftar' => $daftar,
            'selected' => $selected,
            'cari' => $keyword,
            'status' => $status ?? 'Semua',
        ]);
    }

    /**
     * Aksi "Konfirmasi" — sisa hutang anggota diperbarui otomatis. Kalau ini angsuran
     * terakhir dan sisa hutang jadi Rp0, status pinjaman otomatis berubah jadi "Lunas".
     */
    public function konfirmasi(Request $request, string $id)
    {
        $cicilan = PembayaranCicilan::where('status_pembayaran', 'Menunggu Konfirmasi')
            ->where('id_cicilan', $id)
            ->firstOrFail();

        $pinjaman = Pinjaman::where('id_pinjaman', $cicilan->id_pinjaman)->firstOrFail();

        // Total sudah terbayar (verified) SEBELUM cicilan ini + cicilan ini sendiri
        $totalTerbayarSebelumnya = PembayaranCicilan::where('id_pinjaman', $pinjaman->id_pinjaman)
            ->where('status_pembayaran', 'Terverifikasi')
            ->get();

        $totalTerbayar = $totalTerbayarSebelumnya->sum('jumlah_pembayaran')
            - $totalTerbayarSebelumnya->sum('jumlah_denda')
            + $cicilan->jumlah_pembayaran
            - $cicilan->jumlah_denda;

        $sisaHutang = max($pinjaman->total_pengembalian - $totalTerbayar, 0);

        $cicilan->update([
            'status_pembayaran' => 'Terverifikasi',
            'sisa_hutang' => $sisaHutang,
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        if ($sisaHutang <= 0) {
            $pinjaman->update([
                'status_pinjaman' => 'Lunas',
            ]);
        }

        return redirect()
            ->route('bendahara.cicilan.index')
            ->with('success', 'Pembayaran ' . $cicilan->id_cicilan . ' berhasil dikonfirmasi, sisa hutang anggota sudah diperbarui.'
                . ($sisaHutang <= 0 ? ' Pinjaman ' . $pinjaman->id_pinjaman . ' kini berstatus Lunas.' : ''));
    }

    /**
     * Aksi "Tolak" — catatan penolakan wajib diisi, status kembali "Ditolak"
     * (denda keterlambatan tetap dihitung dari jatuh tempo asli jika anggota mengajukan ulang).
     */
    public function tolak(Request $request, string $id)
    {
        $request->validate([
            'catatan_penolakan' => ['required', 'string', 'max:255'],
        ], [
            'catatan_penolakan.required' => 'Harap isi semua kolom — Catatan Penolakan wajib diisi kalau menolak pembayaran ini.',
        ]);

        $cicilan = PembayaranCicilan::where('status_pembayaran', 'Menunggu Konfirmasi')
            ->where('id_cicilan', $id)
            ->firstOrFail();

        $cicilan->update([
            'status_pembayaran' => 'Ditolak',
            'catatan_penolakan' => $request->catatan_penolakan,
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        return redirect()
            ->route('bendahara.cicilan.index')
            ->with('success', 'Pembayaran ' . $cicilan->id_cicilan . ' ditolak.');
    }
}