<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanan', function (Blueprint $table) {
            $table->string('id_simpanan', 10)->primary(); // SMP-001

            $table->string('id_anggota', 10);
            $table->foreign('id_anggota')->references('id_anggota')->on('anggota')->cascadeOnDelete();

            $table->string('id_pengurus_pencatat', 10)->nullable(); // Bendahara
            $table->foreign('id_pengurus_pencatat')->references('id_pengurus')->on('pengurus')->nullOnDelete();

            $table->enum('jenis_simpanan', ['Wajib', 'Sukarela']);
            $table->enum('jenis_transaksi', ['Setoran', 'Penarikan']);
            $table->enum('metode_setoran', ['QRIS', 'Tunai'])->nullable();
            $table->enum('metode_penarikan', ['Transfer Bank', 'Tunai'])->nullable();
            $table->string('nama_bank_ewallet', 100)->nullable();
            $table->string('no_rekening_tujuan', 50)->nullable();
            $table->string('nama_pemilik_rekening', 100)->nullable();
            $table->string('bukti_transaksi')->nullable();
            $table->enum('status_transaksi', ['Menunggu', 'Berhasil', 'Ditolak'])->default('Menunggu');
            $table->string('catatan_penolakan', 255)->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_transaksi')->nullable();

            // Saldo berjalan (running balance) setelah transaksi ini
            $table->decimal('saldo_simpanan_wajib', 15, 2)->default(0);
            $table->decimal('saldo_simpanan_sukarela', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simpanan');
    }
};
