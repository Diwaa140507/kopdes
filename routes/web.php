<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\PengurusLoginController;
use App\Http\Controllers\Auth\RegisteredAnggotaController;
use App\Http\Controllers\Auth\PermintaanResetPasswordController;
use App\Http\Controllers\Auth\GantiPasswordController;
use App\Http\Controllers\Anggota\SimpananController;
use App\Http\Controllers\Anggota\PinjamanController;
use App\Http\Controllers\Anggota\CicilanController;
use App\Http\Controllers\Anggota\ProfilController;
use App\Http\Controllers\Sekretaris\VerifikasiPendaftaranController;
use App\Http\Controllers\Sekretaris\ResetKataSandiController;
use App\Http\Controllers\Sekretaris\RiwayatPerubahanController;
use App\Http\Controllers\Sekretaris\PenghapusanAnggotaController;
use App\Http\Controllers\Bendahara\SimpananController as BendaharaSimpananController;
use App\Http\Controllers\Bendahara\PinjamanController as BendaharaPinjamanController;
use App\Http\Controllers\Bendahara\CicilanController as BendaharaCicilanController;
use App\Http\Controllers\Ketua\LaporanController as KetuaLaporanController;
use App\Http\Controllers\Ketua\PengurusController as KetuaPengurusController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Login khusus pengurus (D-02)
Route::middleware('guest')->group(function () {
    Route::get('/login/pengurus', [PengurusLoginController::class, 'create'])
        ->name('pengurus.login');

    Route::post('/login/pengurus', [PengurusLoginController::class, 'store'])
        ->name('pengurus.login.store');
});

// Logout khusus pengurus
Route::middleware('auth:pengurus')->post('/logout/pengurus', function () {
    Auth::guard('pengurus')->logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login/pengurus');
})->name('pengurus.logout');

// Pendaftaran anggota baru (D-03)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredAnggotaController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredAnggotaController::class, 'store'])
        ->name('register.store');
});

// Lupa Kata Sandi (D-04)
Route::middleware('guest')->group(function () {
    Route::get('/lupa-kata-sandi', [PermintaanResetPasswordController::class, 'create'])
        ->name('lupa-kata-sandi');

    Route::post('/lupa-kata-sandi', [PermintaanResetPasswordController::class, 'store'])
        ->name('lupa-kata-sandi.store');
});

// Ganti Password (D-05) — dipakai untuk 2 skenario: wajib ganti (dipaksa sistem
// setelah reset oleh Sekretaris) dan ganti mandiri (anggota masih ingat password lama)
Route::middleware('auth')->group(function () {
    Route::get('/ganti-password', [GantiPasswordController::class, 'create'])
        ->name('ganti-password');
    Route::post('/ganti-password', [GantiPasswordController::class, 'store'])
        ->name('ganti-password.store');
});

// Simpanan Anggota (D-07, D-08, D-09) — landing page setelah login Anggota
Route::middleware('auth')->prefix('simpanan')->name('simpanan.')->group(function () {
    Route::get('/', [SimpananController::class, 'setor'])->name('setor');
    Route::post('/', [SimpananController::class, 'setorStore'])->name('setor.store');

    Route::get('/tarik', [SimpananController::class, 'tarik'])->name('tarik');
    Route::post('/tarik', [SimpananController::class, 'tarikStore'])->name('tarik.store');

    Route::get('/riwayat', [SimpananController::class, 'riwayat'])->name('riwayat');
});

// Pinjaman Anggota (D-10, D-11, D-12)
Route::middleware('auth')->prefix('pinjaman')->name('pinjaman.')->group(function () {
    Route::get('/', [PinjamanController::class, 'cekKelayakan'])->name('cek-kelayakan');
    Route::post('/', [PinjamanController::class, 'cekKelayakanStore'])->name('cek-kelayakan.store');

    Route::get('/ajukan', [PinjamanController::class, 'ajukan'])->name('ajukan');
    Route::post('/ajukan', [PinjamanController::class, 'ajukanStore'])->name('ajukan.store');

    Route::get('/detail', [PinjamanController::class, 'detail'])->name('detail');
});

