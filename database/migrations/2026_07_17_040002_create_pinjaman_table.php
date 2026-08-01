<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->string('id_pinjaman', 10)->primary(); // PIN-001

            $table->string('id_anggota', 10);
            $table->foreign('id_anggota')->references('id_anggota')->on('anggota')->cascadeOnDelete();

            $table->string('id_pengurus_pencatat', 10)->nullable(); // Bendahara
            $table->foreign('id_pengurus_pencatat')->references('id_pengurus')->on('pengurus')->nullOnDelete();

            $table->string('rekening_tujuan', 50)->nullable();
            $table->string('tujuan_pinjaman', 255)->nullable();
            $table->decimal('nominal_pinjaman', 15, 2);
            $table->decimal('persentase_jasa', 5, 2)->default(0);
            $table->decimal('jumlah_jasa', 15, 2)->default(0);
            $table->decimal('total_pengembalian', 15, 2)->default(0);
            $table->decimal('cicilan_per_bulan', 15, 2)->default(0);
            $table->unsignedTinyInteger('tenor_bulan'); // 1-60
            $table->date('tanggal_pencairan')->nullable();
            $table->date('jadwal_jatuh_tempo')->nullable();
            $table->enum('status_pinjaman', [
                'Menunggu Persetujuan',
                'Menunggu Pencairan',
                'Ditolak',
                'Aktif',
                'Lunas',
            ])->default('Menunggu Persetujuan');
            $table->string('alasan_penolakan', 255)->nullable();
            $table->string('bukti_pencairan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
