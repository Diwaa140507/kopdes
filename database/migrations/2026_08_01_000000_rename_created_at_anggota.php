<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('anggota', 'created_at') && !Schema::hasColumn('anggota', 'tanggal_daftar')) {
            Schema::table('anggota', function (Blueprint $table) {
                $table->renameColumn('created_at', 'tanggal_daftar');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('anggota', 'tanggal_daftar') && !Schema::hasColumn('anggota', 'created_at')) {
            Schema::table('anggota', function (Blueprint $table) {
                $table->renameColumn('tanggal_daftar', 'created_at');
            });
        }
    }
};