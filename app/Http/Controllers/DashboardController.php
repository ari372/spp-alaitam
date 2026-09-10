<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\OrangTua;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\PembayaranTagihan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // =====================================================
    // DASHBOARD ADMIN
    // =====================================================

    public function admin()
    {
        // =================================================
        // TOTAL DATA
        // =================================================

        $totalSiswa = Siswa::count();

        $totalOrangTua = OrangTua::count();

        $totalKelas = Kelas::count();


        // =================================================
        // AMBIL SEMUA TAGIHAN
        // =================================================

        $tagihan = Tagihan::with([
            'siswa',
            'pembayaran',
            'kategori',
            'tahunAjaran'
        ])->get();


        // =================================================
        // TOTAL NOMINAL TAGIHAN
        // =================================================

        $totalTagihan = $tagihan->sum('nominal');


        // =================================================
        // TOTAL PEMBAYARAN YANG SUDAH DISETUJUI
        // =================================================

        $totalPembayaran = PembayaranTagihan::where(
            'status',
            'disetujui'
        )->sum('nominal');


        // =================================================
        // SISA TAGIHAN
        // =================================================

        $sisaTagihan = max(
            $totalTagihan - $totalPembayaran,
            0
        );


        // =================================================
        // STATUS PEMBAYARAN SISWA
        // =================================================

        $lunas = 0;

        $belumLunas = 0;

        $terlambat = 0;


        foreach ($tagihan as $item) {

            // Total pembayaran tagihan ini
            $dibayar = $item->pembayaran
                ->where('status', 'disetujui')
                ->sum('nominal');


            // Sisa tagihan
            $sisa = max(
                $item->nominal - $dibayar,
                0
            );


            // =============================================
            // LUNAS
            // =============================================

            if ($sisa <= 0) {

                $lunas++;

                continue;
            }


            // =============================================
            // TERLAMBAT
            // =============================================

            if (
                $item->jatuh_tempo &&
                Carbon::parse($item->jatuh_tempo)->isPast()
            ) {

                $terlambat++;

                continue;
            }


            // =============================================
            // BELUM LUNAS
            // =============================================

            $belumLunas++;
        }


        // =================================================
        // PEMBAYARAN MENUNGGU PERSETUJUAN
        // =================================================

        $menungguPersetujuan = PembayaranTagihan::where(
            'status',
            'menunggu'
        )->count();


        // =================================================
        // PEMBAYARAN TERBARU
        // =================================================

        $pembayaranTerbaru = PembayaranTagihan::with([
            'tagihan.siswa',
            'tagihan.kategori',
            'tagihan.tahunAjaran'
        ])
        ->latest('tanggal_kirim')
        ->take(5)
        ->get();


        // =================================================
        // GRAFIK PEMBAYARAN
        // =================================================

        $labelGrafik = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];


        $dataGrafik = [];


        for ($bulan = 1; $bulan <= 12; $bulan++) {

            $totalBulan = PembayaranTagihan::where(
                'status',
                'disetujui'
            )
            ->whereYear(
                'tanggal_kirim',
                Carbon::now()->year
            )
            ->whereMonth(
                'tanggal_kirim',
                $bulan
            )
            ->sum('nominal');


            $dataGrafik[] = $totalBulan;
        }


        // =================================================
        // KIRIM DATA KE VIEW
        // =================================================

        return view(
            'admin.dashboard.index',
            compact(
                'totalSiswa',
                'totalOrangTua',
                'totalKelas',
                'totalTagihan',
                'totalPembayaran',
                'sisaTagihan',
                'lunas',
                'belumLunas',
                'terlambat',
                'menungguPersetujuan',
                'pembayaranTerbaru',
                'labelGrafik',
                'dataGrafik'
            )
        );
    }


    // =====================================================
    // DASHBOARD ORANG TUA
    // =====================================================

    public function orangTua()
    {
        $user = Auth::user();


        // =================================================
        // CARI DATA ORANG TUA
        // =================================================

        $orangTua = OrangTua::where(
            'user_id',
            $user->id
        )->first();


        // =================================================
        // JIKA DATA ORANG TUA TIDAK ADA
        // =================================================

        if (!$orangTua) {

            return view(
                'orangtua.dashboard',
                [
                    'siswa' => null,
                    'tagihan' => collect(),
                    'pembayaran' => collect(),
                    'totalTagihan' => 0,
                    'totalDibayar' => 0,
                    'sisaTagihan' => 0
                ]
            );
        }


        // =================================================
        // CARI DATA ANAK
        // =================================================

        $siswa = Siswa::with([
            'kelas',
            'orangTua',
            'tagihan.kategori',
            'tagihan.tahunAjaran',
            'tagihan.pembayaran'
        ])
        ->where(
            'orang_tua_id',
            $orangTua->id
        )
        ->first();


        // =================================================
        // JIKA DATA ANAK TIDAK ADA
        // =================================================

        if (!$siswa) {

            return view(
                'orangtua.dashboard',
                [
                    'siswa' => null,
                    'tagihan' => collect(),
                    'pembayaran' => collect(),
                    'totalTagihan' => 0,
                    'totalDibayar' => 0,
                    'sisaTagihan' => 0
                ]
            );
        }


        // =================================================
        // DATA TAGIHAN
        // =================================================

        $tagihan = $siswa->tagihan;


        // =================================================
        // TOTAL TAGIHAN
        // =================================================

        $totalTagihan = $tagihan->sum('nominal');


        // =================================================
        // DATA PEMBAYARAN
        // =================================================

        $pembayaran = PembayaranTagihan::with([
            'tagihan.kategori',
            'tagihan.tahunAjaran',
            'tagihan.siswa'
        ])
        ->whereHas('tagihan', function ($query) use ($siswa) {

            $query->where(
                'siswa_id',
                $siswa->id
            );

        })
        ->orderByDesc('tanggal_kirim')
        ->get();


        // =================================================
        // TOTAL SUDAH DIBAYAR
        // =================================================

        $totalDibayar = $pembayaran
            ->where('status', 'disetujui')
            ->sum('nominal');


        // =================================================
        // SISA TAGIHAN
        // =================================================

        $sisaTagihan = max(
            $totalTagihan - $totalDibayar,
            0
        );


        // =================================================
        // KIRIM KE VIEW ORANG TUA
        // =================================================

        return view(
            'orangtua.dashboard',
            compact(
                'siswa',
                'tagihan',
                'pembayaran',
                'totalTagihan',
                'totalDibayar',
                'sisaTagihan'
            )
        );
    }
}