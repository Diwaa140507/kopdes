<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            // tanggal_verifikasi ternyata sudah ada di database (dari eksperimen sebelumnya),
            // jadi di sini kita cek dulu satu-satu supaya tidak nabrak "column already exists".
            if (! Schema::hasColumn('anggota', 'tanggal_verifikasi')) {
                $table->date('tanggal_verifikasi')->nullable()->after('status_keanggotaan');
            }

            if (! Schema::hasColumn('anggota', 'catatan_penolakan')) {
                $table->string('catatan_penolakan', 255)->nullable()->after('tanggal_verifikasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            if (Schema::hasColumn('anggota', 'catatan_penolakan')) {
                $table->dropColumn('catatan_penolakan');
            }
        });
    }
};
