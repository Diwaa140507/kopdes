<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Anggota extends Authenticatable
{
    use Notifiable;

    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'tanggal_daftar';
    const UPDATED_AT = 'tanggal_perubahan_terakhir';

    protected $fillable = [
        'id_anggota',
        'email',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat_lengkap',
        'password',
        'dokumen_pendukung',
        'foto_profil',
        'status_keanggotaan',
        'tanggal_verifikasi',
        'alasan_penghapusan',
        'nominal_wajib_ditarik',
        'metode_penarikan_wajib',
        'no_rekening_tujuan_wajib',
        'catatan_penolakan',
        'id_pengurus_pencatat',
        'wajib_ganti_password',
        'status_permintaan_reset',
        'password_sementara_plain',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'wajib_ganti_password' => 'boolean',
        'tanggal_verifikasi' => 'date',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Generate ID baru: AGG-001, AGG-002, dst.
     */
    public static function generateId(): string
    {
        $terakhir = self::orderByDesc('id_anggota')->first();

        $nomor = $terakhir
            ? ((int) substr($terakhir->id_anggota, 4)) + 1
            : 1;

        return 'AGG-'.str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke Pengurus yang mencatat/memproses anggota ini (dipakai di D-20 & D-23).
     */
    public function pengurusPencatat()
    {
        return $this->belongsTo(\App\Models\Pengurus::class, 'id_pengurus_pencatat', 'id_pengurus');
    }

    /**
     * Laravel Auth butuh field 'name' untuk beberapa fitur bawaan Breeze (mis. navbar).
     * Alias supaya nggak perlu ubah view Breeze lain yang masih pakai $user->name.
     */
    public function getNameAttribute()
    {
        return $this->nama_lengkap;
    }
}
