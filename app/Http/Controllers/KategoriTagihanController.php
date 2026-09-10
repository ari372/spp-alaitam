<?php

namespace App\Http\Controllers;

use App\Models\KategoriTagihan;
use Illuminate\Http\Request;

class KategoriTagihanController extends Controller
{
    public function index()
    {
        $kategori = KategoriTagihan::orderBy('id', 'asc')->get();

        return view('admin.kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        KategoriTagihan::create([
            'nama' => $request->nama,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()
            ->route('admin.kategori')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroy(KategoriTagihan $kategori)
    {
        $kategori->delete();

        return redirect()
            ->route('admin.kategori')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}