<?php

namespace Database\Seeders;

use App\Models\PengaturanPembayaran;
use Illuminate\Database\Seeder;

class PengaturanPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        PengaturanPembayaran::firstOrCreate(
            ['id' => 1],
            [
                'tanggal_jatuh_tempo' => 5,
                'hari_pengingat' => 3,
                'aktif' => true,
            ]
        );
    }
}