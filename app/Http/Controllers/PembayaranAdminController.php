<?php

namespace App\Http\Controllers;

use App\Models\PembayaranTagihan;
use App\Models\Tagihan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranAdminController extends Controller
{
    /**
     * ============================================================
     * CEK APAKAH KATEGORI ADALAH SPP
     * ============================================================
     */
    private function isSpp(Tagihan $tagihan)
    {
        return strtolower(
            trim($tagihan->kategori->nama ?? '')
        ) === 'spp';
    }

    /**
     * ============================================================
     * CEK APAKAH TAGIHAN ADALAH PTS ATAU UJIAN
     * ============================================================
     */
    private function isPtsAtauUjian(Tagihan $tagihan)
    {
        $namaKategori = strtolower(
            trim($tagihan->kategori->nama ?? '')
        );

        return str_contains($namaKategori, 'pts')
            || str_contains($namaKategori, 'ujian');
    }

    /**
     * ============================================================
     * AMBIL SELURUH TAGIHAN SPP
     *
     * Diurutkan berdasarkan tahun dan bulan.
     * ============================================================
     */
    private function getTagihanSpp(Tagihan $tagihan)
    {
        return Tagihan::with([
            'kategori',
            'tahunAjaran',
        ])
            ->where('siswa_id', $tagihan->siswa_id)
            ->where('tahun_ajaran_id', $tagihan->tahun_ajaran_id)
            ->whereHas('kategori', function ($query) {
                $query->whereRaw(
                    'LOWER(TRIM(nama)) = ?',
                    ['spp']
                );
            })
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('id')
            ->get();
    }

    /**
     * ============================================================
     * HITUNG TOTAL PEMBAYARAN YANG SUDAH DIBAYAR
     * UNTUK SATU TAGIHAN
     * ============================================================
     */
    private function getTotalDibayar(Tagihan $tagihan)
    {
        return (float) PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
            ->whereIn('status', [
                'dibayar',
                'disetujui',
            ])
            ->sum('nominal');
    }

    /**
     * ============================================================
     * HITUNG SISA SATU TAGIHAN
     * ============================================================
     */
    private function getSisaTagihan(Tagihan $tagihan)
    {
        $sudahDibayar = $this->getTotalDibayar($tagihan);

        return max(
            (float) $tagihan->nominal - $sudahDibayar,
            0
        );
    }

    /**
     * ============================================================
     * CEK APAKAH SELURUH SPP SUDAH LUNAS
     * ============================================================
     */
    private function cekSppLunas(Tagihan $tagihan)
    {
        $tagihanSpp = $this->getTagihanSpp($tagihan);

        if ($tagihanSpp->isEmpty()) {
            return false;
        }

        foreach ($tagihanSpp as $spp) {
            if ($this->getSisaTagihan($spp) > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * ============================================================
     * MENAMPILKAN PEMBAYARAN YANG MENUNGGU
     * ============================================================
     */
    public function index()
    {
        $pembayaran = PembayaranTagihan::with([
            'tagihan.siswa',
            'tagihan.kategori',
            'tagihan.tahunAjaran',
            'user',
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
     * ============================================================
     * FORM PEMBAYARAN MANUAL / CASH
     * ============================================================
     */
    public function createManual()
    {
        $siswa = Siswa::orderBy('nama')->get();

        $tagihan = Tagihan::with([
            'siswa',
            'kategori',
            'tahunAjaran',
            'pembayaran',
        ])
            ->get()
            ->sortBy(function ($item) {
                $tanggalMulai = optional($item->tahunAjaran)->tanggal_mulai;

                $tanggalSekolah = $tanggalMulai
                    ? Carbon::parse($tanggalMulai)->timestamp
                    : PHP_INT_MAX;

                $periode = (($item->tahun ?? 0) * 100)
                    + ($item->bulan ?? 0);

                return [
                    $tanggalSekolah,
                    strtolower($item->siswa->nama ?? ''),
                    strtolower($item->kategori->nama ?? ''),
                    $periode,
                    $item->id,
                ];
            })
            ->values();

        return view(
            'admin.pembayaran.manual',
            compact(
                'siswa',
                'tagihan'
            )
        );
    }

    /**
     * ============================================================
     * SIMPAN PEMBAYARAN MANUAL / CASH
     *
     * ATURAN:
     *
     * 1. SPP:
     *    - Bisa memilih bulan mulai pembayaran.
     *    - Pembayaran dimulai dari bulan yang dipilih.
     *    - Jika nominal lebih besar dari satu bulan,
     *      otomatis lanjut ke bulan berikutnya.
     *    - Bulan yang sudah lunas akan dilewati.
     *    - Pembayaran berhenti ketika nominal habis.
     *    - Semua pembayaran langsung berstatus "dibayar".
     *
     * 2. Non-SPP:
     *    - Nominal maksimal = sisa tagihan.
     *    - Pembayaran langsung berstatus "dibayar".
     * ============================================================
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'tagihan_id' => [
                'required',
                'exists:tagihan,id',
            ],
            'nominal' => [
                'required',
                'numeric',
                'min:1',
            ],
            'tanggal_kirim' => [
                'required',
                'date',
            ],
        ], [
            'tagihan_id.required' =>
                'Tagihan harus dipilih.',

            'tagihan_id.exists' =>
                'Tagihan tidak ditemukan.',

            'nominal.required' =>
                'Nominal pembayaran harus diisi.',

            'nominal.numeric' =>
                'Nominal pembayaran harus berupa angka.',

            'nominal.min' =>
                'Nominal pembayaran harus lebih dari 0.',

            'tanggal_kirim.required' =>
                'Tanggal pembayaran harus diisi.',

            'tanggal_kirim.date' =>
                'Tanggal pembayaran tidak valid.',
        ]);

        return DB::transaction(function () use ($request) {

            /**
             * ========================================================
             * AMBIL TAGIHAN YANG DIPILIH
             * ========================================================
             */
            $tagihan = Tagihan::with([
                'siswa',
                'kategori',
                'tahunAjaran',
            ])
                ->lockForUpdate()
                ->findOrFail($request->tagihan_id);

            $nominal = (float) $request->nominal;

            /**
             * ========================================================
             * SPP
             * ========================================================
             */
            if ($this->isSpp($tagihan)) {

                $tagihanSpp = $this->getTagihanSpp($tagihan)
                    ->filter(function ($spp) use ($tagihan) {

                        if ($spp->tahun > $tagihan->tahun) {
                            return true;
                        }

                        if (
                            $spp->tahun == $tagihan->tahun &&
                            $spp->bulan >= $tagihan->bulan
                        ) {
                            return true;
                        }

                        return false;
                    })
                    ->values();

                if ($tagihanSpp->isEmpty()) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Tagihan SPP dari bulan yang dipilih tidak ditemukan.'
                        );
                }

                /**
                 * ====================================================
                 * TOTAL SISA SPP
                 * ====================================================
                 */
                $totalSisaSpp = $tagihanSpp->sum(function ($spp) {
                    return $this->getSisaTagihan($spp);
                });

                /**
                 * ====================================================
                 * JIKA SUDAH LUNAS
                 * ====================================================
                 */
                if ($totalSisaSpp <= 0) {

                    $bulan = $tagihan->bulan;
                    $tahun = $tagihan->tahun;

                    $namaBulan = '-';

                    if ($bulan && $tahun) {
                        $namaBulan = Carbon::create(
                            $tahun,
                            $bulan,
                            1
                        )->translatedFormat('F Y');
                    }

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Tagihan SPP mulai bulan ' .
                            $namaBulan .
                            ' sudah lunas.'
                        );
                }

                /**
                 * ====================================================
                 * NOMINAL TIDAK BOLEH MELEBIHI TOTAL SISA
                 * ====================================================
                 */
                if ($nominal > $totalSisaSpp) {

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Nominal pembayaran melebihi total sisa SPP mulai bulan yang dipilih. Sisa total: Rp ' .
                            number_format(
                                $totalSisaSpp,
                                0,
                                ',',
                                '.'
                            )
                        );
                }

                /**
                 * ====================================================
                 * ALOKASI PEMBAYARAN
                 * ====================================================
                 */
                $sisaPembayaran = $nominal;

                foreach ($tagihanSpp as $spp) {

                    if ($sisaPembayaran <= 0) {
                        break;
                    }

                    $sisaBulan = $this->getSisaTagihan($spp);

                    if ($sisaBulan <= 0) {
                        continue;
                    }

                    $nominalUntukBulan = min(
                        $sisaPembayaran,
                        $sisaBulan
                    );

                    /**
                     * =================================================
                     * SIMPAN PEMBAYARAN CASH
                     * =================================================
                     */
                    PembayaranTagihan::create([
                        'tagihan_id' =>
                            $spp->id,

                        'user_id' =>
                            Auth::id(),

                        'nominal' =>
                            $nominalUntukBulan,

                        'metode' =>
                            'cash',

                        'tanggal_kirim' =>
                            $request->tanggal_kirim,

                        'status' =>
                            'dibayar',

                        'tanggal_disetujui' =>
                            now(),
                    ]);

                    $sisaPembayaran -= $nominalUntukBulan;
                }

                /**
                 * ====================================================
                 * CEK KEAMANAN
                 * ====================================================
                 */
                if ($sisaPembayaran > 0) {
                    throw new \Exception(
                        'Nominal pembayaran melebihi total sisa tagihan SPP.'
                    );
                }

                /**
                 * ====================================================
                 * NAMA BULAN YANG DIPILIH
                 * ====================================================
                 */
                $bulan = $tagihan->bulan;
                $tahun = $tagihan->tahun;

                $namaBulan = '-';

                if ($bulan && $tahun) {
                    $namaBulan = Carbon::create(
                        $tahun,
                        $bulan,
                        1
                    )->translatedFormat('F Y');
                }

                /**
                 * ====================================================
                 * PESAN BERHASIL
                 * ====================================================
                 */
                return redirect()
                    ->route('admin.pembayaran.index')
                    ->with(
                        'success',
                        'Pembayaran cash SPP mulai bulan ' .
                        $namaBulan .
                        ' sebesar Rp ' .
                        number_format(
                            $nominal,
                            0,
                            ',',
                            '.'
                        ) .
                        ' berhasil dicatat dan otomatis dialokasikan ke bulan berikutnya.'
                    );
            }

            /**
             * ========================================================
             * NON-SPP
             * ========================================================
             */
            $sisaTagihan = $this->getSisaTagihan($tagihan);

            if ($sisaTagihan <= 0) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Tagihan ini sudah lunas.'
                    );
            }

            if ($nominal > $sisaTagihan) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Nominal pembayaran melebihi sisa tagihan. Sisa tagihan: Rp ' .
                        number_format(
                            $sisaTagihan,
                            0,
                            ',',
                            '.'
                        )
                    );
            }

            PembayaranTagihan::create([
                'tagihan_id' =>
                    $tagihan->id,

                'user_id' =>
                    Auth::id(),

                'nominal' =>
                    $nominal,

                'metode' =>
                    'cash',

                'tanggal_kirim' =>
                    $request->tanggal_kirim,

                'status' =>
                    'dibayar',

                'tanggal_disetujui' =>
                    now(),
            ]);

            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'success',
                    'Pembayaran cash berhasil dicatat.'
                );
        });
    }

    /**
     * ============================================================
     * JUMLAH NOTIFIKASI PEMBAYARAN
     * ============================================================
     */
    public function notifikasi()
    {
        $jumlah = PembayaranTagihan::where(
            'status',
            'menunggu'
        )->count();

        return response()->json([
            'jumlah' => $jumlah,
        ]);
    }

    /**
     * ============================================================
     * FORM KOREKSI PEMBAYARAN
     * ============================================================
     */
    public function edit(PembayaranTagihan $pembayaran)
    {
        $pembayaran->load([
            'tagihan.siswa',
            'tagihan.kategori',
            'tagihan.tahunAjaran',
        ]);

        if ($pembayaran->status !== 'menunggu') {

            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Pembayaran yang sudah diproses tidak dapat dikoreksi.'
                );
        }

        $tagihan = $pembayaran->tagihan;

        if (!$tagihan) {

            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Tagihan tidak ditemukan.'
                );
        }

        /**
         * ========================================================
         * SPP
         * ========================================================
         */
        if ($this->isSpp($tagihan)) {

            $tagihanSpp = $this->getTagihanSpp($tagihan)
                ->filter(function ($spp) use ($tagihan) {

                    if ($spp->tahun > $tagihan->tahun) {
                        return true;
                    }

                    if (
                        $spp->tahun == $tagihan->tahun &&
                        $spp->bulan >= $tagihan->bulan
                    ) {
                        return true;
                    }

                    return false;
                })
                ->values();

            $totalSisaSpp = $tagihanSpp->sum(function ($spp) {
                return $this->getSisaTagihan($spp);
            });

            return view(
                'admin.pembayaran.edit',
                [
                    'pembayaran' =>
                        $pembayaran,

                    'tagihan' =>
                        $tagihan,

                    'sudahDibayar' =>
                        0,

                    'sisaTagihan' =>
                        $totalSisaSpp,
                ]
            );
        }

        /**
         * ========================================================
         * NON-SPP
         * ========================================================
         */
        $sudahDibayar = $this->getTotalDibayar(
            $tagihan
        );

        $sisaTagihan = $this->getSisaTagihan(
            $tagihan
        );

        return view(
            'admin.pembayaran.edit',
            compact(
                'pembayaran',
                'tagihan',
                'sudahDibayar',
                'sisaTagihan'
            )
        );
    }

    /**
     * ============================================================
     * UPDATE / SIMPAN KOREKSI PEMBAYARAN
     * ============================================================
     */
    public function update(
        Request $request,
        PembayaranTagihan $pembayaran
    ) {
        if ($pembayaran->status !== 'menunggu') {

            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Pembayaran yang sudah diproses tidak dapat dikoreksi.'
                );
        }

        $request->validate([
            'nominal' => [
                'required',
                'numeric',
                'min:1',
            ],

            'metode' => [
                'required',
                'in:transfer,qris',
            ],
        ], [
            'nominal.required' =>
                'Nominal pembayaran harus diisi.',

            'nominal.numeric' =>
                'Nominal pembayaran harus berupa angka.',

            'nominal.min' =>
                'Nominal pembayaran harus lebih dari 0.',

            'metode.required' =>
                'Metode pembayaran harus dipilih.',

            'metode.in' =>
                'Metode pembayaran tidak valid.',
        ]);

        /**
         * ========================================================
         * INI YANG MEMPERBAIKI ERROR:
         *
         * Undefined variable $nominal
         * ========================================================
         */
        $nominal = (float) $request->nominal;

        $pembayaran->load([
            'tagihan.kategori',
            'tagihan.tahunAjaran',
        ]);

        $tagihan = $pembayaran->tagihan;

        if (!$tagihan) {

            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Tagihan tidak ditemukan.'
                );
        }

        /**
         * ========================================================
         * KOREKSI PEMBAYARAN SPP
         * ========================================================
         */
        if ($this->isSpp($tagihan)) {

            $tagihanSpp = $this->getTagihanSpp($tagihan)
                ->filter(function ($spp) use ($tagihan) {

                    if ($spp->tahun > $tagihan->tahun) {
                        return true;
                    }

                    if (
                        $spp->tahun == $tagihan->tahun &&
                        $spp->bulan >= $tagihan->bulan
                    ) {
                        return true;
                    }

                    return false;
                })
                ->values();

            $totalSisaSpp = $tagihanSpp->sum(
                function ($spp) {
                    return $this->getSisaTagihan($spp);
                }
            );

            if ($nominal > $totalSisaSpp) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Nominal pembayaran melebihi total sisa SPP. Total sisa SPP: Rp ' .
                        number_format(
                            $totalSisaSpp,
                            0,
                            ',',
                            '.'
                        )
                    );
            }

            $pembayaran->update([
                'nominal' =>
                    $nominal,

                'metode' =>
                    $request->metode,
            ]);

            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'success',
                    'Nominal pembayaran berhasil dikoreksi. Saat disetujui, pembayaran akan otomatis dialokasikan ke beberapa bulan SPP.'
                );
        }

        /**
         * ========================================================
         * KOREKSI NON-SPP
         * ========================================================
         */
        $sudahDibayar = PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
            ->whereIn('status', [
                'dibayar',
                'disetujui',
            ])
            ->sum('nominal');

        $sisaTagihan = max(
            (float) $tagihan->nominal
                - (float) $sudahDibayar,
            0
        );

        if ($nominal > $sisaTagihan) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Nominal pembayaran tidak boleh melebihi sisa tagihan. Sisa tagihan: Rp ' .
                    number_format(
                        $sisaTagihan,
                        0,
                        ',',
                        '.'
                    )
                );
        }

        $pembayaran->update([
            'nominal' =>
                $nominal,

            'metode' =>
                $request->metode,
        ]);

        return redirect()
            ->route('admin.pembayaran.index')
            ->with(
                'success',
                'Nominal pembayaran berhasil dikoreksi.'
            );
    }

    /**
     * ============================================================
     * MENYETUJUI PEMBAYARAN
     *
     * Untuk pembayaran online SPP:
     * nominal dapat dialokasikan ke beberapa bulan.
     * ============================================================
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

        if (
            $pembayaran->nominal === null ||
            (float) $pembayaran->nominal <= 0
        ) {

            return back()->with(
                'error',
                'Nominal pembayaran belum ditentukan. Silakan lakukan koreksi terlebih dahulu.'
            );
        }

        return DB::transaction(function () use ($pembayaran) {

            $pembayaran->load([
                'tagihan.kategori',
                'tagihan.tahunAjaran',
            ]);

            $tagihan = $pembayaran->tagihan;

            if (!$tagihan) {

                return back()->with(
                    'error',
                    'Tagihan pembayaran tidak ditemukan.'
                );
            }

            $nominalPembayaran =
                (float) $pembayaran->nominal;

            /**
             * ====================================================
             * JIKA BUKAN SPP
             * ====================================================
             */
            if (!$this->isSpp($tagihan)) {

                $sudahDibayar = PembayaranTagihan::where(
                    'tagihan_id',
                    $tagihan->id
                )
                    ->whereIn('status', [
                        'dibayar',
                        'disetujui',
                    ])
                    ->where(
                        'id',
                        '!=',
                        $pembayaran->id
                    )
                    ->sum('nominal');

                $sisaTagihan = max(
                    (float) $tagihan->nominal
                        - (float) $sudahDibayar,
                    0
                );

                if ($nominalPembayaran > $sisaTagihan) {

                    return back()->with(
                        'error',
                        'Nominal pembayaran melebihi sisa tagihan. Sisa tagihan: Rp ' .
                        number_format(
                            $sisaTagihan,
                            0,
                            ',',
                            '.'
                        )
                    );
                }

                $pembayaran->update([
                    'status' =>
                        'dibayar',

                    'tanggal_disetujui' =>
                        now(),
                ]);

                return back()->with(
                    'success',
                    'Pembayaran berhasil disetujui.'
                );
            }

            /**
             * ====================================================
             * SPP
             * ====================================================
             */
            $tagihanSpp = $this->getTagihanSpp($tagihan)
                ->filter(function ($spp) use ($tagihan) {

                    if ($spp->tahun > $tagihan->tahun) {
                        return true;
                    }

                    if (
                        $spp->tahun == $tagihan->tahun &&
                        $spp->bulan >= $tagihan->bulan
                    ) {
                        return true;
                    }

                    return false;
                })
                ->values();

            if ($tagihanSpp->isEmpty()) {

                return back()->with(
                    'error',
                    'Tagihan SPP tidak ditemukan.'
                );
            }

            /**
             * ====================================================
             * CEK PEMBAYARAN MENUNGGU LAIN
             * ====================================================
             */
            $adaMenungguLain =
                PembayaranTagihan::where(
                    'status',
                    'menunggu'
                )
                    ->where(
                        'id',
                        '!=',
                        $pembayaran->id
                    )
                    ->whereHas(
                        'tagihan',
                        function ($query) use ($tagihan) {

                            $query->where(
                                'siswa_id',
                                $tagihan->siswa_id
                            )
                                ->where(
                                    'tahun_ajaran_id',
                                    $tagihan->tahun_ajaran_id
                                );
                        }
                    )
                    ->exists();

            if ($adaMenungguLain) {

                return back()->with(
                    'error',
                    'Masih terdapat pembayaran SPP lain yang menunggu persetujuan untuk siswa dan tahun ajaran ini. Proses pembayaran tersebut terlebih dahulu.'
                );
            }

            /**
             * ====================================================
             * ALOKASI NOMINAL
             * ====================================================
             */
            $sisaPembayaran =
                $nominalPembayaran;

            $alokasiPertama = true;

            foreach ($tagihanSpp as $spp) {

                if ($sisaPembayaran <= 0) {
                    break;
                }

                $sisaTagihan =
                    $this->getSisaTagihan($spp);

                if ($sisaTagihan <= 0) {
                    continue;
                }

                $nominalUntukTagihan = min(
                    $sisaPembayaran,
                    $sisaTagihan
                );

                /**
                 * =================================================
                 * PEMBAYARAN PERTAMA
                 * =================================================
                 */
                if ($alokasiPertama) {

                    $pembayaran->update([
                        'tagihan_id' =>
                            $spp->id,

                        'nominal' =>
                            $nominalUntukTagihan,

                        'status' =>
                            'dibayar',

                        'tanggal_disetujui' =>
                            now(),
                    ]);

                    $alokasiPertama = false;
                }

                /**
                 * =================================================
                 * PEMBAYARAN BERIKUTNYA
                 * =================================================
                 */
                else {

                    PembayaranTagihan::create([
                        'tagihan_id' =>
                            $spp->id,

                        'user_id' =>
                            $pembayaran->user_id,

                        'nominal' =>
                            $nominalUntukTagihan,

                        'metode' =>
                            $pembayaran->metode,

                        'bukti_pembayaran' =>
                            $pembayaran->bukti_pembayaran,

                        'status' =>
                            'dibayar',

                        'catatan' =>
                            $pembayaran->catatan,

                        'tanggal_kirim' =>
                            $pembayaran->tanggal_kirim,

                        'tanggal_disetujui' =>
                            now(),
                    ]);
                }

                $sisaPembayaran -=
                    $nominalUntukTagihan;
            }

            /**
             * ====================================================
             * JIKA ADA UANG YANG BELUM TERPAKAI
             * ====================================================
             */
            if ($sisaPembayaran > 0) {

                throw new \Exception(
                    'Nominal pembayaran melebihi total sisa tagihan SPP.'
                );
            }

            return back()->with(
                'success',
                'Pembayaran berhasil disetujui dan otomatis dialokasikan ke tagihan SPP bulan berikutnya.'
            );
        });
    }

    /**
     * ============================================================
     * MENOLAK PEMBAYARAN
     * ============================================================
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
            'catatan' => [
                'required',
                'string',
                'max:500',
            ],
        ]);

        $pembayaran->update([
            'status' =>
                'ditolak',

            'catatan' =>
                $request->catatan,
        ]);

        return back()->with(
            'success',
            'Pembayaran ditolak.'
        );
    }
}