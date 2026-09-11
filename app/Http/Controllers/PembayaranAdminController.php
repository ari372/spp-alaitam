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
     * Menampilkan pembayaran yang menunggu persetujuan
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
     * Form pembayaran manual / cash
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
            compact('siswa', 'tagihan')
        );
    }


    /**
     * Menyimpan pembayaran manual / cash
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihans,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal_kirim' => 'required|date',
        ], [
            'tagihan_id.required' => 'Tagihan harus dipilih.',
            'tagihan_id.exists' => 'Tagihan tidak ditemukan.',
            'nominal.required' => 'Nominal pembayaran harus diisi.',
            'nominal.numeric' => 'Nominal pembayaran harus berupa angka.',
            'nominal.min' => 'Nominal pembayaran harus lebih dari 0.',
            'tanggal_kirim.required' => 'Tanggal pembayaran harus diisi.',
            'tanggal_kirim.date' => 'Tanggal pembayaran tidak valid.',
        ]);

        $tagihan = Tagihan::findOrFail($request->tagihan_id);

        /*
        |--------------------------------------------------------------------------
        | Hitung pembayaran yang sudah dibayar
        |--------------------------------------------------------------------------
        */

        $sudahDibayar = PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
        ->where('status', 'dibayar')
        ->sum('nominal');


        $sisaTagihan = $tagihan->nominal - $sudahDibayar;


        /*
        |--------------------------------------------------------------------------
        | Cek tagihan sudah lunas
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
        | Cek nominal tidak melebihi sisa tagihan
        |--------------------------------------------------------------------------
        */

        if ($request->nominal > $sisaTagihan) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Nominal pembayaran melebihi sisa tagihan. Sisa tagihan: Rp ' .
                    number_format($sisaTagihan, 0, ',', '.')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan pembayaran cash
        |--------------------------------------------------------------------------
        */

        PembayaranTagihan::create([
            'tagihan_id' => $tagihan->id,
            'user_id' => Auth::id(),
            'nominal' => $request->nominal,
            'metode' => 'cash',
            'tanggal_kirim' => $request->tanggal_kirim,
            'status' => 'dibayar',
            'tanggal_disetujui' => now(),
        ]);


        return redirect()
            ->route('admin.pembayaran.index')
            ->with(
                'success',
                'Pembayaran cash berhasil dicatat.'
            );
    }


    /**
     * Jumlah notifikasi pembayaran
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
     * Menyetujui pembayaran
     */
    public function setujui(
        PembayaranTagihan $pembayaran
    ) {
        if ($pembayaran->status !== 'menunggu') {

            return back()->with(
                'error',
                'Pembayaran ini sudah diproses.'
            );
        }

        $pembayaran->update([
            'status' => 'dibayar',
            'tanggal_disetujui' => now(),
        ]);

        return back()->with(
            'success',
            'Pembayaran berhasil disetujui.'
        );
    }


    /**
     * Menolak pembayaran
     */
    public function tolak(
        Request $request,
        PembayaranTagihan $pembayaran
    ) {
        if ($pembayaran->status !== 'menunggu') {

            return back()->with(
                'error',
                'Pembayaran ini sudah diproses.'
            );
        }

        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $pembayaran->update([
            'status' => 'ditolak',
            'catatan' => $request->catatan,
        ]);

        return back()->with(
            'success',
            'Pembayaran ditolak.'
        );
    }
}