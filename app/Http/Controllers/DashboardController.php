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
            'kategori',
            'tahunAjaran',
            'pembayaran'
        ])->get();


        // =================================================
        // TOTAL SELURUH TAGIHAN
        // =================================================

        $totalTagihan = $tagihan->sum(function ($item) {
            return (float) $item->nominal;
        });


        // =================================================
        // TOTAL PEMBAYARAN
        // =================================================
        //
        // dibayar   = pembayaran yang sudah disetujui
        // disetujui = jika ada status tersebut
        //
        // =================================================

        $totalPembayaran = PembayaranTagihan::whereIn(
            'status',
            [
                'dibayar',
                'disetujui'
            ]
        )->sum('nominal');

        $totalPembayaran = (float) $totalPembayaran;


        // =================================================
        // SISA TAGIHAN
        // =================================================

        $sisaTagihan = max(
            $totalTagihan - $totalPembayaran,
            0
        );


        // =================================================
        // STATUS SISWA
        // =================================================

        $lunas = 0;

        $belumLunas = 0;

        $terlambat = 0;


        // =================================================
        // TANGGAL HARI INI
        // =================================================

        $hariIni = Carbon::today();


        // =================================================
        // KELOMPOKKAN TAGIHAN BERDASARKAN SISWA
        // =================================================

        $tagihanPerSiswa = $tagihan->groupBy('siswa_id');


        // =================================================
        // CEK STATUS SETIAP SISWA
        // =================================================

        foreach ($tagihanPerSiswa as $siswaId => $tagihanSiswa) {

            // Awalnya dianggap lunas
            $semuaLunas = true;

            // Ada tagihan belum lunas
            $adaBelumLunas = false;

            // Ada tagihan yang benar-benar terlambat
            $adaTerlambat = false;


            // =================================================
            // CEK SETIAP TAGIHAN
            // =================================================

            foreach ($tagihanSiswa as $item) {

                // ---------------------------------------------
                // NOMINAL TAGIHAN
                // ---------------------------------------------

                $nominalTagihan = (float) $item->nominal;


                // ---------------------------------------------
                // TOTAL PEMBAYARAN YANG SUDAH SAH
                // ---------------------------------------------

                $dibayar = $item->pembayaran
                    ->filter(function ($pembayaran) {

                        $status = strtolower(
                            trim(
                                (string) $pembayaran->status
                            )
                        );

                        return in_array(
                            $status,
                            [
                                'dibayar',
                                'disetujui'
                            ]
                        );

                    })
                    ->sum(function ($pembayaran) {

                        return (float) $pembayaran->nominal;

                    });


                // ---------------------------------------------
                // HITUNG SISA
                // ---------------------------------------------

                $sisa = max(
                    $nominalTagihan - $dibayar,
                    0
                );


                // =================================================
                // JIKA TAGIHAN MASIH MEMILIKI SISA
                // =================================================

                if ($sisa > 0) {

                    $semuaLunas = false;

                    $adaBelumLunas = true;


                    // =================================================
                    // CEK JATUH TEMPO
                    // =================================================

                    if (!empty($item->jatuh_tempo)) {

                        try {

                            $jatuhTempo = Carbon::parse(
                                $item->jatuh_tempo
                            )->startOfDay();


                            // =================================================
                            // TERLAMBAT HANYA JIKA:
                            //
                            // JATUH TEMPO < HARI INI
                            //
                            // Contoh:
                            //
                            // Hari ini       : 09-09-2026
                            // Jatuh tempo    : 05-10-2026
                            //
                            // Maka:
                            // Belum Lunas
                            //
                            // BUKAN:
                            // Terlambat
                            // =================================================

                            if ($jatuhTempo->lt($hariIni)) {

                                $adaTerlambat = true;

                            }

                        } catch (\Exception $e) {

                            // Jika format tanggal tidak valid,
                            // jangan dianggap terlambat.

                        }

                    }

                }

            }


            // =================================================
            // TENTUKAN STATUS SISWA
            // =================================================

            if ($semuaLunas) {

                // Semua tagihan siswa sudah lunas

                $lunas++;

            } elseif ($adaTerlambat) {

                // Ada tagihan belum lunas
                // yang sudah melewati jatuh tempo

                $terlambat++;

            } elseif ($adaBelumLunas) {

                // Ada tagihan belum lunas
                // tetapi belum melewati jatuh tempo

                $belumLunas++;

            }

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
        ->orderByDesc('tanggal_kirim')
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

            $totalBulan = PembayaranTagihan::whereIn(
                'status',
                [
                    'dibayar',
                    'disetujui'
                ]
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


            $dataGrafik[] = (float) $totalBulan;

        }


        // =================================================
        // KIRIM DATA KE VIEW ADMIN
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

        $totalTagihan = $tagihan->sum(function ($item) {

            return (float) $item->nominal;

        });


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
            ->filter(function ($item) {

                $status = strtolower(
                    trim(
                        (string) $item->status
                    )
                );

                return in_array(
                    $status,
                    [
                        'dibayar',
                        'disetujui'
                    ]
                );

            })
            ->sum(function ($item) {

                return (float) $item->nominal;

            });


        // =================================================
        // SISA TAGIHAN
        // =================================================

        $sisaTagihan = max(
            $totalTagihan - $totalDibayar,
            0
        );


        // =================================================
        // KIRIM DATA KE VIEW
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