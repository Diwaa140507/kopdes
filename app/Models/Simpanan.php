<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $table = 'simpanan';
    protected $primaryKey = 'id_simpanan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_simpanan',
        'id_anggota',
        'id_pengurus_pencatat',
        'jenis_simpanan',
        'jenis_transaksi',
        'metode_setoran',
        'metode_penarikan',
        'nama_bank_ewallet',
        'no_rekening_tujuan',
        'nama_pemilik_rekening',
        'bukti_transaksi',
        'status_transaksi',
        'catatan_penolakan',
        'jumlah',
        'tanggal_transaksi',
        'saldo_simpanan_wajib',
        'saldo_simpanan_sukarela',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'saldo_simpanan_wajib' => 'decimal:2',
        'saldo_simpanan_sukarela' => 'decimal:2',
        'tanggal_transaksi' => 'date',
    ];

    /**
     * Generate ID baru: SMP-001, SMP-002, dst.
     */
    public static function generateId(): string
    {
        $terakhir = self::orderByDesc('id_simpanan')->first();

        $nomor = $terakhir
            ? ((int) substr($terakhir->id_simpanan, 4)) + 1
            : 1;

        return 'SMP-' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Ambil saldo wajib & sukarela terkini seorang anggota,
     * berdasarkan transaksi "Berhasil" terakhir (running balance).
     */
    public static function currentSaldo(string $idAnggota): array
    {
        $terakhir = self::where('id_anggota', $idAnggota)
            ->where('status_transaksi', 'Berhasil')
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id_simpanan')
            ->first();

        return [
            'wajib' => $terakhir->saldo_simpanan_wajib ?? 0,
            'sukarela' => $terakhir->saldo_simpanan_sukarela ?? 0,
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function pengurusPencatat()
    {
        return $this->belongsTo(Pengurus::class, 'id_pengurus_pencatat', 'id_pengurus');
    }
}
