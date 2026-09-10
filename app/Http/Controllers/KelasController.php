<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Menampilkan semua kelas
     */
    public function index()
    {
        $kelas = Kelas::latest()->get();

        return view(
            'admin.kelas.index',
            compact('kelas')
        );
    }


    /**
     * Form tambah kelas
     */
    public function create()
    {
        return view('admin.kelas.create');
    }


    /**
     * Simpan kelas
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                'unique:kelas,nama_kelas',
            ],
        ]);

        Kelas::create($validated);

        return redirect()
            ->route('admin.kelas.index')
            ->with(
                'success',
                'Data kelas berhasil ditambahkan.'
            );
    }


    /**
     * Form edit kelas
     */
    public function edit(Kelas $kela)
    {
        return view(
            'admin.kelas.edit',
            compact('kela')
        );
    }


    /**
     * Update kelas
     */
    public function update(
        Request $request,
        Kelas $kela
    ) {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                'unique:kelas,nama_kelas,' . $kela->id,
            ],
        ]);

        $kela->update($validated);

        return redirect()
            ->route('admin.kelas.index')
            ->with(
                'success',
                'Data kelas berhasil diperbarui.'
            );
    }


    /**
     * Hapus kelas
     */
    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()
            ->route('admin.kelas.index')
            ->with(
                'success',
                'Data kelas berhasil dihapus.'
            );
    }
}