<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerubahanAnggota extends Model
{
    protected $table = 'perubahan_anggota';
    protected $primaryKey = 'id_perubahan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_perubahan',
        'id_anggota',
        'jenis_perubahan',
        'data_lama',
        'data_baru',
        'tanggal_perubahan',
    ];

    protected $casts = [
        'tanggal_perubahan' => 'datetime',
    ];

    public static function generateId(): string
    {
        $terakhir = static::orderByDesc('id_perubahan')->first();
        $nomor = $terakhir ? ((int) substr($terakhir->id_perubahan, 4)) + 1 : 1;

        return 'PRB-' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }
}
