<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable()->after('name');
            $table->date('tanggal_lahir')->nullable()->after('jenis_kelamin');
            $table->text('alamat')->nullable()->after('tanggal_lahir');
            $table->string('dokumen_pendukung')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'tanggal_lahir', 'alamat', 'dokumen_pendukung']);
        });
    }
};
