<?php

namespace App\Exports;

use App\Models\PembayaranTagihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

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
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    /**
     * Ambil nomor bulan
     *
     * Bisa menerima:
     * - 1 sampai 12
     * - "Januari" sampai "Desember"
     */
    private function getNomorBulan($bulan)
    {
        if (empty($bulan)) {
            return null;
        }

        if (is_numeric($bulan)) {
            $bulan = (int) $bulan;

            if ($bulan >= 1 && $bulan <= 12) {
                return $bulan;
            }

            return null;
        }

        $bulan = strtolower(trim($bulan));

        $bulanMap = [
            'januari'   => 1,
            'februari'  => 2,
            'maret'     => 3,
            'april'     => 4,
            'mei'       => 5,
            'juni'      => 6,
            'juli'      => 7,
            'agustus'   => 8,
            'september' => 9,
            'oktober'   => 10,
            'november'  => 11,
            'desember'  => 12,
        ];

        return $bulanMap[$bulan] ?? null;
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
            'tagihan.pembayaran',
            'user',
        ])
        ->where('status', 'dibayar');

        /*
        |--------------------------------------------------------------------------
        | FILTER TAHUN
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
        | FILTER BULAN
        |--------------------------------------------------------------------------
        */

        if (!empty($this->bulan)) {

            $nomorBulan = $this->getNomorBulan(
                $this->bulan
            );

            if ($nomorBulan) {

                $query->whereMonth(
                    'tanggal_kirim',
                    $nomorBulan
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | URUTKAN
        |--------------------------------------------------------------------------
        */

        return $query
            ->orderByDesc('tanggal_kirim')
            ->orderByDesc('id')
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
            'Bulan',
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
            'H' => '#,##0',

            // Dibayar
            'I' => '#,##0',

            // Sisa
            'J' => '#,##0',
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
        | NOMINAL TAGIHAN
        |--------------------------------------------------------------------------
        */

        $nominalTagihan = (float) (
            $tagihan?->nominal ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | NOMINAL PEMBAYARAN PADA TRANSAKSI INI
        |--------------------------------------------------------------------------
        */

        $dibayar = (float) (
            $item->nominal ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | TOTAL SUDAH DIBAYAR PADA TAGIHAN
        |--------------------------------------------------------------------------
        |
        | Penting untuk pembayaran lebih dari satu kali.
        |
        | Contoh:
        |
        | Tagihan        Rp500.000
        | Pembayaran 1   Rp200.000
        | Pembayaran 2   Rp300.000
        |
        | Maka total dibayar = Rp500.000
        | dan sisa = Rp0.
        |
        */

        $totalSudahDibayar = 0;

        if ($tagihan) {

            if ($tagihan->relationLoaded('pembayaran')) {

                $totalSudahDibayar = (float) $tagihan
                    ->pembayaran
                    ->whereIn(
                        'status',
                        [
                            'dibayar',
                            'disetujui',
                        ]
                    )
                    ->sum('nominal');

            } else {

                $totalSudahDibayar = (float) PembayaranTagihan::where(
                    'tagihan_id',
                    $tagihan->id
                )
                ->whereIn(
                    'status',
                    [
                        'dibayar',
                        'disetujui',
                    ]
                )
                ->sum('nominal');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisa = max(
            $nominalTagihan - $totalSudahDibayar,
            0
        );

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        $status = $sisa <= 0
            ? 'Lunas'
            : 'Belum Lunas';

        /*
        |--------------------------------------------------------------------------
        | NOMOR URUT
        |--------------------------------------------------------------------------
        */

        static $no = 0;

        $no++;

        /*
        |--------------------------------------------------------------------------
        | BULAN TAGIHAN
        |--------------------------------------------------------------------------
        |
        | Untuk SPP:
        | gunakan bulan dan tahun pada tagihan.
        |
        | Contoh:
        | Juli 2028
        | Agustus 2028
        | September 2028
        |
        */

        $bulanTagihan = '-';

        if (
            $tagihan &&
            $tagihan->bulan &&
            $tagihan->tahun
        ) {

            $bulanTagihan = Carbon::create(
                $tagihan->tahun,
                $tagihan->bulan,
                1
            )->translatedFormat('F Y');
        }

        /*
        |--------------------------------------------------------------------------
        | TANGGAL BAYAR
        |--------------------------------------------------------------------------
        */

        $tanggalBayar = '-';

        if ($item->tanggal_kirim) {

            $tanggalBayar = Carbon::parse(
                $item->tanggal_kirim
            )
                ->timezone('Asia/Jakarta')
                ->format('d-m-Y H:i');
        }

        /*
        |--------------------------------------------------------------------------
        | METODE
        |--------------------------------------------------------------------------
        */

        $metode = strtolower(
            trim($item->metode ?? '')
        );

        if ($metode === 'transfer') {

            $metode = 'Transfer';

        } elseif ($metode === 'qris') {

            $metode = 'QRIS';

        } elseif ($metode === 'cash') {

            $metode = 'Cash';

        } else {

            $metode = $item->metode ?? '-';
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN DATA
        |--------------------------------------------------------------------------
        */

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

            // Bulan
            $bulanTagihan,

            // Kategori
            $tagihan?->kategori?->nama ?? '-',

            // Tagihan
            $nominalTagihan,

            // Dibayar
            $dibayar,

            // Sisa
            $sisa,

            // Metode
            $metode,

            // Status
            $status,

            // Tanggal Bayar
            $tanggalBayar,
        ];
    }
}