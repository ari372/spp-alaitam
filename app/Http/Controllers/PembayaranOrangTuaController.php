<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranOrangTuaController extends Controller
{
    /**
     * Menampilkan daftar tagihan
     */
    public function index()
    {
        $user = Auth::user();

        $orangTua = $user->orangTua;

        if (!$orangTua) {
            abort(404, 'Data orang tua tidak ditemukan.');
        }

        $siswa = $orangTua->siswa;

        $siswaIds = $siswa->pluck('id');


        $tagihan = Tagihan::with([
            'siswa',
            'tahunAjaran',
            'kategori',
            'pembayaran'
        ])
        ->whereIn('siswa_id', $siswaIds)
        ->orderByDesc('tahun_ajaran_id')
        ->get();


        return view(
            'orangtua.pembayaran.index',
            compact(
                'tagihan',
                'siswa'
            )
        );
    }


    /**
     * Form pembayaran
     */
    public function create(Tagihan $tagihan)
    {
        $user = Auth::user();

        $orangTua = $user->orangTua;

        if (!$orangTua) {
            abort(404);
        }


        // Pastikan tagihan memang milik anak
        $milikOrangTua = $orangTua->siswa()
            ->where('id', $tagihan->siswa_id)
            ->exists();

        if (!$milikOrangTua) {
            abort(403);
        }


        $tagihan->load([
            'siswa',
            'tahunAjaran',
            'kategori',
            'pembayaran'
        ]);


        // ============================
        // HITUNG YANG SUDAH DIBAYAR
        // ============================

        $totalDibayar = $tagihan->pembayaran
            ->where('status', 'dibayar')
            ->sum('nominal');


        $sisaTagihan =
            $tagihan->nominal - $totalDibayar;


        if ($sisaTagihan <= 0) {

            return redirect()
                ->route('orangtua.pembayaran.index')
                ->with(
                    'error',
                    'Tagihan ini sudah lunas.'
                );
        }


        // ============================
        // CEK PEMBAYARAN MENUNGGU
        // ============================

        $sedangDiproses =
            $tagihan->pembayaran
                ->where('status', 'menunggu')
                ->count();


        if ($sedangDiproses > 0) {

            return redirect()
                ->route('orangtua.pembayaran.index')
                ->with(
                    'error',
                    'Pembayaran untuk tagihan ini sedang menunggu persetujuan admin.'
                );
        }


        // ============================
        // ATURAN PTS / UJIAN
        // ============================

        $namaKategori =
            strtolower(
                $tagihan->kategori->nama ?? ''
            );


        if (
            str_contains($namaKategori, 'pts') ||
            str_contains($namaKategori, 'ujian')
        ) {

            $spp = Tagihan::where(
                'siswa_id',
                $tagihan->siswa_id
            )
            ->where(
                'tahun_ajaran_id',
                $tagihan->tahun_ajaran_id
            )
            ->whereHas('kategori', function ($query) {

                $query->where(
                    'nama',
                    'SPP'
                );

            })
            ->first();


            if ($spp) {

                $totalSPPDibayar =
                    PembayaranTagihan::where(
                        'tagihan_id',
                        $spp->id
                    )
                    ->where(
                        'status',
                        'dibayar'
                    )
                    ->sum('nominal');


                $sisaSPP =
                    $spp->nominal -
                    $totalSPPDibayar;


                if ($sisaSPP > 0) {

                    return redirect()
                        ->route(
                            'orangtua.pembayaran.index'
                        )
                        ->with(
                            'error',
                            'SPP harus lunas terlebih dahulu sebelum membayar PTS/Ujian.'
                        );
                }
            }
        }


        return view(
            'orangtua.pembayaran.create',
            compact(
                'tagihan',
                'totalDibayar',
                'sisaTagihan'
            )
        );
    }


    /**
     * Simpan pembayaran
     */
    public function store(
        Request $request,
        Tagihan $tagihan
    ) {
        $user = Auth::user();

        $orangTua = $user->orangTua;


        if (!$orangTua) {
            abort(404);
        }


        // Pastikan tagihan milik anak
        $milikOrangTua =
            $orangTua->siswa()
                ->where(
                    'id',
                    $tagihan->siswa_id
                )
                ->exists();


        if (!$milikOrangTua) {
            abort(403);
        }


        // ============================
        // VALIDASI
        // ============================

        $validated = $request->validate([

            'nominal' => [
                'required',
                'numeric',
                'min:1'
            ],

            'metode' => [
                'required',
                'in:transfer,qris'
            ],

            'bukti_pembayaran' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],

        ]);


        // ============================
        // HITUNG SISA TAGIHAN
        // ============================

        $totalDibayar =
            $tagihan->pembayaran()
                ->where(
                    'status',
                    'dibayar'
                )
                ->sum('nominal');


        $sisaTagihan =
            $tagihan->nominal -
            $totalDibayar;


        if ($sisaTagihan <= 0) {

            return back()
                ->with(
                    'error',
                    'Tagihan sudah lunas.'
                );
        }


        // Tidak boleh membayar melebihi sisa
        if (
            $validated['nominal'] >
            $sisaTagihan
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Nominal pembayaran melebihi sisa tagihan.'
                );
        }


        // ============================
        // CEK PEMBAYARAN MENUNGGU
        // ============================

        $menunggu =
            $tagihan->pembayaran()
                ->where(
                    'status',
                    'menunggu'
                )
                ->exists();


        if ($menunggu) {

            return back()
                ->with(
                    'error',
                    'Masih ada pembayaran yang menunggu persetujuan admin.'
                );
        }


        // ============================
        // CEK SPP UNTUK PTS / UJIAN
        // ============================

        $namaKategori =
            strtolower(
                $tagihan->kategori->nama ?? ''
            );


        if (
            str_contains($namaKategori, 'pts') ||
            str_contains($namaKategori, 'ujian')
        ) {

            $spp = Tagihan::where(
                'siswa_id',
                $tagihan->siswa_id
            )
            ->where(
                'tahun_ajaran_id',
                $tagihan->tahun_ajaran_id
            )
            ->whereHas('kategori', function ($query) {

                $query->where(
                    'nama',
                    'SPP'
                );

            })
            ->first();


            if ($spp) {

                $totalSPP =
                    $spp->pembayaran()
                        ->where(
                            'status',
                            'dibayar'
                        )
                        ->sum('nominal');


                $sisaSPP =
                    $spp->nominal -
                    $totalSPP;


                if ($sisaSPP > 0) {

                    return back()
                        ->with(
                            'error',
                            'SPP harus lunas terlebih dahulu sebelum membayar PTS/Ujian.'
                        );
                }
            }
        }


        // ============================
        // UPLOAD BUKTI
        // ============================

        $file =
            $request->file(
                'bukti_pembayaran'
            );


        $path =
            $file->store(
                'bukti-pembayaran',
                'public'
            );


        // ============================
        // SIMPAN PEMBAYARAN
        // ============================

        PembayaranTagihan::create([

            'tagihan_id' =>
                $tagihan->id,

            'user_id' =>
                $user->id,

            'nominal' =>
                $validated['nominal'],

            'metode' =>
                $validated['metode'],

            'bukti_pembayaran' =>
                $path,

            'status' =>
                'menunggu',

            'tanggal_kirim' =>
                now(),

        ]);


        return redirect()
            ->route(
                'orangtua.pembayaran.index'
            )
            ->with(
                'success',
                'Bukti pembayaran berhasil dikirim. Silakan menunggu persetujuan admin.'
            );
    }
}