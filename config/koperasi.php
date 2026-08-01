<?php

// Simpan di: config/koperasi.php

return [

    /*
    |--------------------------------------------------------------------------
    | Modal Awal Koperasi
    |--------------------------------------------------------------------------
    |
    | Saldo kas awal koperasi sebelum ada transaksi apapun. Dipakai sebagai
    | titik tolak perhitungan Saldo Kas Koperasi di Laporan Keseluruhan
    | (Saldo Kas = Modal Awal + Simpanan Masuk + Cicilan Masuk + Denda Masuk
    | - Pinjaman Dicairkan - Penarikan Simpanan).
    |
    */

    'modal_awal' => 50000000,

];