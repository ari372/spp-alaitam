<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use RuntimeException;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $barisExcel = $index + 2;

            $nis = trim((string) ($row['nis'] ?? ''));
            $namaSiswa = trim((string) ($row['nama_siswa'] ?? ''));
            $jenisKelamin = strtoupper(trim((string) ($row['jenis_kelamin'] ?? '')));
            $alamatSiswa = trim((string) ($row['alamat_siswa'] ?? ''));
            $namaKelas = trim((string) ($row['kelas'] ?? ''));

            $namaOrangTua = trim((string) ($row['nama_orang_tua'] ?? ''));
            $emailOrangTua = strtolower(trim((string) ($row['email_orang_tua'] ?? '')));
            $noHpOrangTua = trim((string) ($row['no_hp_orang_tua'] ?? ''));
            $alamatOrangTua = trim((string) ($row['alamat_orang_tua'] ?? ''));
            $passwordOrangTua = trim((string) ($row['password_orang_tua'] ?? ''));

            if (
                $nis === '' &&
                $namaSiswa === '' &&
                $emailOrangTua === ''
            ) {
                continue;
            }

            if ($nis === '') {
                throw new RuntimeException(
                    "Baris {$barisExcel}: NIS siswa wajib diisi."
                );
            }

            if ($namaSiswa === '') {
                throw new RuntimeException(
                    "Baris {$barisExcel}: Nama siswa wajib diisi."
                );
            }

            if (!in_array($jenisKelamin, ['L', 'P'])) {
                throw new RuntimeException(
                    "Baris {$barisExcel}: Jenis kelamin harus L atau P."
                );
            }

            if ($namaKelas === '') {
                throw new RuntimeException(
                    "Baris {$barisExcel}: Kelas wajib diisi."
                );
            }

            if ($namaOrangTua === '') {
                throw new RuntimeException(
                    "Baris {$barisExcel}: Nama orang tua wajib diisi."
                );
            }

            if (!filter_var($emailOrangTua, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException(
                    "Baris {$barisExcel}: Email orang tua tidak valid."
                );
            }

            $siswaSudahAda = Siswa::where('nis', $nis)->exists();

            if ($siswaSudahAda) {
                throw new RuntimeException(
                    "Baris {$barisExcel}: NIS {$nis} sudah terdaftar."
                );
            }

            $kelas = Kelas::whereRaw(
                'LOWER(TRIM(nama_kelas)) = ?',
                [strtolower($namaKelas)]
            )->first();

            if (!$kelas) {
                throw new RuntimeException(
                    "Baris {$barisExcel}: Kelas {$namaKelas} tidak ditemukan."
                );
            }

            $user = User::where('email', $emailOrangTua)->first();

            if ($user) {
                if (strtolower(trim($user->role)) !== 'orang_tua') {
                    throw new RuntimeException(
                        "Baris {$barisExcel}: Email {$emailOrangTua} sudah digunakan oleh akun selain orang tua."
                    );
                }

                $orangTua = OrangTua::where('user_id', $user->id)->first();

                if (!$orangTua) {
                    $orangTua = OrangTua::create([
                        'user_id' => $user->id,
                        'nama' => $namaOrangTua,
                        'no_hp' => $noHpOrangTua ?: null,
                        'alamat' => $alamatOrangTua ?: null,
                    ]);
                }
            } else {
                $password = $passwordOrangTua !== ''
                    ? $passwordOrangTua
                    : 'orangtua123';

                $user = User::create([
                    'name' => $namaOrangTua,
                    'email' => $emailOrangTua,
                    'password' => Hash::make($password),
                    'role' => 'orang_tua',
                ]);

                $orangTua = OrangTua::create([
                    'user_id' => $user->id,
                    'nama' => $namaOrangTua,
                    'no_hp' => $noHpOrangTua ?: null,
                    'alamat' => $alamatOrangTua ?: null,
                ]);
            }

            Siswa::create([
                'nis' => $nis,
                'nama' => $namaSiswa,
                'jenis_kelamin' => $jenisKelamin,
                'alamat' => $alamatSiswa ?: null,
                'kelas_id' => $kelas->id,
                'orang_tua_id' => $orangTua->id,
            ]);
        }
    }
}