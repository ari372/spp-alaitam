<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\OrangTua;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\KategoriTagihan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================
        // ADMIN
        // =========================================
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@alaitam.sch.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // =========================================
        // ORANG TUA
        // =========================================
        $userOrangTua = User::create([
            'name' => 'Bapak Ahmad',
            'email' => 'ahmad@gmail.com',
            'password' => Hash::make('orangtua123'),
            'role' => 'orang_tua',
        ]);

        OrangTua::create([
            'user_id' => $userOrangTua->id,
            'nama' => 'Bapak Ahmad',
            'no_hp' => '081234567890',
            'alamat' => 'Bandung',
        ]);

        // =========================================
        // KELAS
        // =========================================
        Kelas::create(['nama_kelas' => '7A']);
        Kelas::create(['nama_kelas' => '7B']);
        Kelas::create(['nama_kelas' => '8A']);
        Kelas::create(['nama_kelas' => '8B']);
        Kelas::create(['nama_kelas' => '9A']);
        Kelas::create(['nama_kelas' => '9B']);

        // =========================================
        // TAHUN AJARAN
        // =========================================
        TahunAjaran::create([
            'nama' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'aktif' => true,
        ]);

        TahunAjaran::create([
            'nama' => '2027/2028',
            'tanggal_mulai' => '2027-07-01',
            'tanggal_selesai' => '2028-06-30',
            'aktif' => false,
        ]);

        TahunAjaran::create([
            'nama' => '2028/2029',
            'tanggal_mulai' => '2028-07-01',
            'tanggal_selesai' => '2029-06-30',
            'aktif' => false,
        ]);

        // =========================================
        // KATEGORI TAGIHAN
        // =========================================
        KategoriTagihan::create([
            'nama' => 'SPP',
            'nominal' => 1350000,
            'keterangan' => 'Pembayaran SPP',
        ]);

        KategoriTagihan::create([
            'nama' => 'Jas/Baju',
            'nominal' => 500000,
            'keterangan' => 'Pembayaran jas atau baju siswa',
        ]);

        KategoriTagihan::create([
            'nama' => 'PTS',
            'nominal' => 550000,
            'keterangan' => 'Pembayaran PTS/Ujian',
        ]);

        KategoriTagihan::create([
            'nama' => 'Ujian',
            'nominal' => 550000,
            'keterangan' => 'Pembayaran ujian siswa',
        ]);
    }
}