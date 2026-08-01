<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';
    protected $primaryKey = 'id_pinjaman';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pinjaman',
        'id_anggota',
        'id_pengurus_pencatat',
        'rekening_tujuan',
        'tujuan_pinjaman',
        'nominal_pinjaman',
        'persentase_jasa',
        'jumlah_jasa',
        'total_pengembalian',
        'cicilan_per_bulan',
        'tenor_bulan',
        'tanggal_pencairan',
        'jadwal_jatuh_tempo',
        'status_pinjaman',
        'alasan_penolakan',
        'bukti_pencairan',
    ];

    protected $casts = [
        'nominal_pinjaman' => 'decimal:2',
        'persentase_jasa' => 'decimal:2',
        'jumlah_jasa' => 'decimal:2',
        'total_pengembalian' => 'decimal:2',
        'cicilan_per_bulan' => 'decimal:2',
        'tanggal_pencairan' => 'date',
        'jadwal_jatuh_tempo' => 'date',
    ];

    /**
     * Generate ID baru: PIN-001, PIN-002, dst.
     */
    public static function generateId(): string
    {
        $terakhir = self::orderByDesc('id_pinjaman')->first();

        $nomor = $terakhir
            ? ((int) substr($terakhir->id_pinjaman, 4)) + 1
            : 1;

        return 'PIN-' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function pengurusPencatat()
    {
        return $this->belongsTo(Pengurus::class, 'id_pengurus_pencatat', 'id_pengurus');
    }

    public function cicilan()
    {
        return $this->hasMany(PembayaranCicilan::class, 'id_pinjaman', 'id_pinjaman');
    }
}
