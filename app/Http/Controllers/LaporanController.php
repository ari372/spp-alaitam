<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan pembayaran
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun ?? date('Y');

        /*
        |--------------------------------------------------------------------------
        | DATA PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $query = Pembayaran::with([
            'siswa.kelas',
            'tagihan'
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER TAHUN
        |--------------------------------------------------------------------------
        */

        if ($tahun) {
            $query->whereYear(
                'tanggal_pembayaran',
                $tahun
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN
        |--------------------------------------------------------------------------
        */

        if ($bulan) {

            $bulanIndonesia = [
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

            if (isset($bulanIndonesia[$bulan])) {

                $query->whereMonth(
                    'tanggal_pembayaran',
                    $bulanIndonesia[$bulan]
                );
            }
        }

        $pembayaran = $query
            ->latest('tanggal_pembayaran')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $totalPembayaran = $pembayaran
            ->where('status', 'disetujui')
            ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAGIHAN
        |--------------------------------------------------------------------------
        |
        | Ambil tagihan yang berkaitan dengan pembayaran yang ditampilkan.
        | distinct digunakan agar satu tagihan tidak dihitung berkali-kali
        | apabila memiliki beberapa pembayaran.
        |
        */

        $tagihanIds = $pembayaran
            ->pluck('tagihan_id')
            ->filter()
            ->unique();

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
        $tahun = $request->tahun ?? date('Y');

        $query = Pembayaran::with([
            'siswa.kelas',
            'tagihan'
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER TAHUN
        |--------------------------------------------------------------------------
        */

        if ($tahun) {
            $query->whereYear(
                'tanggal_pembayaran',
                $tahun
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN
        |--------------------------------------------------------------------------
        */

        if ($bulan) {

            $bulanIndonesia = [
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

            if (isset($bulanIndonesia[$bulan])) {

                $query->whereMonth(
                    'tanggal_pembayaran',
                    $bulanIndonesia[$bulan]
                );
            }
        }

        $pembayaran = $query
            ->latest('tanggal_pembayaran')
            ->get();


        $totalPembayaran = $pembayaran
            ->where('status', 'disetujui')
            ->sum('nominal');


        $tagihanIds = $pembayaran
            ->pluck('tagihan_id')
            ->filter()
            ->unique();

        $totalTagihan = Tagihan::whereIn(
            'id',
            $tagihanIds
        )->sum('nominal');


        $sisaTagihan = max(
            0,
            $totalTagihan - $totalPembayaran
        );


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
            'a4',
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