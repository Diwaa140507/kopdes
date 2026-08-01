<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpananController extends Controller
{
    /**
     * D-26 - Tab "Konfirmasi Setoran"
     */
    public function setoran(Request $request)
    {
        $keyword = $request->input('cari');

        $antrian = Simpanan::with('anggota')
            ->where('jenis_transaksi', 'Setoran')
            ->where('status_transaksi', 'Menunggu')
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
            $selected = Simpanan::with('anggota')
                ->where('jenis_transaksi', 'Setoran')
                ->where('status_transaksi', 'Menunggu')
                ->where('id_simpanan', $request->detail)
                ->first();
        }

        return view('bendahara.simpanan.setoran', [
            'antrian' => $antrian,
            'selected' => $selected,
            'cari' => $keyword,
        ]);
    }

    /**
     * Aksi "Konfirmasi" di D-26 — saldo anggota bertambah otomatis sebesar Jumlah Setoran.
     */
    public function setoranKonfirmasi(Request $request, string $id)
    {
        $simpanan = Simpanan::where('jenis_transaksi', 'Setoran')
            ->where('status_transaksi', 'Menunggu')
            ->where('id_simpanan', $id)
            ->firstOrFail();

        $saldo = Simpanan::currentSaldo($simpanan->id_anggota);

        $saldoWajibBaru = $saldo['wajib'] + ($simpanan->jenis_simpanan === 'Wajib' ? $simpanan->jumlah : 0);
        $saldoSukarelaBaru = $saldo['sukarela'] + ($simpanan->jenis_simpanan === 'Sukarela' ? $simpanan->jumlah : 0);

        $simpanan->update([
            'status_transaksi' => 'Berhasil',
            'tanggal_transaksi' => now()->toDateString(),
            'saldo_simpanan_wajib' => $saldoWajibBaru,
            'saldo_simpanan_sukarela' => $saldoSukarelaBaru,
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        return redirect()
            ->route('bendahara.simpanan.setoran')
            ->with('success', 'Setoran ' . $simpanan->id_simpanan . ' berhasil dikonfirmasi, saldo anggota sudah diperbarui.');
    }

    /**
     * Aksi "Tolak" di D-26 — catatan penolakan wajib diisi, saldo tidak berubah.
     */
    public function setoranTolak(Request $request, string $id)
    {
        $request->validate([
            'catatan_penolakan' => ['required', 'string', 'max:255'],
        ], [
            'catatan_penolakan.required' => 'Harap isi catatan penolakan sebelum menolak transaksi ini.',
        ]);

        $simpanan = Simpanan::where('jenis_transaksi', 'Setoran')
            ->where('status_transaksi', 'Menunggu')
            ->where('id_simpanan', $id)
            ->firstOrFail();

        $simpanan->update([
            'status_transaksi' => 'Ditolak',
            'catatan_penolakan' => $request->catatan_penolakan,
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        return redirect()
            ->route('bendahara.simpanan.setoran')
            ->with('success', 'Setoran ' . $simpanan->id_simpanan . ' ditolak.');
    }

    /**
     * D-27 - Tab "Konfirmasi Penarikan"
     *
     * Menampung 2 jenis baris sekaligus (dibedakan lewat kolom jenis_simpanan):
     * - jenis_simpanan = 'Sukarela' -> penarikan sukarela biasa (anggota ajukan sendiri)
     * - jenis_simpanan = 'Wajib'    -> penarikan otomatis dari proses Penghapusan Akun
     *   (satu-satunya kondisi sistem membuat baris Penarikan+Wajib adalah alur ini,
     *   karena Simpanan Wajib/Pokok tidak bisa ditarik lewat form penarikan normal).
     * Tidak perlu tab terpisah — cukup dibedakan via badge "Jenis" di tabel & detail.
     */
    public function penarikan(Request $request)
    {
        $keyword = $request->input('cari');

        $antrian = Simpanan::with('anggota')
            ->where('jenis_transaksi', 'Penarikan')
            ->where('status_transaksi', 'Menunggu')
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

        // PENTING: kolom saldo_simpanan_wajib/sukarela di baris Simpanan itu adalah
        // running-balance SETELAH transaksi dikonfirmasi — untuk baris yang masih
        // "Menunggu" kolom itu belum pernah diisi (selalu 0). Supaya "Saldo Terkait"
        // di tabel menunjukkan saldo yang BENAR-BENAR berlaku saat ini, hitung live
        // pakai Simpanan::currentSaldo(), bukan baca kolom itu.
        $antrian->each(function ($row) {
            $saldoLive = Simpanan::currentSaldo($row->id_anggota);
            $row->saldo_terkait_live = $row->jenis_simpanan === 'Wajib'
                ? $saldoLive['wajib']
                : $saldoLive['sukarela'];
        });

        $selected = null;

        if ($request->filled('detail')) {
            $selected = Simpanan::with('anggota')
                ->where('jenis_transaksi', 'Penarikan')
                ->where('status_transaksi', 'Menunggu')
                ->where('id_simpanan', $request->detail)
                ->first();

            if ($selected) {
                $saldoLive = Simpanan::currentSaldo($selected->id_anggota);
                $selected->saldo_terkait_live = $selected->jenis_simpanan === 'Wajib'
                    ? $saldoLive['wajib']
                    : $saldoLive['sukarela'];
            }
        }

        return view('bendahara.simpanan.penarikan', [
            'antrian' => $antrian,
            'selected' => $selected,
            'cari' => $keyword,
        ]);
    }

    /**
     * Aksi "Konfirmasi" di D-27 — saldo anggota berkurang otomatis sebesar Jumlah Diajukan,
     * dari kelompok saldo yang sesuai (Wajib atau Sukarela tergantung jenis_simpanan baris ini).
     */
    public function penarikanKonfirmasi(Request $request, string $id)
    {
        $simpanan = Simpanan::where('jenis_transaksi', 'Penarikan')
            ->where('status_transaksi', 'Menunggu')
            ->where('id_simpanan', $id)
            ->firstOrFail();

        $saldo = Simpanan::currentSaldo($simpanan->id_anggota);
        $isWajib = $simpanan->jenis_simpanan === 'Wajib';

        $saldoTerkait = $isWajib ? $saldo['wajib'] : $saldo['sukarela'];

        if ($simpanan->jumlah > $saldoTerkait) {
            $labelJenis = $isWajib ? 'Wajib' : 'Sukarela';

            return redirect()
                ->route('bendahara.simpanan.penarikan', ['detail' => $id])
                ->with('error', 'Saldo ' . $labelJenis . ' anggota saat ini (Rp ' . number_format($saldoTerkait, 0, ',', '.') . ') sudah tidak mencukupi untuk penarikan ini.');
        }

        $saldoWajibBaru = $isWajib ? $saldo['wajib'] - $simpanan->jumlah : $saldo['wajib'];
        $saldoSukarelaBaru = $isWajib ? $saldo['sukarela'] : $saldo['sukarela'] - $simpanan->jumlah;

        $simpanan->update([
            'status_transaksi' => 'Berhasil',
            'tanggal_transaksi' => now()->toDateString(),
            'saldo_simpanan_wajib' => $saldoWajibBaru,
            'saldo_simpanan_sukarela' => $saldoSukarelaBaru,
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        $pesanSukses = $isWajib
            ? 'Penarikan ' . $simpanan->id_simpanan . ' (Simpanan Wajib — Penghapusan Akun) berhasil dikonfirmasi, saldo anggota sudah diperbarui.'
            : 'Penarikan ' . $simpanan->id_simpanan . ' berhasil dikonfirmasi, saldo anggota sudah diperbarui.';

        return redirect()
            ->route('bendahara.simpanan.penarikan')
            ->with('success', $pesanSukses);
    }

    /**
     * Aksi "Tolak" di D-27 — catatan penolakan wajib diisi, saldo tidak berubah.
     */
    public function penarikanTolak(Request $request, string $id)
    {
        $request->validate([
            'catatan_penolakan' => ['required', 'string', 'max:255'],
        ], [
            'catatan_penolakan.required' => 'Harap isi catatan penolakan sebelum menolak transaksi ini.',
        ]);

        $simpanan = Simpanan::where('jenis_transaksi', 'Penarikan')
            ->where('status_transaksi', 'Menunggu')
            ->where('id_simpanan', $id)
            ->firstOrFail();

        $simpanan->update([
            'status_transaksi' => 'Ditolak',
            'catatan_penolakan' => $request->catatan_penolakan,
            'id_pengurus_pencatat' => Auth::guard('pengurus')->id(),
        ]);

        return redirect()
            ->route('bendahara.simpanan.penarikan')
            ->with('success', 'Penarikan ' . $simpanan->id_simpanan . ' ditolak.');
    }

    /**
     * D-?? - Tab "Riwayat" — semua transaksi simpanan yang sudah diproses
     * (Berhasil / Ditolak), termasuk bukti QRIS setoran.
     */
    public function riwayat(Request $request)
    {
        $keyword = $request->input('cari');
        $filter = $request->input('filter');

        $riwayat = Simpanan::with('anggota')
            ->whereIn('status_transaksi', ['Berhasil', 'Ditolak'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('id_anggota', 'like', "%{$keyword}%")
                        ->orWhereHas('anggota', function ($qq) use ($keyword) {
                            $qq->where('nama_lengkap', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($filter && $filter !== 'Semua', function ($query) use ($filter) {
                $query->where('jenis_transaksi', $filter);
            })
            ->orderByDesc('created_at')
            ->get();

        $selected = null;

        if ($request->filled('detail')) {
            $selected = $riwayat->firstWhere('id_simpanan', $request->detail);
        }

        return view('bendahara.simpanan.riwayat', [
            'riwayat' => $riwayat,
            'selected' => $selected,
            'cari' => $keyword,
            'filter' => $filter,
        ]);
    }
}