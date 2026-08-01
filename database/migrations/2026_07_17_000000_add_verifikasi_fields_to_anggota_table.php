<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Tambah kolom catatan_penolakan & tanggal_verifikasi
        Schema::table('anggota', function (Blueprint $table) {
            $table->string('catatan_penolakan', 255)->nullable()->after('alasan_penghapusan');
            $table->date('tanggal_verifikasi')->nullable()->after('tanggal_perubahan_terakhir');
        });

        // 2. Tambah nilai 'Ditolak' ke enum status_keanggotaan
        DB::statement("ALTER TABLE anggota MODIFY COLUMN status_keanggotaan ENUM('Menunggu Verifikasi','Terverifikasi','Ditolak','Terhapus') NOT NULL DEFAULT 'Menunggu Verifikasi'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['catatan_penolakan', 'tanggal_verifikasi']);
        });

        DB::statement("ALTER TABLE anggota MODIFY COLUMN status_keanggotaan ENUM('Menunggu Verifikasi','Terverifikasi','Terhapus') NOT NULL DEFAULT 'Menunggu Verifikasi'");
    }
};
