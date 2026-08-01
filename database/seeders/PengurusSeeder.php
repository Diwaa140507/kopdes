<?php

namespace Database\Seeders;

use App\Models\Pengurus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PengurusSeeder extends Seeder
{
    public function run(): void
    {
        Pengurus::create([
            'id_pengurus' => 'SEK-001',
            'nama_pengurus' => 'Sekretaris Koperasi',
            'email' => 'sekretaris.001@koperasimerahputih.id',
            'password' => Hash::make('sekretaris123'),
            'jabatan' => 'Sekretaris',
            'status' => 'Menjabat',
        ]);

        Pengurus::create([
            'id_pengurus' => 'KET-001',
            'nama_pengurus' => 'Ketua Koperasi',
            'email' => 'ketua.001@koperasimerahputih.id',
            'password' => Hash::make('ketua123'),
            'jabatan' => 'Ketua Koperasi',
            'status' => 'Menjabat',
        ]);

        Pengurus::create([
            'id_pengurus' => 'BEN-001',
            'nama_pengurus' => 'Bendahara Koperasi',
            'email' => 'bendahara.001@koperasimerahputih.id',
            'password' => Hash::make('bendahara123'),
            'jabatan' => 'Bendahara',
            'status' => 'Menjabat',
        ]);
    }
}
