<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\OrangTua;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    /**
     * Menampilkan semua siswa
     */
    public function index()
    {
        $siswa = Siswa::with([
            'kelas',
            'orangTua'
        ])
        ->latest()
        ->get();

        return view(
            'admin.siswa.index',
            compact('siswa')
        );
    }


    /**
     * Form tambah siswa
     */
    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        $orangTua = OrangTua::with('user')
            ->orderBy('nama')
            ->get();

        return view(
            'admin.siswa.create',
            compact(
                'kelas',
                'orangTua'
            )
        );
    }


    /**
     * Simpan siswa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => [
                'required',
                'string',
                'max:50',
                'unique:siswa,nis',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P']),
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'orang_tua_id' => [
                'required',
                'exists:orang_tua,id',
            ],
        ]);


        Siswa::create($validated);


        return redirect()
            ->route('admin.siswa.index')
            ->with(
                'success',
                'Data siswa berhasil ditambahkan.'
            );
    }


    /**
     * Menampilkan detail siswa
     */
    public function show(Siswa $siswa)
    {
        $siswa->load([
            'kelas',
            'orangTua',
            'tagihan',
            'pembayaran'
        ]);


        return view(
            'admin.siswa.show',
            compact('siswa')
        );
    }


    /**
     * Form edit siswa
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        $orangTua = OrangTua::with('user')
            ->orderBy('nama')
            ->get();


        return view(
            'admin.siswa.edit',
            compact(
                'siswa',
                'kelas',
                'orangTua'
            )
        );
    }


    /**
     * Update siswa
     */
    public function update(
        Request $request,
        Siswa $siswa
    ) {
        $validated = $request->validate([
            'nis' => [
                'required',
                'string',
                'max:50',
                Rule::unique('siswa', 'nis')
                    ->ignore($siswa->id),
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P']),
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'orang_tua_id' => [
                'required',
                'exists:orang_tua,id',
            ],
        ]);


        $siswa->update($validated);


        return redirect()
            ->route('admin.siswa.index')
            ->with(
                'success',
                'Data siswa berhasil diperbarui.'
            );
    }


    /**
     * Hapus siswa
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();


        return redirect()
            ->route('admin.siswa.index')
            ->with(
                'success',
                'Data siswa berhasil dihapus.'
            );
    }
}