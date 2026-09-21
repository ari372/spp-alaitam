<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateSiswaExport implements
    FromArray,
    WithHeadings,
    ShouldAutoSize,
    WithStyles
{
    /**
     * Header kolom Excel.
     */
    public function headings(): array
    {
        return [
            'nis',
            'nama_siswa',
            'jenis_kelamin',
            'alamat_siswa',
            'kelas',
            'nama_orang_tua',
            'email_orang_tua',
            'no_hp_orang_tua',
            'alamat_orang_tua',
            'password_orang_tua',
        ];
    }

    /**
     * Contoh data pada template.
     */
    public function array(): array
    {
        return [
            [
                '1004',
                'Contoh Nama Siswa',
                'L',
                'Bandung',
                '7A',
                'Nama Orang Tua',
                'orangtua@example.com',
                '081234567890',
                'Bandung',
                'orangtua123',
            ],
        ];
    }

    /**
     * Styling header Excel.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '0F5132',
                    ],
                ],
            ],
        ];
    }
}