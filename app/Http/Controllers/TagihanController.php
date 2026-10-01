<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\KategoriTagihan;
use App\Models\TahunAjaran;
use App\Models\PengaturanPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagihanController extends Controller
{
    /**
     * Menampilkan tagihan yang dikelompokkan berdasarkan siswa.
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
     * Form buat tagihan.
     */
    public function create()
    {
        $kategori = KategoriTagihan::orderBy('nama')->get();

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
     * Form edit tagihan.
     */
    public function edit(Tagihan $tagihan)
    {
        $tagihan->load([
            'siswa',
            'tahunAjaran',
            'kategori',
        ]);

        $kategori = KategoriTagihan::orderBy('nama')->get();

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
     * Update tagihan.
     */
    public function update(Request $request, Tagihan $tagihan)
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
        ]);

        $kategori = KategoriTagihan::findOrFail(
            $validated['kategori_tagihan_id']
        );

        $tahunAjaran = TahunAjaran::findOrFail(
            $validated['tahun_ajaran_id']
        );

        /*
        |--------------------------------------------------------------------------
        | SPP
        |--------------------------------------------------------------------------
        |
        | Nominal kategori SPP = TOTAL SPP 1 tahun.
        |
        | Contoh:
        |
        | Rp1.350.000 / 12 = Rp112.500 per bulan
        |
        */

        if ($this->isSpp($kategori)) {

            /*
            |--------------------------------------------------------------------------
            | Tentukan bulan dan tahun tagihan
            |--------------------------------------------------------------------------
            */

            if ($tagihan->bulan && $tagihan->tahun) {

                $bulan = (int) $tagihan->bulan;

                $tahun = (int) $tagihan->tahun;

            } elseif ($tagihan->jatuh_tempo) {

                $tanggalLama = Carbon::parse(
                    $tagihan->jatuh_tempo
                );

                $bulan = $tanggalLama->month;

                $tahun = $tanggalLama->year;

            } else {

                $tanggalMulai = Carbon::parse(
                    $tahunAjaran->tanggal_mulai
                );

                $bulan = $tanggalMulai->month;

                $tahun = $tanggalMulai->year;
            }


            /*
            |--------------------------------------------------------------------------
            | Hitung jumlah bulan tahun ajaran
            |--------------------------------------------------------------------------
            */

            $periode =
                $this->getPeriodeTahunAjaran(
                    $tahunAjaran
                );

            $jumlahBulan =
                count($periode);


            if ($jumlahBulan <= 0) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Periode tahun ajaran tidak valid.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Hitung nominal per bulan
            |--------------------------------------------------------------------------
            */

            $totalSpp =
                (int) round(
                    (float) $kategori->nominal
                );

            $nominalPerBulan =
                intdiv(
                    $totalSpp,
                    $jumlahBulan
                );

            $sisa =
                $totalSpp -
                ($nominalPerBulan * $jumlahBulan);


            /*
            |--------------------------------------------------------------------------
            | Tentukan posisi bulan dalam periode
            |--------------------------------------------------------------------------
            */

            $indexBulan = 0;

            foreach ($periode as $index => $periodeBulanan) {

                if (
                    (int) $periodeBulanan['bulan'] === $bulan &&
                    (int) $periodeBulanan['tahun'] === $tahun
                ) {

                    $indexBulan = $index;

                    break;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Tambahkan sisa ke bulan tertentu
            |--------------------------------------------------------------------------
            */

            $nominalBulanIni =
                $nominalPerBulan;

            if ($indexBulan < $sisa) {

                $nominalBulanIni++;
            }


            /*
            |--------------------------------------------------------------------------
            | Cek apakah tahun/kategori diubah
            |--------------------------------------------------------------------------
            */

            $duplikat = Tagihan::where(
                'siswa_id',
                $tagihan->siswa_id
            )
                ->where(
                    'tahun_ajaran_id',
                    $validated['tahun_ajaran_id']
                )
                ->where(
                    'kategori_tagihan_id',
                    $validated['kategori_tagihan_id']
                )
                ->where(
                    'bulan',
                    $bulan
                )
                ->where(
                    'tahun',
                    $tahun
                )
                ->where(
                    'id',
                    '!=',
                    $tagihan->id
                )
                ->exists();


            if ($duplikat) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Tagihan SPP untuk bulan tersebut sudah ada.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Jatuh tempo
            |--------------------------------------------------------------------------
            */

            $jatuhTempo =
                $this->buatTanggalJatuhTempo(
                    $tahun,
                    $bulan
                );


            /*
            |--------------------------------------------------------------------------
            | Update
            |--------------------------------------------------------------------------
            */

            $tagihan->update([

                'tahun_ajaran_id' =>
                    $validated['tahun_ajaran_id'],

                'kategori_tagihan_id' =>
                    $validated['kategori_tagihan_id'],

                'nominal' =>
                    $nominalBulanIni,

                'bulan' =>
                    $bulan,

                'tahun' =>
                    $tahun,

                'jatuh_tempo' =>
                    $jatuhTempo,

            ]);

        } else {

            /*
            |--------------------------------------------------------------------------
            | NON SPP
            |--------------------------------------------------------------------------
            */

            $jatuhTempo =
                $this->buatTanggalJatuhTempoNonSpp(
                    $tahunAjaran
                );


            /*
            |--------------------------------------------------------------------------
            | Cek duplikat non-SPP
            |--------------------------------------------------------------------------
            */

            $duplikat = Tagihan::where(
                'siswa_id',
                $tagihan->siswa_id
            )
                ->where(
                    'tahun_ajaran_id',
                    $validated['tahun_ajaran_id']
                )
                ->where(
                    'kategori_tagihan_id',
                    $validated['kategori_tagihan_id']
                )
                ->whereNull('bulan')
                ->whereNull('tahun')
                ->where(
                    'id',
                    '!=',
                    $tagihan->id
                )
                ->exists();


            if ($duplikat) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Tagihan untuk kategori tersebut sudah ada.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Update non-SPP
            |--------------------------------------------------------------------------
            */

            $tagihan->update([

                'tahun_ajaran_id' =>
                    $validated['tahun_ajaran_id'],

                'kategori_tagihan_id' =>
                    $validated['kategori_tagihan_id'],

                'nominal' =>
                    $kategori->nominal,

                'bulan' =>
                    null,

                'tahun' =>
                    null,

                'jatuh_tempo' =>
                    $jatuhTempo,

            ]);
        }


        return redirect()
            ->route('admin.tagihan.index')
            ->with(
                'success',
                'Tagihan berhasil diperbarui.'
            );
    }

    /**
     * Hapus tagihan.
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
     * Membuat tagihan untuk semua siswa.
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
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi kategori
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
        | Tahun ajaran
        |--------------------------------------------------------------------------
        */

        $tahunAjaran = TahunAjaran::findOrFail(
            $validated['tahun_ajaran_id']
        );


        /*
        |--------------------------------------------------------------------------
        | Kategori
        |--------------------------------------------------------------------------
        */

        if ($validated['kategori_tagihan_id'] === 'semua') {

            $daftarKategori =
                KategoriTagihan::orderBy('nama')->get();

        } else {

            $daftarKategori =
                KategoriTagihan::where(
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
        | Semua siswa
        |--------------------------------------------------------------------------
        */

        $siswa =
            Siswa::select('id')->get();


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
        | Periode tahun ajaran
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | 2028/2029
        |
        | Juli 2028 sampai Juni 2029
        |
        */

        $periode =
            $this->getPeriodeTahunAjaran(
                $tahunAjaran
            );


        $jumlahBulan =
            count($periode);


        if ($jumlahBulan <= 0) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Periode tahun ajaran tidak valid.'
                );
        }


        $jumlahDibuat = 0;

        $jumlahSudahAda = 0;


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $siswa,
            $daftarKategori,
            $validated,
            $periode,
            $jumlahBulan,
            $tahunAjaran,
            &$jumlahDibuat,
            &$jumlahSudahAda
        ) {

            foreach ($siswa as $itemSiswa) {

                foreach ($daftarKategori as $kategori) {

                    /*
                    |--------------------------------------------------------------------------
                    | SPP = 12 TAGIHAN BULANAN
                    |--------------------------------------------------------------------------
                    |
                    | Nominal kategori SPP dianggap sebagai TOTAL
                    | selama satu tahun ajaran.
                    |
                    | Contoh:
                    |
                    | Rp1.350.000 / 12
                    | = Rp112.500 per bulan
                    |
                    */

                    if ($this->isSpp($kategori)) {

                        /*
                        |--------------------------------------------------------------------------
                        | Total SPP satu tahun
                        |--------------------------------------------------------------------------
                        */

                        $totalSpp =
                            (int) round(
                                (float) $kategori->nominal
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Nominal dasar setiap bulan
                        |--------------------------------------------------------------------------
                        */

                        $nominalPerBulan =
                            intdiv(
                                $totalSpp,
                                $jumlahBulan
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Hitung sisa pembagian
                        |--------------------------------------------------------------------------
                        |
                        | Contoh:
                        |
                        | Rp1.350.000 / 12
                        |
                        | Hasil = Rp112.500
                        | Sisa = Rp0
                        |
                        */

                        $sisa =
                            $totalSpp -
                            (
                                $nominalPerBulan *
                                $jumlahBulan
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Buat 12 bulan
                        |--------------------------------------------------------------------------
                        */

                        foreach (
                            $periode
                            as $index => $periodeBulanan
                        ) {

                            $bulan =
                                $periodeBulanan['bulan'];

                            $tahun =
                                $periodeBulanan['tahun'];


                            /*
                            |--------------------------------------------------------------------------
                            | Cek SPP bulan tersebut
                            |--------------------------------------------------------------------------
                            */

                            $sudahAda =
                                Tagihan::where(
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
                                ->where(
                                    'bulan',
                                    $bulan
                                )
                                ->where(
                                    'tahun',
                                    $tahun
                                )
                                ->exists();


                            if ($sudahAda) {

                                $jumlahSudahAda++;

                                continue;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Nominal bulan ini
                            |--------------------------------------------------------------------------
                            */

                            $nominalBulanIni =
                                $nominalPerBulan;


                            /*
                            |--------------------------------------------------------------------------
                            | Bagikan sisa pembagian
                            |--------------------------------------------------------------------------
                            |
                            | Kalau ada sisa, Rp1 ditambahkan
                            | ke bulan-bulan pertama.
                            |
                            */

                            if ($index < $sisa) {

                                $nominalBulanIni++;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Jatuh tempo
                            |--------------------------------------------------------------------------
                            */

                            $jatuhTempo =
                                $this->buatTanggalJatuhTempo(
                                    $tahun,
                                    $bulan
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | Buat tagihan SPP
                            |--------------------------------------------------------------------------
                            */

                            Tagihan::create([

                                'siswa_id' =>
                                    $itemSiswa->id,

                                'tahun_ajaran_id' =>
                                    $validated['tahun_ajaran_id'],

                                'kategori_tagihan_id' =>
                                    $kategori->id,

                                /*
                                 * NOMINAL DI SINI SUDAH
                                 * MENJADI NOMINAL BULANAN
                                 */
                                'nominal' =>
                                    $nominalBulanIni,

                                'bulan' =>
                                    $bulan,

                                'tahun' =>
                                    $tahun,

                                'jatuh_tempo' =>
                                    $jatuhTempo,

                            ]);


                            $jumlahDibuat++;

                        }

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | SELAIN SPP = SATU TAGIHAN
                        |--------------------------------------------------------------------------
                        |
                        | Contoh:
                        |
                        | Ujian    Rp550.000
                        | Jas      Rp300.000
                        | PTS      Rp250.000
                        |
                        | Tidak dibagi 12 bulan.
                        |
                        */

                        $sudahAda =
                            Tagihan::where(
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
                            ->whereNull('bulan')
                            ->whereNull('tahun')
                            ->exists();


                        if ($sudahAda) {

                            $jumlahSudahAda++;

                            continue;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Jatuh tempo non-SPP
                        |--------------------------------------------------------------------------
                        */

                        $jatuhTempo =
                            $this->buatTanggalJatuhTempoNonSpp(
                                $tahunAjaran
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Buat tagihan non-SPP
                        |--------------------------------------------------------------------------
                        */

                        Tagihan::create([

                            'siswa_id' =>
                                $itemSiswa->id,

                            'tahun_ajaran_id' =>
                                $validated['tahun_ajaran_id'],

                            'kategori_tagihan_id' =>
                                $kategori->id,

                            'nominal' =>
                                $kategori->nominal,

                            'bulan' =>
                                null,

                            'tahun' =>
                                null,

                            'jatuh_tempo' =>
                                $jatuhTempo,

                        ]);


                        $jumlahDibuat++;

                    }

                }

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
     * Detail tagihan.
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

    /**
     * Hapus beberapa tagihan sekaligus.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'tagihan_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'tagihan_ids.*' => [
                'integer',
                'exists:tagihan,id',
            ],
        ]);


        $jumlah =
            count(
                $request->tagihan_ids
            );


        Tagihan::whereIn(
            'id',
            $request->tagihan_ids
        )->delete();


        return redirect()
            ->route('admin.tagihan.index')
            ->with(
                'success',
                $jumlah .
                ' tagihan berhasil dihapus.'
            );
    }

    /**
     * Mengecek apakah kategori adalah SPP.
     */
    private function isSpp(
        KategoriTagihan $kategori
    ): bool {

        return strtolower(
            trim($kategori->nama)
        ) === 'spp';
    }

    /**
     * Membuat periode bulanan berdasarkan
     * tanggal mulai dan tanggal selesai
     * tahun ajaran.
     */
    private function getPeriodeTahunAjaran(
        TahunAjaran $tahunAjaran
    ): array {

        $tanggalMulai =
            Carbon::parse(
                $tahunAjaran->tanggal_mulai
            )->startOfMonth();


        $tanggalSelesai =
            Carbon::parse(
                $tahunAjaran->tanggal_selesai
            )->startOfMonth();


        $periode = [];


        $tanggal =
            $tanggalMulai->copy();


        while (
            $tanggal->lessThanOrEqualTo(
                $tanggalSelesai
            )
        ) {

            $periode[] = [

                'bulan' =>
                    $tanggal->month,

                'tahun' =>
                    $tanggal->year,

            ];


            $tanggal->addMonth();
        }


        return $periode;
    }

    /**
     * Membuat tanggal jatuh tempo SPP.
     */
    private function buatTanggalJatuhTempo(
        int $tahun,
        int $bulan
    ): ?string {

        $pengaturan =
            PengaturanPembayaran::first();


        if (
            !$pengaturan ||
            !$pengaturan->aktif
        ) {

            return null;
        }


        $hari =
            (int) $pengaturan->tanggal_jatuh_tempo;


        $jumlahHari =
            Carbon::create(
                $tahun,
                $bulan,
                1
            )->daysInMonth;


        $hari =
            min(
                $hari,
                $jumlahHari
            );


        return Carbon::create(
            $tahun,
            $bulan,
            $hari
        )->format('Y-m-d');
    }

    /**
     * Membuat tanggal jatuh tempo non-SPP.
     */
    private function buatTanggalJatuhTempoNonSpp(
        TahunAjaran $tahunAjaran
    ): ?string {

        $pengaturan =
            PengaturanPembayaran::first();


        if (
            !$pengaturan ||
            !$pengaturan->aktif
        ) {

            return null;
        }


        $tanggalMulai =
            Carbon::parse(
                $tahunAjaran->tanggal_mulai
            );


        $hari =
            (int) $pengaturan->tanggal_jatuh_tempo;


        $jumlahHari =
            $tanggalMulai->daysInMonth;


        $hari =
            min(
                $hari,
                $jumlahHari
            );


        return $tanggalMulai
            ->copy()
            ->setDay($hari)
            ->format('Y-m-d');
    }
}