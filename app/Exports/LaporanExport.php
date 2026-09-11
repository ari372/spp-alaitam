<?php

namespace App\Exports;

use App\Models\PembayaranTagihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements
    FromCollection,
    WithHeadings,
    WithMapping
{
    protected $bulan;
    protected $tahun;

    public function __construct(
        $bulan = null,
        $tahun = null
    ) {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }


    /**
     * Daftar bulan Indonesia
     */
    private function bulanIndonesia()
    {
        return [
            'Januari'   => 1,
            'Februari'  => 2,
            'Maret'     => 3,
            'April'     => 4,
            'Mei'       => 5,
            'Juni'      => 6,
            'Juli'      => 7,
            'Agustus'   => 8,
            'September' => 9,
            'Oktober'   => 10,
            'November'  => 11,
            'Desember'  => 12,
        ];
    }


    /**
     * Ambil data pembayaran
     */
    public function collection()
    {
        $query = PembayaranTagihan::with([
            'tagihan.siswa.kelas',
            'tagihan.kategori',
            'tagihan.tahunAjaran',
            'user',
        ])

        /*
        |--------------------------------------------------------------------------
        | Hanya pembayaran yang sudah dibayar
        |--------------------------------------------------------------------------
        */

        ->where('status', 'dibayar');


        /*
        |--------------------------------------------------------------------------
        | Filter tahun
        |--------------------------------------------------------------------------
        */

        if (!empty($this->tahun)) {

            $query->whereYear(
                'tanggal_kirim',
                $this->tahun
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter bulan
        |--------------------------------------------------------------------------
        */

        if (!empty($this->bulan)) {

            $bulanMap = $this->bulanIndonesia();

            if (isset($bulanMap[$this->bulan])) {

                $query->whereMonth(
                    'tanggal_kirim',
                    $bulanMap[$this->bulan]
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Urutkan dari pembayaran terbaru
        |--------------------------------------------------------------------------
        */

        return $query
            ->orderByDesc('tanggal_kirim')
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
            'NIS',
            'Kelas',
            'Tahun Ajaran',
            'Kategori',
            'Tagihan',
            'Dibayar',
            'Sisa',
            'Metode',
            'Status',
            'Tanggal Bayar',
        ];
    }


    /**
     * Isi setiap baris Excel
     */
    public function map($item): array
    {
        $tagihan = $item->tagihan;

        /*
        |--------------------------------------------------------------------------
        | Nominal tagihan
        |--------------------------------------------------------------------------
        */

        $nominalTagihan = $tagihan?->nominal ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Nominal yang dibayar
        |--------------------------------------------------------------------------
        */

        $dibayar = $item->nominal ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Sisa
        |--------------------------------------------------------------------------
        */

        $sisa = max(
            0,
            $nominalTagihan - $dibayar
        );


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        $status = $sisa <= 0
            ? 'Lunas'
            : 'Belum Lunas';


        /*
        |--------------------------------------------------------------------------
        | Nomor urut
        |--------------------------------------------------------------------------
        */

        static $no = 0;

        $no++;


        return [

            // No
            $no,

            // Siswa
            $tagihan?->siswa?->nama ?? '-',

            // NIS
            $tagihan?->siswa?->nis ?? '-',

            // Kelas
            $tagihan?->siswa?->kelas?->nama_kelas ?? '-',

            // Tahun Ajaran
            $tagihan?->tahunAjaran?->nama ?? '-',

            // Kategori
            $tagihan?->kategori?->nama ?? '-',

            // Total tagihan
            $nominalTagihan,

            // Dibayar
            $dibayar,

            // Sisa
            $sisa,

            // Metode pembayaran
            $item->metode ?? '-',

            // Status pembayaran
            $status,

            // Tanggal pembayaran
            $item->tanggal_kirim
                ? $item->tanggal_kirim->format('d-m-Y H:i')
                : '-',
        ];
    }
}