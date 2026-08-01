<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->decimal('nominal_wajib_ditarik', 15, 2)->nullable()->after('alasan_penghapusan');
            $table->string('metode_penarikan_wajib')->nullable()->after('nominal_wajib_ditarik');
            $table->string('no_rekening_tujuan_wajib')->nullable()->after('metode_penarikan_wajib');
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['nominal_wajib_ditarik', 'metode_penarikan_wajib', 'no_rekening_tujuan_wajib']);
        });
    }
};
