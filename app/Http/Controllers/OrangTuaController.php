<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OrangTuaController extends Controller
{
    /**
     * Menampilkan semua orang tua
     */
/**
 * Menampilkan seluruh data orang tua.
 */
public function index(Request $request)
{
    $query = OrangTua::with('user')
        ->withCount('siswa');

    if ($request->filled('search')) {

        $search = trim($request->search);

        $query->where(function ($q) use ($search) {

            // Pencarian berdasarkan nama orang tua
            $q->where('nama', 'like', '%' . $search . '%')

                // Pencarian berdasarkan nomor HP
                ->orWhere('no_hp', 'like', '%' . $search . '%')

                // Pencarian berdasarkan email atau nama user
                ->orWhereHas('user', function ($userQuery) use ($search) {

                    $userQuery
                        ->where('email', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%');

                })

                // Pencarian berdasarkan data anak
                ->orWhereHas('siswa', function ($siswaQuery) use ($search) {

                    $siswaQuery
                        ->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nis', 'like', '%' . $search . '%');

                });

        });

    }

    $orangTua = $query
        ->latest()
        ->get();

    return view(
        'admin.orang-tua.index',
        compact('orangTua')
    );
}

    /**
     * Form tambah orang tua
     */
    public function create()
    {
        return view('admin.orang-tua.create');
    }


    /**
     * Simpan orang tua + akun login
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

        ]);


        DB::transaction(function () use ($validated) {

            // =========================================
            // 1. BUAT AKUN USER UNTUK LOGIN
            // =========================================

            $user = User::create([

                'name' => $validated['nama'],

                'email' => $validated['email'],

                'password' => Hash::make(
                    $validated['password']
                ),

                'role' => 'orang_tua',

            ]);


            // =========================================
            // 2. BUAT DATA ORANG TUA
            // =========================================

            OrangTua::create([

                'user_id' => $user->id,

                'nama' => $validated['nama'],

                'no_hp' => $validated['no_hp'] ?? null,

                'alamat' => $validated['alamat'] ?? null,

            ]);
        });


        return redirect()
            ->route('admin.orang-tua.index')
            ->with(
                'success',
                'Data orang tua dan akun login berhasil dibuat.'
            );
    }


    /**
     * Detail orang tua
     */
    public function show(OrangTua $orangTua)
    {
        $orangTua->load([
            'user',
            'siswa.kelas'
        ]);

        return view(
            'admin.orang-tua.show',
            compact('orangTua')
        );
    }


    /**
     * Form edit
     */
    public function edit(OrangTua $orangTua)
    {
        $orangTua->load('user');

        return view(
            'admin.orang-tua.edit',
            compact('orangTua')
        );
    }


    /**
     * Update orang tua
     */
    public function update(
        Request $request,
        OrangTua $orangTua
    ) {
        $orangTua->load('user');

        $validated = $request->validate([

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($orangTua->user_id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

        ]);


        DB::transaction(function () use (
            $validated,
            $orangTua
        ) {

            // =========================================
            // UPDATE AKUN USER
            // =========================================

            $userData = [

                'name' => $validated['nama'],

                'email' => $validated['email'],

            ];


            // Jika password diisi,
            // password ikut diubah
            if (!empty($validated['password'])) {

                $userData['password'] =
                    Hash::make(
                        $validated['password']
                    );
            }


            $orangTua->user->update(
                $userData
            );


            // =========================================
            // UPDATE DATA ORANG TUA
            // =========================================

            $orangTua->update([

                'nama' => $validated['nama'],

                'no_hp' => $validated['no_hp'] ?? null,

                'alamat' => $validated['alamat'] ?? null,

            ]);
        });


        return redirect()
            ->route('admin.orang-tua.index')
            ->with(
                'success',
                'Data orang tua berhasil diperbarui.'
            );
    }


    /**
     * Hapus orang tua
     */
    public function destroy(OrangTua $orangTua)
    {
        $orangTua->load('user');


        DB::transaction(function () use ($orangTua) {

            // =========================================
            // HAPUS AKUN USER
            // =========================================

            if ($orangTua->user) {

                $orangTua->user->delete();
            }


            // =========================================
            // HAPUS DATA ORANG TUA
            // =========================================

            $orangTua->delete();
        });


        return redirect()
            ->route('admin.orang-tua.index')
            ->with(
                'success',
                'Data orang tua berhasil dihapus.'
            );
    }
}