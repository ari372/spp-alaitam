<?php

namespace App\Exports;

use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements
    FromCollection,
    WithHeadings,
    WithMapping
{
    protected $tahunAjaranId;
    protected $kategoriId;

    public function __construct(
        $tahunAjaranId = null,
        $kategoriId = null
    ) {
        $this->tahunAjaranId = $tahunAjaranId;
        $this->kategoriId = $kategoriId;
    }


    /**
     * Ambil data
     */
    public function collection()
    {
        $query = Tagihan::with([
            'siswa.kelas',
            'tahunAjaran',
            'kategori',
            'pembayaran'
        ]);


        if ($this->tahunAjaranId) {

            $query->where(
                'tahun_ajaran_id',
                $this->tahunAjaranId
            );
        }


        if ($this->kategoriId) {

            $query->where(
                'kategori_tagihan_id',
                $this->kategoriId
            );
        }


        return $query
            ->latest()
            ->get();
    }


    /**
     * Header Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Siswa',
            'Kelas',
            'Tahun Ajaran',
            'Kategori',
            'Tagihan',
            'Dibayar',
            'Sisa',
            'Status',
        ];
    }


    /**
     * Isi setiap baris
     */
    public function map($item): array
    {
        $dibayar = $item->pembayaran
            ->where('status', 'disetujui')
            ->sum('nominal');

        $sisa = max(
            0,
            $item->nominal - $dibayar
        );


        $status = $sisa <= 0
            ? 'Lunas'
            : 'Belum Lunas';


        static $no = 0;

        $no++;


        return [
            $no,
            $item->siswa->nama ?? '-',
            $item->siswa->kelas->nama_kelas ?? '-',
            $item->tahunAjaran->nama ?? '-',
            $item->kategori->nama ?? '-',
            $item->nominal,
            $dibayar,
            $sisa,
            $status,
        ];
    }
}