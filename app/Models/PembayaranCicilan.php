<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranCicilan extends Model
{
    protected $table = 'pembayaran_cicilan';
    protected $primaryKey = 'id_cicilan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_cicilan',
        'id_pinjaman',
        'id_anggota',
        'id_pengurus_pencatat',
        'no_angsuran',
        'tanggal_pembayaran',
        'jumlah_pembayaran',
        'metode_setoran',
        'bukti_transaksi',
        'status_pembayaran',
        'catatan_penolakan',
        'jumlah_denda',
        'sisa_hutang',
    ];

    protected $casts = [
        'jumlah_pembayaran' => 'decimal:2',
        'jumlah_denda' => 'decimal:2',
        'sisa_hutang' => 'decimal:2',
        'tanggal_pembayaran' => 'date',
    ];

    /**
     * Generate ID baru: CIC-001, CIC-002, dst.
     */
    public static function generateId(): string
    {
        $terakhir = self::orderByDesc('id_cicilan')->first();

        $nomor = $terakhir
            ? ((int) substr($terakhir->id_cicilan, 4)) + 1
            : 1;

        return 'CIC-' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'id_pinjaman', 'id_pinjaman');
    }

    public function pengurusPencatat()
    {
        return $this->belongsTo(Pengurus::class, 'id_pengurus_pencatat', 'id_pengurus');
    }
}