// Cicilan Anggota (D-13, D-14)
Route::middleware('auth')->prefix('cicilan')->name('cicilan.')->group(function () {
    Route::get('/', [CicilanController::class, 'tagihan'])->name('tagihan');

    Route::get('/bayar', [CicilanController::class, 'bayar'])->name('bayar');
    Route::post('/bayar', [CicilanController::class, 'bayarStore'])->name('bayar.store');
});

// Profil Saya (D-15, D-16, D-17)
Route::middleware('auth')->prefix('profil')->name('profil.')->group(function () {
    Route::get('/', [ProfilController::class, 'detail'])->name('detail');

    Route::get('/ubah-data-diri', [ProfilController::class, 'ubahDataDiri'])->name('ubah-data-diri');
    Route::post('/ubah-data-diri', [ProfilController::class, 'ubahDataDiriStore'])->name('ubah-data-diri.store');

    Route::get('/ajukan-penghapusan', [ProfilController::class, 'ajukanPenghapusan'])->name('ajukan-penghapusan');
    Route::post('/ajukan-penghapusan', [ProfilController::class, 'ajukanPenghapusanStore'])->name('ajukan-penghapusan.store');
});

// Sekretaris — landing page setelah login: Verifikasi Pendaftaran
Route::middleware('auth:pengurus')->prefix('dashboard/sekretaris')->name('sekretaris.')->group(function () {

    // Verifikasi Pendaftaran (D-19 & D-20) — Tahap 2
    Route::get('/verifikasi', [VerifikasiPendaftaranController::class, 'index'])
        ->name('verifikasi');
    Route::get('/verifikasi/sudah-diproses', [VerifikasiPendaftaranController::class, 'sudahDiproses'])
        ->name('verifikasi.sudah-diproses');
    Route::post('/verifikasi/{id}/setujui', [VerifikasiPendaftaranController::class, 'setujui'])
        ->name('verifikasi.setujui');
    Route::post('/verifikasi/{id}/tolak', [VerifikasiPendaftaranController::class, 'tolak'])
        ->name('verifikasi.tolak');

    // Kelola Data Anggota — 3 tab: Riwayat Perubahan (D-21) | Reset Kata Sandi (D-22/D-23) | Penghapusan Anggota (D-24)
    Route::prefix('kelola-data-anggota')->name('kelola-data-anggota.')->group(function () {

        Route::get('/', [ResetKataSandiController::class, 'index'])->name('index');

        // Reset Kata Sandi (D-22 & D-23) — Tahap 3
        Route::get('/reset-kata-sandi', [ResetKataSandiController::class, 'index'])
            ->name('reset-kata-sandi');
        Route::get('/reset-kata-sandi/sudah-diproses', [ResetKataSandiController::class, 'sudahDiproses'])
            ->name('reset-kata-sandi.sudah-diproses');
        Route::post('/reset-kata-sandi/{id}/konfirmasi', [ResetKataSandiController::class, 'konfirmasi'])
            ->name('reset-kata-sandi.konfirmasi');

        // Riwayat Perubahan (D-21) — Tahap 4
        Route::get('/riwayat-perubahan', [RiwayatPerubahanController::class, 'index'])
            ->name('riwayat-perubahan');

        // Penghapusan Anggota (D-24) — Tahap 4
        Route::get('/penghapusan', [PenghapusanAnggotaController::class, 'index'])
            ->name('penghapusan');
        Route::get('/penghapusan/sudah-diproses', [PenghapusanAnggotaController::class, 'sudahDiproses'])
            ->name('penghapusan.sudah-diproses');
        Route::post('/penghapusan/{id}/hapus', [PenghapusanAnggotaController::class, 'hapus'])
            ->name('penghapusan.hapus');
    });
});

