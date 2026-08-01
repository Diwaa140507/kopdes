<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            // Menyimpan password sementara hasil reset dalam bentuk plaintext,
            // supaya Sekretaris bisa lihat lagi di D-23 (Sudah Diproses) kalau perlu.
            // CATATAN KEAMANAN: ini di luar dokumen resmi & praktik ideal (idealnya hanya di-hash),
            // disimpan plaintext atas permintaan eksplisit user untuk kebutuhan operasional tugas kuliah.
            $table->string('password_sementara_plain', 255)->nullable()->after('id_pengurus_pencatat');
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn('password_sementara_plain');
        });
    }
};
