<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKeuangan extends Model
{
    protected $table = 'laporan_keuangan';
    protected $primaryKey = 'id_laporan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_laporan',
        'jenis_laporan',
        'periode_bulan',
        'periode_tahun',
        'tanggal_dibuat',
        'id_pengurus_pembuat',
        'total_simpanan_masuk',
        'total_penarikan_keluar',
        'total_pinjaman_dicairkan',
        'total_cicilan_masuk',
        'total_denda_masuk',
    ];

    protected $casts = [
        'tanggal_dibuat' => 'date',
        'total_simpanan_masuk' => 'decimal:2',
        'total_penarikan_keluar' => 'decimal:2',
        'total_pinjaman_dicairkan' => 'decimal:2',
        'total_cicilan_masuk' => 'decimal:2',
        'total_denda_masuk' => 'decimal:2',
    ];

    // Kode prefix per jenis laporan, dipakai untuk generate ID
    const KODE_JENIS = [
        'Anggota' => 'AGG',
        'Simpanan' => 'SMP',
        'Pinjaman' => 'PIN',
        'Cicilan' => 'CIC',
        'Pengurus' => 'PNG',
        'Keseluruhan' => 'KES',
    ];

    /**
     * Generate ID baru per jenis: LAP-PIN-001, LAP-KES-004, dst.
     */
    public static function generateId(string $jenisLaporan): string
    {
        $kode = self::KODE_JENIS[$jenisLaporan] ?? 'LAP';

        $terakhir = self::where('jenis_laporan', $jenisLaporan)
            ->orderByDesc('id_laporan')
            ->first();

        $nomor = $terakhir
            ? ((int) substr($terakhir->id_laporan, -3)) + 1
            : 1;

        return 'LAP-' . $kode . '-' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    public function pengurusPembuat()
    {
        return $this->belongsTo(Pengurus::class, 'id_pengurus_pembuat', 'id_pengurus');
    }
}
