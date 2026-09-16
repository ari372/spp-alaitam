<?php

namespace App\Http\Controllers;

use App\Models\PembayaranTagihan;
use App\Models\Tagihan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranAdminController extends Controller
{
    /**
     * ============================================================
     * MENAMPILKAN PEMBAYARAN YANG MENUNGGU PERSETUJUAN
     * ============================================================
     */
    public function index()
    {
        $pembayaran = PembayaranTagihan::with([
            'tagihan.siswa',
            'tagihan.kategori',
            'tagihan.tahunAjaran',
            'user'
        ])
        ->where('status', 'menunggu')
        ->orderByDesc('tanggal_kirim')
        ->get();

        return view(
            'admin.pembayaran.index',
            compact('pembayaran')
        );
    }


    /**
     * ============================================================
     * FORM PEMBAYARAN MANUAL / CASH
     * ============================================================
     */
    public function createManual()
    {
        $siswa = Siswa::orderBy('nama')->get();

        $tagihan = Tagihan::with([
            'siswa',
            'kategori',
            'tahunAjaran'
        ])
        ->orderByDesc('id')
        ->get();

        return view(
            'admin.pembayaran.manual',
            compact(
                'siswa',
                'tagihan'
            )
        );
    }


    /**
     * ============================================================
     * SIMPAN PEMBAYARAN MANUAL / CASH
     * ============================================================
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'tagihan_id' => [
                'required',
                'exists:tagihan,id'
            ],

            'nominal' => [
                'required',
                'numeric',
                'min:1',
            ],

            'tanggal_kirim' => [
                'required',
                'date',
            ],

        ], [

            'tagihan_id.required' =>
                'Tagihan harus dipilih.',

            'tagihan_id.exists' =>
                'Tagihan tidak ditemukan.',

            'nominal.required' =>
                'Nominal pembayaran harus diisi.',

            'nominal.numeric' =>
                'Nominal pembayaran harus berupa angka.',

            'nominal.min' =>
                'Nominal pembayaran harus lebih dari 0.',

            'tanggal_kirim.required' =>
                'Tanggal pembayaran harus diisi.',

            'tanggal_kirim.date' =>
                'Tanggal pembayaran tidak valid.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | CARI TAGIHAN
        |--------------------------------------------------------------------------
        */

        $tagihan = Tagihan::findOrFail(
            $request->tagihan_id
        );


        /*
        |--------------------------------------------------------------------------
        | HITUNG PEMBAYARAN YANG SUDAH DIBAYAR
        |--------------------------------------------------------------------------
        */

        $sudahDibayar = PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
        ->whereIn('status', [
            'dibayar',
            'disetujui',
        ])
        ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | HITUNG SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            (float) $tagihan->nominal
            - (float) $sudahDibayar,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | CEK TAGIHAN SUDAH LUNAS
        |--------------------------------------------------------------------------
        */

        if ($sisaTagihan <= 0) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Tagihan ini sudah lunas.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK NOMINAL TIDAK MELEBIHI SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        if (
            (float) $request->nominal
            > $sisaTagihan
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Nominal pembayaran melebihi sisa tagihan. Sisa tagihan: Rp ' .
                    number_format(
                        $sisaTagihan,
                        0,
                        ',',
                        '.'
                    )
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PEMBAYARAN CASH
        |--------------------------------------------------------------------------
        */

        PembayaranTagihan::create([
            'tagihan_id' =>
                $tagihan->id,

            'user_id' =>
                Auth::id(),

            'nominal' =>
                $request->nominal,

            'metode' =>
                'cash',

            'tanggal_kirim' =>
                $request->tanggal_kirim,

            'status' =>
                'dibayar',

            'tanggal_disetujui' =>
                now(),
        ]);


        return redirect()
            ->route('admin.pembayaran.index')
            ->with(
                'success',
                'Pembayaran cash berhasil dicatat.'
            );
    }


    /**
     * ============================================================
     * JUMLAH NOTIFIKASI PEMBAYARAN
     * ============================================================
     */
    public function notifikasi()
    {
        $jumlah = PembayaranTagihan::where(
            'status',
            'menunggu'
        )->count();

        return response()->json([
            'jumlah' => $jumlah
        ]);
    }


    /**
     * ============================================================
     * FORM KOREKSI PEMBAYARAN
     * ============================================================
     */
    public function edit(
        PembayaranTagihan $pembayaran
    ) {
        /*
        |--------------------------------------------------------------------------
        | LOAD DATA PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $pembayaran->load([
            'tagihan.siswa',
            'tagihan.kategori',
            'tagihan.tahunAjaran',
        ]);


        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN HARUS MASIH MENUNGGU
        |--------------------------------------------------------------------------
        */

        if ($pembayaran->status !== 'menunggu') {
            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Pembayaran yang sudah diproses tidak dapat dikoreksi.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK TAGIHAN
        |--------------------------------------------------------------------------
        */

        $tagihan = $pembayaran->tagihan;

        if (!$tagihan) {
            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Tagihan tidak ditemukan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG PEMBAYARAN YANG SUDAH DIBAYAR
        |--------------------------------------------------------------------------
        */

        $sudahDibayar = PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
        ->whereIn('status', [
            'dibayar',
            'disetujui',
        ])
        ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | HITUNG SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            (float) $tagihan->nominal
            - (float) $sudahDibayar,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN FORM KOREKSI
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.pembayaran.edit',
            compact(
                'pembayaran',
                'tagihan',
                'sudahDibayar',
                'sisaTagihan'
            )
        );
    }


    /**
     * ============================================================
     * UPDATE / SIMPAN KOREKSI PEMBAYARAN
     * ============================================================
     */
    public function update(
        Request $request,
        PembayaranTagihan $pembayaran
    ) {
        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN HARUS MASIH MENUNGGU
        |--------------------------------------------------------------------------
        */

        if ($pembayaran->status !== 'menunggu') {
            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Pembayaran yang sudah diproses tidak dapat dikoreksi.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'nominal' => [
                'required',
                'numeric',
                'min:1',
            ],

            'metode' => [
                'required',
                'in:transfer,qris',
            ],

        ], [

            'nominal.required' =>
                'Nominal pembayaran harus diisi.',

            'nominal.numeric' =>
                'Nominal pembayaran harus berupa angka.',

            'nominal.min' =>
                'Nominal pembayaran harus lebih dari 0.',

            'metode.required' =>
                'Metode pembayaran harus dipilih.',

            'metode.in' =>
                'Metode pembayaran tidak valid.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | LOAD TAGIHAN
        |--------------------------------------------------------------------------
        */

        $pembayaran->load('tagihan');

        $tagihan = $pembayaran->tagihan;

        if (!$tagihan) {
            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Tagihan tidak ditemukan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG PEMBAYARAN YANG SUDAH DIBAYAR
        |--------------------------------------------------------------------------
        */

        $sudahDibayar = PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
        ->whereIn('status', [
            'dibayar',
            'disetujui',
        ])
        ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | HITUNG SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            (float) $tagihan->nominal
            - (float) $sudahDibayar,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | NOMINAL TIDAK BOLEH MELEBIHI SISA
        |--------------------------------------------------------------------------
        */

        if (
            (float) $request->nominal
            > $sisaTagihan
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Nominal pembayaran tidak boleh melebihi sisa tagihan. Sisa tagihan: Rp ' .
                    number_format(
                        $sisaTagihan,
                        0,
                        ',',
                        '.'
                    )
                );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $pembayaran->update([
            'nominal' =>
                $request->nominal,

            'metode' =>
                $request->metode,
        ]);


        return redirect()
            ->route('admin.pembayaran.index')
            ->with(
                'success',
                'Nominal pembayaran berhasil dikoreksi.'
            );
    }


    /**
     * ============================================================
     * MENYETUJUI PEMBAYARAN
     * ============================================================
     */
    public function setujui(
        PembayaranTagihan $pembayaran
    ) {
        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN HARUS MASIH MENUNGGU
        |--------------------------------------------------------------------------
        */

        if ($pembayaran->status !== 'menunggu') {
            return back()->with(
                'error',
                'Pembayaran ini sudah diproses.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NOMINAL HARUS SUDAH DITENTUKAN
        |--------------------------------------------------------------------------
        */

        if (
            $pembayaran->nominal === null ||
            (float) $pembayaran->nominal <= 0
        ) {
            return back()->with(
                'error',
                'Nominal pembayaran belum ditentukan. Silakan lakukan koreksi terlebih dahulu.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD TAGIHAN
        |--------------------------------------------------------------------------
        */

        $pembayaran->load('tagihan');

        if (!$pembayaran->tagihan) {
            return back()->with(
                'error',
                'Tagihan pembayaran tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG PEMBAYARAN SEBELUMNYA
        |--------------------------------------------------------------------------
        */

        $sudahDibayar = PembayaranTagihan::where(
            'tagihan_id',
            $pembayaran->tagihan_id
        )
        ->whereIn('status', [
            'dibayar',
            'disetujui',
        ])
        ->where(
            'id',
            '!=',
            $pembayaran->id
        )
        ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | HITUNG SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            (float) $pembayaran->tagihan->nominal
            - (float) $sudahDibayar,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | NOMINAL TIDAK BOLEH MELEBIHI SISA
        |--------------------------------------------------------------------------
        */

        if (
            (float) $pembayaran->nominal
            > $sisaTagihan
        ) {
            return back()->with(
                'error',
                'Nominal pembayaran melebihi sisa tagihan. Sisa tagihan: Rp ' .
                number_format(
                    $sisaTagihan,
                    0,
                    ',',
                    '.'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SETUJUI PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $pembayaran->update([
            'status' =>
                'dibayar',

            'tanggal_disetujui' =>
                now(),
        ]);


        return back()->with(
            'success',
            'Pembayaran berhasil disetujui.'
        );
    }


    /**
     * ============================================================
     * MENOLAK PEMBAYARAN
     * ============================================================
     */
    public function tolak(
        Request $request,
        PembayaranTagihan $pembayaran
    ) {
        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN HARUS MASIH MENUNGGU
        |--------------------------------------------------------------------------
        */

        if ($pembayaran->status !== 'menunggu') {
            return back()->with(
                'error',
                'Pembayaran ini sudah diproses.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI CATATAN
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'catatan' => [
                'required',
                'string',
                'max:500',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | TOLAK PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $pembayaran->update([
            'status' =>
                'ditolak',

            'catatan' =>
                $request->catatan,
        ]);


        return back()->with(
            'success',
            'Pembayaran ditolak.'
        );
    }
}