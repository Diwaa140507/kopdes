<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengurus', function (Blueprint $table) {
            $table->string('id_pengurus', 10)->primary();
            $table->string('nama_pengurus', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->enum('jabatan', ['Ketua Koperasi', 'Sekretaris', 'Bendahara']);
            $table->enum('status', ['Menjabat', 'Diberhentikan'])->default('Menjabat');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengurus');
    }
};
