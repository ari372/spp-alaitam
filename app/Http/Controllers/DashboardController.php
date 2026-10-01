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

            $semuaLunas = true;
            $adaBelumLunas = false;
            $adaTerlambat = false;

            foreach ($tagihanSiswa as $item) {

                // -----------------------------------------
                // NOMINAL TAGIHAN
                // -----------------------------------------

                $nominalTagihan = (float) $item->nominal;

                // -----------------------------------------
                // TOTAL PEMBAYARAN SAH
                // -----------------------------------------

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

                // -----------------------------------------
                // HITUNG SISA
                // -----------------------------------------

                $sisa = max(
                    $nominalTagihan - $dibayar,
                    0
                );

                // -----------------------------------------
                // JIKA BELUM LUNAS
                // -----------------------------------------

                if ($sisa > 0) {

                    $semuaLunas = false;
                    $adaBelumLunas = true;

                    // -------------------------------------
                    // CEK JATUH TEMPO
                    // -------------------------------------

                    if (!empty($item->jatuh_tempo)) {

                        try {

                            $jatuhTempo = Carbon::parse(
                                $item->jatuh_tempo
                            )->startOfDay();

                            if ($jatuhTempo->lt($hariIni)) {
                                $adaTerlambat = true;
                            }

                        } catch (\Exception $e) {
                            // Jangan dianggap terlambat
                            // jika tanggal tidak valid.
                        }
                    }
                }
            }

            // =================================================
            // TENTUKAN STATUS SISWA
            // =================================================

            if ($semuaLunas) {

                $lunas++;

            } elseif ($adaTerlambat) {

                $terlambat++;

            } elseif ($adaBelumLunas) {

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
        // SEMUA TAGIHAN ANAK
        // =================================================

        $semuaTagihan = $siswa->tagihan;

        // =================================================
        // KELOMPOKKAN SPP
        // BERDASARKAN TAHUN AJARAN
        // =================================================

        $tagihanSppPerTahun = $semuaTagihan
            ->filter(function ($item) {

                return strtolower(
                    trim(
                        $item->kategori->nama ?? ''
                    )
                ) === 'spp';

            })
            ->groupBy('tahun_ajaran_id');

        // =================================================
        // HITUNG TOTAL SPP DAN PEMBAYARAN
        // =================================================

        $totalSpp = 0;
        $totalSppDibayar = 0;

        foreach ($tagihanSppPerTahun as $tahunAjaranId => $tagihanSpp) {

            // ---------------------------------------------
            // TOTAL NOMINAL SPP
            // ---------------------------------------------

            $totalSppTahun = $tagihanSpp->sum(
                function ($item) {
                    return (float) $item->nominal;
                }
            );

            $totalSpp += $totalSppTahun;

            // ---------------------------------------------
            // TOTAL PEMBAYARAN SPP
            // ---------------------------------------------

            foreach ($tagihanSpp as $spp) {

                $dibayar = $spp->pembayaran
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

                $totalSppDibayar += $dibayar;
            }
        }

        // =================================================
        // SISA TOTAL SPP
        // =================================================

        $sisaSpp = max(
            $totalSpp - $totalSppDibayar,
            0
        );

        // =================================================
        // BUAT TAGIHAN AKTIF
        // =================================================
        //
        // SPP:
        // Jika satu tahun ajaran sudah lunas,
        // seluruh 12 tagihan SPP tidak ditampilkan
        // sebagai tagihan aktif.
        //
        // Jika belum lunas:
        // tagihan SPP tetap ditampilkan.
        //
        // NON-SPP:
        // Tetap ditampilkan sesuai sisa masing-masing.
        // =================================================

        $tagihan = collect();

        foreach ($semuaTagihan->groupBy('tahun_ajaran_id') as $tahunAjaranId => $tagihanTahun) {

            // ---------------------------------------------
            // AMBIL TAGIHAN SPP TAHUN INI
            // ---------------------------------------------

            $sppTahun = $tagihanTahun
                ->filter(function ($item) {

                    return strtolower(
                        trim(
                            $item->kategori->nama ?? ''
                        )
                    ) === 'spp';

                });

            // ---------------------------------------------
            // HITUNG TOTAL SPP TAHUN INI
            // ---------------------------------------------

            $totalSppTahun = $sppTahun->sum(
                function ($item) {
                    return (float) $item->nominal;
                }
            );

            // ---------------------------------------------
            // HITUNG PEMBAYARAN SPP TAHUN INI
            // ---------------------------------------------

            $dibayarSppTahun = 0;

            foreach ($sppTahun as $spp) {

                $dibayarSppTahun += $spp->pembayaran
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
            }

            $sisaSppTahun = max(
                $totalSppTahun - $dibayarSppTahun,
                0
            );

            // ---------------------------------------------
            // JIKA SPP BELUM LUNAS
            // ---------------------------------------------

            if ($sisaSppTahun > 0) {

                foreach ($sppTahun as $spp) {

                    $tagihan->push($spp);
                }
            }

            // ---------------------------------------------
            // TAGIHAN NON-SPP
            // ---------------------------------------------

            $nonSpp = $tagihanTahun->filter(
                function ($item) {

                    return strtolower(
                        trim(
                            $item->kategori->nama ?? ''
                        )
                    ) !== 'spp';
                }
            );

            foreach ($nonSpp as $item) {

                $nominalTagihan = (float) $item->nominal;

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

                $sisa = max(
                    $nominalTagihan - $dibayar,
                    0
                );

                // Hanya tampilkan tagihan
                // yang masih memiliki sisa.
                if ($sisa > 0) {

                    $tagihan->push($item);
                }
            }
        }

        // =================================================
        // URUTKAN TAGIHAN
        // =================================================

        $tagihan = $tagihan
            ->sortByDesc(function ($item) {

                return optional(
                    $item->tahunAjaran
                )->tanggal_mulai
                    ?? '0000-00-00';

            })
            ->values();

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
        // TOTAL SELURUH TAGIHAN
        // =================================================
        //
        // Untuk dashboard, total tagihan tetap
        // berdasarkan seluruh kewajiban anak.
        // =================================================

        $totalTagihan = $semuaTagihan->sum(
            function ($item) {
                return (float) $item->nominal;
            }
        );

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