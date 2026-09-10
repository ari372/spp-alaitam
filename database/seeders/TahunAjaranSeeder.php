<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        TahunAjaran::updateOrCreate(
            ['nama' => '2026/2027'],
            ['aktif' => true]
        );

        TahunAjaran::updateOrCreate(
            ['nama' => '2027/2028'],
            ['aktif' => false]
        );

        TahunAjaran::updateOrCreate(
            ['nama' => '2028/2029'],
            ['aktif' => false]
        );
    }
}