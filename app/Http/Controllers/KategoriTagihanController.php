<?php

namespace App\Http\Controllers;

use App\Models\KategoriTagihan;
use Illuminate\Http\Request;

class KategoriTagihanController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     */
    public function index()
    {
        $kategori = KategoriTagihan::orderBy('id', 'asc')->get();

        return view('admin.kategori.index', compact('kategori'));
    }


    /**
     * Menampilkan form tambah kategori.
     */
    public function create()
    {
        return view('admin.kategori.create');
    }


    /**
     * Menyimpan kategori baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
            ],

            'nominal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        KategoriTagihan::create($validated);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori pembayaran berhasil ditambahkan.');
    }


    /**
     * Menampilkan form edit kategori.
     */
    public function edit(KategoriTagihan $kategori)
    {
        return view(
            'admin.kategori.edit',
            compact('kategori')
        );
    }


    /**
     * Memperbarui kategori.
     */
    public function update(
        Request $request,
        KategoriTagihan $kategori
    ) {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
            ],

            'nominal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $kategori->update($validated);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori pembayaran berhasil diperbarui.');
    }


    /**
     * Menghapus kategori.
     */
    public function destroy(KategoriTagihan $kategori)
    {
        $kategori->delete();

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori pembayaran berhasil dihapus.');
    }
}