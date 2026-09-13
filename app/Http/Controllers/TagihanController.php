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
     * Menampilkan seluruh tagihan berdasarkan kelompok siswa
     */
/**
 * Menampilkan tagihan yang dikelompokkan berdasarkan siswa
 */
public function index(Request $request)
{
    $search = $request->input('search');

    $query = Tagihan::with([
        'siswa.kelas',
        'tahunAjaran',
        'kategori',
    ]);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->whereHas('siswa', function ($siswaQuery) use ($search) {
                $siswaQuery
                    ->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%');
            })
            ->orWhereHas('kategori', function ($kategoriQuery) use ($search) {
                $kategoriQuery->where(
                    'nama',
                    'like',
                    '%' . $search . '%'
                );
            })
            ->orWhereHas('tahunAjaran', function ($tahunQuery) use ($search) {
                $tahunQuery->where(
                    'nama',
                    'like',
                    '%' . $search . '%'
                );
            });
        });
    }

    $tagihan = $query
        ->latest()
        ->get()
        ->groupBy(function ($item) {
            return $item->siswa_id;
        });

    return view(
        'admin.tagihan.index',
        compact('tagihan', 'search')
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
            'kategori',
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
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'kategori_tagihan_id' => $validated['kategori_tagihan_id'],
            'nominal' => $kategori->nominal,
            'jatuh_tempo' => $validated['jatuh_tempo'] ?? null,
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
                'string',
            ],

            'jatuh_tempo' => [
                'nullable',
                'date',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi kategori jika bukan semua kategori
        |--------------------------------------------------------------------------
        */

        if ($validated['kategori_tagihan_id'] !== 'semua') {
            $request->validate([
                'kategori_tagihan_id' => [
                    'required',
                    'exists:kategori_tagihan,id',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil kategori
        |--------------------------------------------------------------------------
        */

        if ($validated['kategori_tagihan_id'] === 'semua') {
            $daftarKategori = KategoriTagihan::orderBy('nama')
                ->get();
        } else {
            $daftarKategori = KategoriTagihan::where(
                'id',
                $validated['kategori_tagihan_id']
            )->get();
        }

        if ($daftarKategori->isEmpty()) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Belum ada kategori tagihan.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh siswa
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
        | Proses pembuatan tagihan
        |--------------------------------------------------------------------------
        */

        $jumlahDibuat = 0;
        $jumlahSudahAda = 0;

        DB::transaction(function () use (
            $siswa,
            $daftarKategori,
            $validated,
            &$jumlahDibuat,
            &$jumlahSudahAda
        ) {
            foreach ($siswa as $itemSiswa) {
                foreach ($daftarKategori as $kategori) {

                    /*
                    |--------------------------------------------------------------------------
                    | Cek tagihan yang sama
                    |--------------------------------------------------------------------------
                    */

                    $sudahAda = Tagihan::where(
                        'siswa_id',
                        $itemSiswa->id
                    )
                        ->where(
                            'tahun_ajaran_id',
                            $validated['tahun_ajaran_id']
                        )
                        ->where(
                            'kategori_tagihan_id',
                            $kategori->id
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
                        'siswa_id' => $itemSiswa->id,

                        'tahun_ajaran_id' =>
                            $validated['tahun_ajaran_id'],

                        'kategori_tagihan_id' =>
                            $kategori->id,

                        'nominal' =>
                            $kategori->nominal,

                        'jatuh_tempo' =>
                            $validated['jatuh_tempo'] ?? null,
                    ]);

                    $jumlahDibuat++;
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Pesan hasil
        |--------------------------------------------------------------------------
        */

        $pesan = $jumlahDibuat .
            ' tagihan berhasil dibuat.';

        if ($jumlahSudahAda > 0) {
            $pesan .= ' ' .
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
            'pembayaran',
        ]);

        return view(
            'admin.tagihan.show',
            compact('tagihan')
        );
    }
}