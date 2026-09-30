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
        $pengaturan = PengaturanPembayaran::first();

        // Jika pengaturan belum tersedia,
        // buat pengaturan default.
        if (!$pengaturan) {
            $pengaturan = PengaturanPembayaran::create([
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
     * Menyimpan perubahan pengaturan pembayaran.
     */
    public function update(Request $request)
    {
        $validated = $request->validate(
            [
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
            ],
            [
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
            ]
        );

        $pengaturan = PengaturanPembayaran::first();

        // Jika belum ada data, buat data baru.
        if (!$pengaturan) {
            $pengaturan = new PengaturanPembayaran();
        }

        $pengaturan->tanggal_jatuh_tempo =
            $validated['tanggal_jatuh_tempo'];

        $pengaturan->hari_pengingat =
            $validated['hari_pengingat'];

        // Checkbox aktif:
        // dicentang = true
        // tidak dicentang = false
        $pengaturan->aktif = $request->has('aktif');

        $pengaturan->save();

        /*
        |--------------------------------------------------------------------------
        | UPDATE JATUH TEMPO SEMUA TAGIHAN
        |--------------------------------------------------------------------------
        |
        | Jika pengaturan aktif, semua tagihan yang sudah ada
        | akan mengikuti tanggal jatuh tempo dari pengaturan.
        |
        | Contoh:
        | Pengaturan 5 -> semua tagihan menjadi tanggal 5
        | Pengaturan 2 -> semua tagihan menjadi tanggal 2
        |
        */

        if ($pengaturan->aktif) {

            $tanggalJatuhTempo =
                (int) $pengaturan->tanggal_jatuh_tempo;

            $tagihan = Tagihan::all();

            foreach ($tagihan as $item) {

                if (!$item->jatuh_tempo) {
                    continue;
                }

                $tanggalLama = Carbon::parse(
                    $item->jatuh_tempo
                );

                /*
                |--------------------------------------------------------------------------
                | Gunakan bulan dan tahun dari tagihan
                |--------------------------------------------------------------------------
                */

                $tanggalBaru = min(
                    $tanggalJatuhTempo,
                    $tanggalLama->daysInMonth
                );

                $tanggalLama->setDay($tanggalBaru);

                $item->jatuh_tempo =
                    $tanggalLama->format('Y-m-d');

                $item->save();
            }
        }

        return redirect()
            ->route('admin.pengaturan-pembayaran.index')
            ->with(
                'success',
                'Pengaturan pembayaran berhasil diperbarui dan tanggal jatuh tempo tagihan telah disesuaikan.'
            );
    }
}