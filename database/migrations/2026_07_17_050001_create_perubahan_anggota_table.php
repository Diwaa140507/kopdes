<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perubahan_anggota', function (Blueprint $table) {
            $table->string('id_perubahan', 10)->primary();
            $table->string('id_anggota', 10);
            $table->enum('jenis_perubahan', ['Alamat', 'Email', 'Kata_Sandi']);
            $table->string('data_lama')->nullable();
            $table->string('data_baru')->nullable();
            $table->timestamp('tanggal_perubahan')->useCurrent();

            $table->foreign('id_anggota')->references('id_anggota')->on('anggota')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perubahan_anggota');
    }
};
