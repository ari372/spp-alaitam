<?php

namespace App\Http\Controllers;

use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;

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