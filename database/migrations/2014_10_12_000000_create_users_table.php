<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->nullable()->unique(); // khusus anggota
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->default('anggota'); // anggota, sekretaris, bendahara, ketua
            $table->string('status')->default('menunggu_verifikasi'); // menunggu_verifikasi, aktif, ditolak
            $table->boolean('wajib_ganti_password')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
