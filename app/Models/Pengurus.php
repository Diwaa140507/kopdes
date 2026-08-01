<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengurus extends Authenticatable
{
    use Notifiable;

    protected $table = 'pengurus';
    protected $primaryKey = 'id_pengurus';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'tanggal_diangkat';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_pengurus',
        'nama_pengurus',
        'email',
        'password',
        'jabatan',
        'status',
        'tanggal_diberhentikan',
    ];

    protected $casts = [
        'tanggal_diberhentikan' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Generate ID baru sesuai jabatan.
     * Ketua Koperasi -> KET-001, Sekretaris -> SEK-001, Bendahara -> BEN-001
     */
    public static function generateId(string $jabatan): string
    {
        $prefix = match ($jabatan) {
            'Ketua Koperasi' => 'KET',
            'Sekretaris' => 'SEK',
            'Bendahara' => 'BEN',
        };

        $terakhir = self::where('id_pengurus', 'like', $prefix.'-%')
            ->orderByDesc('id_pengurus')
            ->first();

        $nomor = $terakhir
            ? ((int) substr($terakhir->id_pengurus, 4)) + 1
            : 1;

        return $prefix.'-'.str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }
}