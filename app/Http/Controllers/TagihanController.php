<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\KategoriTagihan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    /**
     * Menampilkan seluruh tagihan
     */
    public function index()
    {
        $tagihan = Tagihan::with([
            'siswa.kelas',
            'tahunAjaran',
            'kategori'
        ])
        ->latest()
        ->get();

        return view(
            'admin.tagihan.index',
            compact('tagihan')
        );
    }


    /**
     * Form buat tagihan untuk semua siswa
     */
    public function create()
    {
        $kategori = KategoriTagihan::orderBy('nama')
            ->get();

        $tahunAjaran = TahunAjaran::orderBy(
            'tanggal_mulai',
            'desc'
        )->get();

        return view(
            'admin.tagihan.create',
            compact(
                'kategori',
                'tahunAjaran'
            )
        );
    }

    /**
 * Form edit tagihan
 */
public function edit(Tagihan $tagihan)
{
    $tagihan->load([
        'siswa',
        'tahunAjaran',
        'kategori'
    ]);

    $kategori = KategoriTagihan::orderBy('nama')
        ->get();

    $tahunAjaran = TahunAjaran::orderBy(
        'tanggal_mulai',
        'desc'
    )->get();

    return view(
        'admin.tagihan.edit',
        compact(
            'tagihan',
            'kategori',
            'tahunAjaran'
        )
    );
}

/**
 * Update tagihan
 */
public function update(
    Request $request,
    Tagihan $tagihan
) {
    $validated = $request->validate([
        'tahun_ajaran_id' => [
            'required',
            'exists:tahun_ajaran,id',
        ],

        'kategori_tagihan_id' => [
            'required',
            'exists:kategori_tagihan,id',
        ],

        'jatuh_tempo' => [
            'nullable',
            'date',
        ],
    ]);

    $kategori = KategoriTagihan::findOrFail(
        $validated['kategori_tagihan_id']
    );

    $tagihan->update([

        'tahun_ajaran_id' =>
            $validated['tahun_ajaran_id'],

        'kategori_tagihan_id' =>
            $validated['kategori_tagihan_id'],

        'nominal' =>
            $kategori->nominal,

        'jatuh_tempo' =>
            $validated['jatuh_tempo'] ?? null,

    ]);

    return redirect()
        ->route('admin.tagihan.index')
        ->with(
            'success',
            'Tagihan berhasil diperbarui.'
        );
}

/**
 * Hapus tagihan
 */
public function destroy(Tagihan $tagihan)
{
    $tagihan->delete();

    return redirect()
        ->route('admin.tagihan.index')
        ->with(
            'success',
            'Tagihan berhasil dihapus.'
        );
}


    /**
     * Simpan tagihan untuk semua siswa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => [
                'required',
                'exists:tahun_ajaran,id',
            ],

            'kategori_tagihan_id' => [
                'required',
                'exists:kategori_tagihan,id',
            ],

            'jatuh_tempo' => [
                'nullable',
                'date',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Ambil kategori
        |--------------------------------------------------------------------------
        */

        $kategori = KategoriTagihan::findOrFail(
            $validated['kategori_tagihan_id']
        );


        /*
        |--------------------------------------------------------------------------
        | Ambil semua siswa
        |--------------------------------------------------------------------------
        */

        $siswa = Siswa::select('id')
            ->get();


        if ($siswa->isEmpty()) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Belum ada data siswa.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Buat tagihan untuk semua siswa
        |--------------------------------------------------------------------------
        */

        $jumlahDibuat = 0;

        $jumlahSudahAda = 0;


        DB::transaction(function () use (
            $siswa,
            $validated,
            $kategori,
            &$jumlahDibuat,
            &$jumlahSudahAda
        ) {

            foreach ($siswa as $item) {

                /*
                |--------------------------------------------------------------------------
                | Cek apakah tagihan siswa ini sudah ada
                |--------------------------------------------------------------------------
                */

                $sudahAda = Tagihan::where(
                    'siswa_id',
                    $item->id
                )
                ->where(
                    'tahun_ajaran_id',
                    $validated['tahun_ajaran_id']
                )
                ->where(
                    'kategori_tagihan_id',
                    $validated['kategori_tagihan_id']
                )
                ->exists();


                if ($sudahAda) {

                    $jumlahSudahAda++;

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Buat tagihan
                |--------------------------------------------------------------------------
                */

                Tagihan::create([

                    'siswa_id' =>
                        $item->id,

                    'tahun_ajaran_id' =>
                        $validated['tahun_ajaran_id'],

                    'kategori_tagihan_id' =>
                        $validated['kategori_tagihan_id'],

                    'nominal' =>
                        $kategori->nominal,

                    'jatuh_tempo' =>
                        $validated['jatuh_tempo'] ?? null,

                ]);


                $jumlahDibuat++;
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Pesan hasil
        |--------------------------------------------------------------------------
        */

        $pesan =
            $jumlahDibuat .
            ' tagihan berhasil dibuat.';


        if ($jumlahSudahAda > 0) {

            $pesan .=
                ' ' .
                $jumlahSudahAda .
                ' tagihan sudah ada dan dilewati.';
        }


        return redirect()
            ->route('admin.tagihan.index')
            ->with(
                'success',
                $pesan
            );
    }


    /**
     * Menampilkan detail tagihan
     */
    public function show(Tagihan $tagihan)
    {
        $tagihan->load([
            'siswa.kelas',
            'tahunAjaran',
            'kategori',
            'pembayaran'
        ]);

        return view(
            'admin.tagihan.show',
            compact('tagihan')
        );
    }
}