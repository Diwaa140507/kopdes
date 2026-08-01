<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->string('id_anggota', 10)->primary();
            $table->string('email', 100)->unique();
            $table->char('nik', 16)->unique();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->date('tanggal_lahir');
            $table->string('alamat_lengkap', 255);
            $table->string('no_hp', 15)->nullable();
            $table->string('password', 255);
            $table->string('dokumen_pendukung')->nullable();

            // Ditambahkan 'Menunggu Verifikasi' di luar dokumen asli (Terverifikasi/Terhapus)
            // supaya alur pendaftaran D-03 bisa berjalan sebelum disetujui Sekretaris
            $table->enum('status_keanggotaan', ['Menunggu Verifikasi', 'Terverifikasi', 'Terhapus'])
                  ->default('Menunggu Verifikasi');

            $table->date('tanggal_daftar')->nullable();
            $table->date('tanggal_perubahan_terakhir')->nullable();
            $table->string('alasan_penghapusan', 255)->nullable();

            $table->string('id_pengurus_pencatat', 10)->nullable();
            $table->foreign('id_pengurus_pencatat')->references('id_pengurus')->on('pengurus')->nullOnDelete();

            // Kolom pendukung alur D-04/D-05 yang sudah kita bangun
            $table->boolean('wajib_ganti_password')->default(true);
            $table->string('status_permintaan_reset')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
