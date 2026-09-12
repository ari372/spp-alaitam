<?php

namespace App\Exports;

use App\Models\PembayaranTagihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnFormatting
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
        ->where('status', 'dibayar');


        /*
        |--------------------------------------------------------------------------
        | Filter Tahun
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
        | Filter Bulan
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
        | Urutkan pembayaran terbaru
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
     * Format kolom Excel
     */
    public function columnFormats(): array
    {
        return [

            // Tagihan
            'G' => '#,##0',

            // Dibayar
            'H' => '#,##0',

            // Sisa
            'I' => '#,##0',

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
        | Nominal Tagihan
        |--------------------------------------------------------------------------
        */

        $nominalTagihan = (float) ($tagihan?->nominal ?? 0);


        /*
        |--------------------------------------------------------------------------
        | Nominal Dibayar
        |--------------------------------------------------------------------------
        */

        $dibayar = (float) ($item->nominal ?? 0);


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
        | Nomor Urut
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

            // Tagihan
            $nominalTagihan,

            // Dibayar
            $dibayar,

            // Sisa
            $sisa,

            // Metode
            $item->metode ?? '-',

            // Status
            $status,

            // Tanggal Bayar
            $item->tanggal_kirim
                ? $item->tanggal_kirim->format('d-m-Y H:i')
                : '-',
        ];
    }
}