<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_cicilan', function (Blueprint $table) {
            $table->string('id_cicilan', 10)->primary(); // CIC-001

            $table->string('id_pinjaman', 10);
            $table->foreign('id_pinjaman')->references('id_pinjaman')->on('pinjaman')->cascadeOnDelete();

            $table->string('id_anggota', 10);
            $table->foreign('id_anggota')->references('id_anggota')->on('anggota')->cascadeOnDelete();

            $table->string('id_pengurus_pencatat', 10)->nullable(); // Bendahara
            $table->foreign('id_pengurus_pencatat')->references('id_pengurus')->on('pengurus')->nullOnDelete();

            $table->unsignedTinyInteger('no_angsuran');
            $table->date('tanggal_pembayaran')->nullable();
            $table->decimal('jumlah_pembayaran', 15, 2); // >= cicilan_per_bulan + jumlah_denda
            $table->enum('metode_setoran', ['QRIS', 'Tunai'])->nullable();
            $table->string('bukti_transaksi')->nullable();
            $table->enum('status_pembayaran', ['Menunggu Konfirmasi', 'Terverifikasi', 'Ditolak'])->default('Menunggu Konfirmasi');
            $table->string('catatan_penolakan', 255)->nullable();
            $table->decimal('jumlah_denda', 15, 2)->default(0); // persentase_denda x nominal_pinjaman x hari_keterlambatan
            $table->decimal('sisa_hutang', 15, 2)->default(0); // saldo berjalan setelah pembayaran ini

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_cicilan');
    }
};
