<?php

namespace App\Http\Controllers;

use App\Models\PengaturanPembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengaturanPembayaranController extends Controller
{
    /**
     * Menampilkan halaman pengaturan pembayaran.
     */
    public function index()
    {
        $pengaturan =
            PengaturanPembayaran::first();

        if (!$pengaturan) {

            $pengaturan =
                PengaturanPembayaran::create([
                    'tanggal_jatuh_tempo' => 5,
                    'hari_pengingat' => 3,
                    'aktif' => true,
                ]);
        }

        return view(
            'admin.pembayaran.pengaturan',
            compact('pengaturan')
        );
    }


    /**
     * Update pengaturan pembayaran.
     */
    public function update(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'tanggal_jatuh_tempo' => [
                'required',
                'integer',
                'min:1',
                'max:31',
            ],

            'hari_pengingat' => [
                'required',
                'integer',
                'min:0',
                'max:30',
            ],
        ], [

            'tanggal_jatuh_tempo.required' =>
                'Tanggal jatuh tempo wajib diisi.',

            'tanggal_jatuh_tempo.integer' =>
                'Tanggal jatuh tempo harus berupa angka.',

            'tanggal_jatuh_tempo.min' =>
                'Tanggal jatuh tempo minimal tanggal 1.',

            'tanggal_jatuh_tempo.max' =>
                'Tanggal jatuh tempo maksimal tanggal 31.',

            'hari_pengingat.required' =>
                'Hari pengingat wajib diisi.',

            'hari_pengingat.integer' =>
                'Hari pengingat harus berupa angka.',

            'hari_pengingat.min' =>
                'Hari pengingat minimal 0 hari.',

            'hari_pengingat.max' =>
                'Hari pengingat maksimal 30 hari.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Ambil pengaturan
        |--------------------------------------------------------------------------
        */

        $pengaturan =
            PengaturanPembayaran::first();


        if (!$pengaturan) {

            $pengaturan =
                new PengaturanPembayaran();
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan pengaturan
        |--------------------------------------------------------------------------
        */

        $pengaturan->tanggal_jatuh_tempo =
            $validated['tanggal_jatuh_tempo'];

        $pengaturan->hari_pengingat =
            $validated['hari_pengingat'];

        $pengaturan->aktif =
            $request->has('aktif');

        $pengaturan->save();


        /*
        |--------------------------------------------------------------------------
        | Sinkronisasi tanggal jatuh tempo
        |--------------------------------------------------------------------------
        |
        | Jika pengaturan aktif:
        |
        | contoh:
        |
        | sebelumnya tanggal 5
        | menjadi tanggal 2
        |
        | maka:
        |
        | 2026-07-05 -> 2026-07-02
        | 2026-08-05 -> 2026-08-02
        | 2027-07-05 -> 2027-07-02
        |
        | Tahun dan bulan masing-masing tagihan
        | tetap dipertahankan.
        |
        */

        if ($pengaturan->aktif) {

            $tanggalBaru =
                (int) $pengaturan->tanggal_jatuh_tempo;


            Tagihan::with('tahunAjaran')
                ->chunkById(
                    500,
                    function ($tagihanList) use (
                        $tanggalBaru
                    ) {

                        foreach (
                            $tagihanList
                            as $tagihan
                        ) {

                            /*
                            |--------------------------------------------------------------------------
                            | Jika sudah mempunyai jatuh tempo,
                            | pertahankan tahun dan bulan.
                            |--------------------------------------------------------------------------
                            */

                            if ($tagihan->jatuh_tempo) {

                                $tanggalLama =
                                    Carbon::parse(
                                        $tagihan->jatuh_tempo
                                    );

                                $hari =
                                    min(
                                        $tanggalBaru,
                                        $tanggalLama->daysInMonth
                                    );

                                $tanggalLama->setDay(
                                    $hari
                                );

                                $tagihan->jatuh_tempo =
                                    $tanggalLama->format(
                                        'Y-m-d'
                                    );

                                $tagihan->save();

                                continue;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Jika jatuh tempo masih kosong,
                            | gunakan tanggal mulai tahun ajaran.
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $tagihan->tahunAjaran &&
                                $tagihan
                                    ->tahunAjaran
                                    ->tanggal_mulai
                            ) {

                                $tanggalMulai =
                                    Carbon::parse(
                                        $tagihan
                                            ->tahunAjaran
                                            ->tanggal_mulai
                                    );

                                $hari =
                                    min(
                                        $tanggalBaru,
                                        $tanggalMulai->daysInMonth
                                    );

                                $tagihan->jatuh_tempo =
                                    $tanggalMulai
                                        ->copy()
                                        ->startOfDay()
                                        ->setDay($hari)
                                        ->format('Y-m-d');

                                $tagihan->save();
                            }
                        }
                    }
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Kembali
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.pengaturan-pembayaran.index'
            )
            ->with(
                'success',
                'Pengaturan pembayaran berhasil diperbarui dan tanggal jatuh tempo tagihan telah disinkronkan.'
            );
    }
}