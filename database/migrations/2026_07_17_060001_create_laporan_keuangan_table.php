<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_keuangan', function (Blueprint $table) {
            $table->string('id_laporan', 15)->primary(); // LAP-KES-001, LAP-PIN-001, dst.
            $table->enum('jenis_laporan', ['Anggota', 'Simpanan', 'Pinjaman', 'Cicilan', 'Pengurus', 'Keseluruhan']);
            $table->unsignedTinyInteger('periode_bulan');
            $table->year('periode_tahun');
            $table->date('tanggal_dibuat');
            $table->string('id_pengurus_pembuat', 10);
            $table->decimal('total_simpanan_masuk', 15, 2)->nullable();
            $table->decimal('total_penarikan_keluar', 15, 2)->nullable();
            $table->decimal('total_pinjaman_dicairkan', 15, 2)->nullable();
            $table->decimal('total_cicilan_masuk', 15, 2)->nullable();
            $table->decimal('total_denda_masuk', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_pengurus_pembuat')->references('id_pengurus')->on('pengurus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_keuangan');
    }
};
