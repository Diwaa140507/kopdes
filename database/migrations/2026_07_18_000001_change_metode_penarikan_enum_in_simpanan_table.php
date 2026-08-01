<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah opsi 'E-Wallet' ke enum metode_penarikan di tabel simpanan.
     * Sebelumnya cuma ['Transfer Bank', 'Tunai'], padahal form Anggota
     * (Ajukan Penghapusan Akun) punya opsi "E-Wallet" juga.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE simpanan MODIFY COLUMN metode_penarikan ENUM('Transfer Bank', 'E-Wallet', 'Tunai') NULL");
    }

    public function down(): void
    {
        // Rollback: kembalikan ke enum lama. Kalau ada baris ber-value 'E-Wallet',
        // baris itu perlu diubah dulu manual sebelum rollback (MySQL akan
        // mengosongkan/mengubahnya jadi '' jika value tidak lagi valid di enum).
        DB::statement("ALTER TABLE simpanan MODIFY COLUMN metode_penarikan ENUM('Transfer Bank', 'Tunai') NULL");
    }
};