// Bendahara — landing page setelah login: Konfirmasi Setoran
Route::middleware('auth:pengurus')->prefix('dashboard/bendahara')->name('bendahara.')->group(function () {

    Route::get('/bendahara/simpanan/riwayat', [SimpananController::class, 'riwayat'])
        ->name('bendahara.simpanan.riwayat');

    // Simpanan Bendahara — 2 tab: Konfirmasi Setoran (D-26) | Konfirmasi Penarikan (D-27)
    Route::prefix('simpanan')->name('simpanan.')->group(function () {
        Route::get('/setoran', [BendaharaSimpananController::class, 'setoran'])->name('setoran');
        Route::post('/setoran/{id}/konfirmasi', [BendaharaSimpananController::class, 'setoranKonfirmasi'])->name('setoran.konfirmasi');
        Route::post('/setoran/{id}/tolak', [BendaharaSimpananController::class, 'setoranTolak'])->name('setoran.tolak');

        Route::get('/penarikan', [BendaharaSimpananController::class, 'penarikan'])->name('penarikan');
        Route::post('/penarikan/{id}/konfirmasi', [BendaharaSimpananController::class, 'penarikanKonfirmasi'])->name('penarikan.konfirmasi');
        Route::post('/penarikan/{id}/tolak', [BendaharaSimpananController::class, 'penarikanTolak'])->name('penarikan.tolak');

        Route::get('/riwayat', [BendaharaSimpananController::class, 'riwayat'])->name('riwayat');
    });

    // Pinjaman Bendahara — 3 tab: Tinjau Pengajuan (D-28) | Proses Pencairan (D-29) | Riwayat Pinjaman (D-30)
    Route::prefix('pinjaman')->name('pinjaman.')->group(function () {
        Route::get('/', [BendaharaPinjamanController::class, 'tinjau'])->name('index');

        Route::get('/tinjau', [BendaharaPinjamanController::class, 'tinjau'])->name('tinjau');
        Route::post('/tinjau/{id}/setujui', [BendaharaPinjamanController::class, 'setujui'])->name('setujui');
        Route::post('/tinjau/{id}/tolak', [BendaharaPinjamanController::class, 'tolak'])->name('tolak');

        Route::get('/pencairan', [BendaharaPinjamanController::class, 'pencairan'])->name('pencairan');
        Route::post('/pencairan/{id}/cairkan', [BendaharaPinjamanController::class, 'cairkan'])->name('cairkan');

        Route::get('/riwayat', [BendaharaPinjamanController::class, 'riwayat'])->name('riwayat');
    });

    // Cicilan Bendahara (D-31) — Konfirmasi Pembayaran Cicilan
    Route::prefix('cicilan')->name('cicilan.')->group(function () {
        Route::get('/', [BendaharaCicilanController::class, 'index'])->name('index');
        Route::get('/riwayat', [BendaharaCicilanController::class, 'riwayat'])->name('riwayat');
        Route::post('/{id}/konfirmasi', [BendaharaCicilanController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('/{id}/tolak', [BendaharaCicilanController::class, 'tolak'])->name('tolak');
    });
});

// Ketua — landing page setelah login: Pilih Laporan
Route::middleware('auth:pengurus')->prefix('dashboard/ketua')->name('ketua.')->group(function () {

    // Laporan (D-33 Pilih Laporan, D-34 Laporan Pinjaman sudah aktif, D-35/36/37/38/39 placeholder)
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [KetuaLaporanController::class, 'pilih'])->name('pilih');
        Route::post('/tampilkan', [KetuaLaporanController::class, 'tampilkan'])->name('tampilkan');
        Route::get('/riwayat/{id}', [KetuaLaporanController::class, 'lihat'])->name('lihat');

        Route::get('/pinjaman', [KetuaLaporanController::class, 'pinjaman'])->name('pinjaman');
        Route::get('/anggota', [KetuaLaporanController::class, 'anggota'])->name('anggota');
        Route::get('/simpanan', [KetuaLaporanController::class, 'simpanan'])->name('simpanan');
        Route::get('/cicilan', [KetuaLaporanController::class, 'cicilan'])->name('cicilan');
        Route::get('/pengurus', [KetuaLaporanController::class, 'pengurus'])->name('pengurus');
        Route::get('/keseluruhan', [KetuaLaporanController::class, 'keseluruhan'])->name('keseluruhan');
    });

    // Kelola Pengurus (D-40, D-41, D-42)
    Route::prefix('pengurus')->name('pengurus.')->group(function () {
        Route::get('/', [KetuaPengurusController::class, 'index'])->name('index');
        Route::get('/tambah', [KetuaPengurusController::class, 'create'])->name('create');
        Route::post('/tambah', [KetuaPengurusController::class, 'store'])->name('store');
        Route::get('/{id}/berhentikan', [KetuaPengurusController::class, 'confirmBerhentikan'])->name('berhentikan.confirm');
        Route::post('/{id}/berhentikan', [KetuaPengurusController::class, 'berhentikan'])->name('berhentikan');
    });
});

require __DIR__.'/auth.php';