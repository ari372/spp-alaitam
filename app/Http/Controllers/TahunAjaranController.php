<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /**
     * Menampilkan daftar tahun ajaran
     */
    public function index()
    {
        $tahunAjaran = TahunAjaran::orderByDesc('nama')->get();

        return view('admin.tahun-ajaran.index', compact('tahunAjaran'));
    }

    /**
     * Menampilkan form tambah tahun ajaran
     */
    public function create()
    {
        return view('admin.tahun-ajaran.create');
    }

    /**
     * Menyimpan tahun ajaran baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:20|unique:tahun_ajaran,nama',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'nama.required' => 'Tahun ajaran wajib diisi.',
            'nama.unique' => 'Tahun ajaran tersebut sudah ada.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        TahunAjaran::create([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'aktif' => false,
        ]);

        return redirect()
            ->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit
     */
    public function edit(TahunAjaran $tahunAjaran)
    {
        return view(
            'admin.tahun-ajaran.edit',
            compact('tahunAjaran')
        );
    }

    /**
     * Memperbarui tahun ajaran
     */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'nama' => 'required|string|max:20|unique:tahun_ajaran,nama,' . $tahunAjaran->id,
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'nama.required' => 'Tahun ajaran wajib diisi.',
            'nama.unique' => 'Tahun ajaran tersebut sudah ada.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        $tahunAjaran->update([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()
            ->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Menghapus tahun ajaran
     */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->tagihan()->exists()) {
            return redirect()
                ->route('admin.tahun-ajaran.index')
                ->with('error', 'Tahun ajaran tidak dapat dihapus karena sudah digunakan pada tagihan.');
        }

        $tahunAjaran->delete();

        return redirect()
            ->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Mengaktifkan tahun ajaran
     */
    public function aktifkan(TahunAjaran $tahunAjaran)
    {
        TahunAjaran::query()->update([
            'aktif' => false,
        ]);

        $tahunAjaran->update([
            'aktif' => true,
        ]);

        return redirect()
            ->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran ' . $tahunAjaran->nama . ' berhasil diaktifkan.');
    }
}