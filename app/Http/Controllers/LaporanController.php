<?php

namespace App\Http\Controllers;

use App\Models\PembayaranTagihan;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
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
     * Query pembayaran
     */
    private function getPembayaranQuery(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $query = PembayaranTagihan::with([
            'tagihan.siswa.kelas',
            'tagihan.kategori',
            'tagihan.tahunAjaran',
            'user',
        ]);


        /*
        |--------------------------------------------------------------------------
        | FILTER TAHUN
        |--------------------------------------------------------------------------
        */

        if (!empty($tahun)) {

            $query->whereYear(
                'tanggal_kirim',
                $tahun
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN
        |--------------------------------------------------------------------------
        */

        if (!empty($bulan)) {

            $bulanMap = $this->bulanIndonesia();

            if (isset($bulanMap[$bulan])) {

                $query->whereMonth(
                    'tanggal_kirim',
                    $bulanMap[$bulan]
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | URUTKAN
        |--------------------------------------------------------------------------
        */

        return $query->orderByDesc(
            'tanggal_kirim'
        );
    }


    /**
     * Menampilkan laporan pembayaran
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $pembayaran = $this
            ->getPembayaranQuery($request)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMBAYARAN
        |--------------------------------------------------------------------------
        |
        | Hanya pembayaran dengan status "dibayar"
        |
        */

        $totalPembayaran = $pembayaran
            ->where('status', 'dibayar')
            ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | AMBIL ID TAGIHAN
        |--------------------------------------------------------------------------
        */

        $tagihanIds = $pembayaran
            ->pluck('tagihan_id')
            ->filter()
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAGIHAN
        |--------------------------------------------------------------------------
        */

        $totalTagihan = Tagihan::whereIn(
            'id',
            $tagihanIds
        )->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            0,
            $totalTagihan - $totalPembayaran
        );


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.laporan.index',
            compact(
                'pembayaran',
                'bulan',
                'tahun',
                'totalPembayaran',
                'totalTagihan',
                'sisaTagihan'
            )
        );
    }


    /**
     * Cetak PDF
     */
    public function pdf(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $pembayaran = $this
            ->getPembayaranQuery($request)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $totalPembayaran = $pembayaran
            ->where('status', 'dibayar')
            ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAGIHAN
        |--------------------------------------------------------------------------
        */

        $tagihanIds = $pembayaran
            ->pluck('tagihan_id')
            ->filter()
            ->unique()
            ->values();


        $totalTagihan = Tagihan::whereIn(
            'id',
            $tagihanIds
        )->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            0,
            $totalTagihan - $totalPembayaran
        );


        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'admin.laporan.pdf',
            compact(
                'pembayaran',
                'bulan',
                'tahun',
                'totalPembayaran',
                'totalTagihan',
                'sisaTagihan'
            )
        );


        $pdf->setPaper(
            'A4',
            'landscape'
        );


        return $pdf->download(
            'laporan-pembayaran.pdf'
        );
    }


    /**
     * Export Excel
     */
    public function excel(Request $request)
    {
        return Excel::download(
            new LaporanExport(
                $request->bulan,
                $request->tahun
            ),
            'laporan-pembayaran.xlsx'
        );
    }
}