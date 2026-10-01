<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\PembayaranTagihan;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranOrangTuaController extends Controller
{
    /**
     * ============================================================
     * CEK APAKAH KATEGORI ADALAH SPP
     * ============================================================
     */
    private function isSpp(Tagihan $tagihan)
    {
        $namaKategori = strtolower(
            trim($tagihan->kategori->nama ?? '')
        );

        return $namaKategori === 'spp';
    }

    /**
     * ============================================================
     * CEK APAKAH KATEGORI ADALAH PTS ATAU UJIAN
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
     * AMBIL SELURUH TAGIHAN SPP DALAM 1 TAHUN AJARAN
     * ============================================================
     *
     * Contoh:
     *
     * Juli       2028
     * Agustus    2028
     * September  2028
     * Oktober    2028
     * November   2028
     * Desember   2028
     * Januari    2029
     * Februari   2029
     * Maret      2029
     * April      2029
     * Mei        2029
     * Juni       2029
     *
     * Semua dianggap sebagai satu kewajiban SPP tahunan.
     */
    private function getTagihanSpp(Tagihan $tagihan)
    {
        return Tagihan::with([
            'kategori',
            'tahunAjaran',
        ])
            ->where(
                'siswa_id',
                $tagihan->siswa_id
            )
            ->where(
                'tahun_ajaran_id',
                $tagihan->tahun_ajaran_id
            )
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
     * HITUNG TOTAL PEMBAYARAN SATU TAGIHAN
     * ============================================================
     *
     * Hanya pembayaran yang sudah disetujui
     * yang dihitung sebagai pembayaran.
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
     *
     * Digunakan untuk NON-SPP.
     *
     * Untuk SPP kita menggunakan getSisaSpp().
     */
    private function getSisaTagihan(Tagihan $tagihan)
    {
        $totalDibayar = $this->getTotalDibayar(
            $tagihan
        );

        return max(
            (float) $tagihan->nominal
                - $totalDibayar,
            0
        );
    }

    /**
     * ============================================================
     * HITUNG TOTAL NOMINAL SPP 1 TAHUN AJARAN
     * ============================================================
     *
     * Contoh:
     *
     * 12 x Rp112.500
     * = Rp1.350.000
     */
    private function getTotalSpp(Tagihan $tagihan)
    {
        return (float) $this->getTagihanSpp($tagihan)
            ->sum(function ($spp) {
                return (float) $spp->nominal;
            });
    }

    /**
     * ============================================================
     * HITUNG TOTAL SPP YANG SUDAH DIBAYAR
     * ============================================================
     *
     * Semua pembayaran dari 12 tagihan SPP
     * dijumlahkan menjadi satu.
     */
    private function getTotalSppDibayar(Tagihan $tagihan)
    {
        $tagihanSpp = $this->getTagihanSpp(
            $tagihan
        );

        if ($tagihanSpp->isEmpty()) {
            return 0;
        }

        $total = 0;

        foreach ($tagihanSpp as $spp) {
            $total += $this->getTotalDibayar(
                $spp
            );
        }

        return $total;
    }

    /**
     * ============================================================
     * HITUNG SISA SPP 1 TAHUN AJARAN
     * ============================================================
     *
     * Contoh:
     *
     * Total SPP      Rp1.350.000
     * Sudah dibayar  Rp850.000
     * -----------------------
     * Sisa           Rp500.000
     *
     * Jadi orang tua dapat melakukan pelunasan
     * Rp500.000 meskipun tagihan bulan berjalan
     * misalnya hanya Rp112.500.
     */
    private function getSisaSpp(Tagihan $tagihan)
    {
        $totalSpp = $this->getTotalSpp(
            $tagihan
        );

        $totalDibayar = $this->getTotalSppDibayar(
            $tagihan
        );

        return max(
            $totalSpp - $totalDibayar,
            0
        );
    }

    /**
     * ============================================================
     * CEK APAKAH SPP TAHUNAN SUDAH LUNAS
     * ============================================================
     *
     * SPP dianggap lunas apabila:
     *
     * total pembayaran >= total nominal
     * seluruh SPP dalam tahun ajaran.
     *
     * Tidak perlu mengecek setiap bulan satu per satu.
     */
    private function isSppLunas(Tagihan $tagihan)
    {
        $tagihanSpp = $this->getTagihanSpp(
            $tagihan
        );

        if ($tagihanSpp->isEmpty()) {
            return false;
        }

        return $this->getSisaSpp(
            $tagihan
        ) <= 0;
    }

    /**
     * ============================================================
     * CEK PEMBAYARAN MENUNGGU PADA TAGIHAN
     * ============================================================
     */
    private function adaPembayaranMenunggu(Tagihan $tagihan)
    {
        return PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
            ->where(
                'status',
                'menunggu'
            )
            ->exists();
    }

    /**
     * ============================================================
     * FORM PEMBAYARAN
     * ============================================================
     */
    public function create(Tagihan $tagihan)
    {
        $user = Auth::user();

        /**
         * ========================================================
         * CEK DATA ORANG TUA
         * ========================================================
         */
        $orangTua = $user->orangTua;

        if (!$orangTua) {
            abort(
                404,
                'Data orang tua tidak ditemukan.'
            );
        }

        /**
         * ========================================================
         * PASTIKAN TAGIHAN MILIK ANAK
         * ========================================================
         */
        $milikOrangTua = $orangTua->siswa()
            ->where(
                'id',
                $tagihan->siswa_id
            )
            ->exists();

        if (!$milikOrangTua) {
            abort(
                403,
                'Anda tidak memiliki akses ke tagihan ini.'
            );
        }

        /**
         * ========================================================
         * LOAD RELASI
         * ========================================================
         */
        $tagihan->load([
            'siswa',
            'tahunAjaran',
            'kategori',
            'pembayaran',
        ]);

        /**
         * ========================================================
         * JIKA SPP
         *
         * Sisa pembayaran dihitung berdasarkan
         * TOTAL SPP SATU TAHUN AJARAN.
         * ========================================================
         */
        if ($this->isSpp($tagihan)) {

            $tagihanSpp = $this->getTagihanSpp(
                $tagihan
            );

            if ($tagihanSpp->isEmpty()) {
                return redirect()
                    ->route('orangtua.dashboard')
                    ->with(
                        'error',
                        'Tagihan SPP untuk tahun ajaran ini belum ditemukan.'
                    );
            }

            /**
             * SPP SUDAH LUNAS
             */
            if ($this->isSppLunas($tagihan)) {
                return redirect()
                    ->route('orangtua.dashboard')
                    ->with(
                        'error',
                        'SPP tahun ajaran ini sudah lunas.'
                    );
            }

            /**
             * CEK PEMBAYARAN MENUNGGU
             *
             * Pembayaran tetap terkait dengan
             * bulan yang dipilih.
             */
            if (
                $this->adaPembayaranMenunggu(
                    $tagihan
                )
            ) {
                return redirect()
                    ->route('orangtua.dashboard')
                    ->with(
                        'error',
                        'Pembayaran untuk tagihan ini sedang menunggu persetujuan admin.'
                    );
            }

            /**
             * UNTUK SPP:
             *
             * totalDibayar = total SPP yang sudah dibayar
             * sisaTagihan = sisa SPP satu tahun
             */
            $totalDibayar = $this->getTotalSppDibayar(
                $tagihan
            );

            $sisaTagihan = $this->getSisaSpp(
                $tagihan
            );

            return view(
                'orangtua.pembayaran.create',
                compact(
                    'tagihan',
                    'totalDibayar',
                    'sisaTagihan'
                )
            );
        }

        /**
         * ========================================================
         * NON-SPP
         * ========================================================
         */
        $totalDibayar = $this->getTotalDibayar(
            $tagihan
        );

        $sisaTagihan = $this->getSisaTagihan(
            $tagihan
        );

        /**
         * TAGIHAN NON-SPP SUDAH LUNAS
         */
        if ($sisaTagihan <= 0) {
            return redirect()
                ->route('orangtua.dashboard')
                ->with(
                    'error',
                    'Tagihan ini sudah lunas.'
                );
        }

        /**
         * CEK PEMBAYARAN MENUNGGU
         */
        if (
            $this->adaPembayaranMenunggu(
                $tagihan
            )
        ) {
            return redirect()
                ->route('orangtua.dashboard')
                ->with(
                    'error',
                    'Pembayaran untuk tagihan ini sedang menunggu persetujuan admin.'
                );
        }

        /**
         * ========================================================
         * CEK PTS / UJIAN
         *
         * SPP TAHUN AJARAN HARUS LUNAS.
         * ========================================================
         */
        if (
            $this->isPtsAtauUjian(
                $tagihan
            )
        ) {
            $tagihanSpp = $this->getTagihanSpp(
                $tagihan
            );

            if ($tagihanSpp->isEmpty()) {
                return redirect()
                    ->route('orangtua.dashboard')
                    ->with(
                        'error',
                        'Tagihan SPP untuk tahun ajaran ini belum ditemukan.'
                    );
            }

            $sisaSPP = $this->getSisaSpp(
                $tagihan
            );

            if ($sisaSPP > 0) {
                return redirect()
                    ->route('orangtua.dashboard')
                    ->with(
                        'error',
                        'SPP harus lunas terlebih dahulu sebelum membayar PTS/Ujian.'
                    );
            }
        }

        return view(
            'orangtua.pembayaran.create',
            compact(
                'tagihan',
                'totalDibayar',
                'sisaTagihan'
            )
        );
    }

    /**
     * ============================================================
     * SIMPAN PEMBAYARAN
     * ============================================================
     *
     * Orang tua tidak memasukkan nominal.
     *
     * Nominal akan diperiksa dan ditentukan admin.
     */
    public function store(
        Request $request,
        Tagihan $tagihan
    ) {
        $user = Auth::user();

        /**
         * ========================================================
         * CEK DATA ORANG TUA
         * ========================================================
         */
        $orangTua = $user->orangTua;

        if (!$orangTua) {
            abort(
                404,
                'Data orang tua tidak ditemukan.'
            );
        }

        /**
         * ========================================================
         * PASTIKAN TAGIHAN MILIK ANAK
         * ========================================================
         */
        $milikOrangTua = $orangTua->siswa()
            ->where(
                'id',
                $tagihan->siswa_id
            )
            ->exists();

        if (!$milikOrangTua) {
            abort(
                403,
                'Anda tidak memiliki akses ke tagihan ini.'
            );
        }

        /**
         * ========================================================
         * VALIDASI
         * ========================================================
         */
        $validated = $request->validate([
            'metode' => [
                'required',
                'in:transfer,qris',
            ],
            'bukti_pembayaran' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ]);

        /**
         * ========================================================
         * LOAD RELASI
         * ========================================================
         */
        $tagihan->load([
            'siswa',
            'tahunAjaran',
            'kategori',
            'pembayaran',
        ]);

        /**
         * ========================================================
         * JIKA SPP
         *
         * Cek berdasarkan TOTAL SPP TAHUNAN.
         * ========================================================
         */
        if ($this->isSpp($tagihan)) {

            $tagihanSpp = $this->getTagihanSpp(
                $tagihan
            );

            if ($tagihanSpp->isEmpty()) {
                return back()
                    ->with(
                        'error',
                        'Tagihan SPP untuk tahun ajaran ini belum ditemukan.'
                    );
            }

            /**
             * SPP SUDAH LUNAS
             */
            $sisaSpp = $this->getSisaSpp(
                $tagihan
            );

            if ($sisaSpp <= 0) {
                return back()
                    ->with(
                        'error',
                        'SPP tahun ajaran ini sudah lunas.'
                    );
            }

            /**
             * CEK PEMBAYARAN MENUNGGU
             * pada bulan yang dipilih.
             */
            if (
                $this->adaPembayaranMenunggu(
                    $tagihan
                )
            ) {
                return back()
                    ->with(
                        'error',
                        'Masih ada pembayaran SPP yang menunggu persetujuan admin pada tagihan ini.'
                    );
            }
        } else {

            /**
             * ====================================================
             * NON-SPP
             * ====================================================
             */
            $sisaTagihan = $this->getSisaTagihan(
                $tagihan
            );

            if ($sisaTagihan <= 0) {
                return back()
                    ->with(
                        'error',
                        'Tagihan sudah lunas.'
                    );
            }

            /**
             * CEK PEMBAYARAN MENUNGGU
             */
            if (
                $this->adaPembayaranMenunggu(
                    $tagihan
                )
            ) {
                return back()
                    ->with(
                        'error',
                        'Masih ada pembayaran yang menunggu persetujuan admin.'
                    );
            }

            /**
             * ====================================================
             * CEK PTS / UJIAN
             *
             * SPP TAHUNAN HARUS LUNAS.
             * ====================================================
             */
            if (
                $this->isPtsAtauUjian(
                    $tagihan
                )
            ) {
                $tagihanSpp = $this->getTagihanSpp(
                    $tagihan
                );

                if ($tagihanSpp->isEmpty()) {
                    return back()
                        ->with(
                            'error',
                            'Tagihan SPP untuk tahun ajaran ini belum ditemukan.'
                        );
                }

                $sisaSPP = $this->getSisaSpp(
                    $tagihan
                );

                if ($sisaSPP > 0) {
                    return back()
                        ->with(
                            'error',
                            'SPP harus lunas terlebih dahulu sebelum membayar PTS/Ujian.'
                        );
                }
            }
        }

        /**
         * ========================================================
         * UPLOAD BUKTI PEMBAYARAN
         * ========================================================
         */
        $file = $request->file(
            'bukti_pembayaran'
        );

        $path = $file->store(
            'bukti-pembayaran',
            'public'
        );

        /**
         * ========================================================
         * SIMPAN PEMBAYARAN
         * ========================================================
         *
         * Untuk SPP:
         *
         * nominal = NULL
         *
         * Admin nanti memasukkan nominal sebenarnya.
         *
         * Contoh:
         *
         * Sisa SPP = Rp500.000
         *
         * Admin dapat mengisi Rp500.000.
         */
        PembayaranTagihan::create([
            'tagihan_id' => $tagihan->id,
            'user_id' => $user->id,
            'nominal' => null,
            'metode' => $validated['metode'],
            'bukti_pembayaran' => $path,
            'status' => 'menunggu',
            'tanggal_kirim' => now(),
        ]);

        /**
         * ========================================================
         * KEMBALI KE DASHBOARD
         * ========================================================
         */
        return redirect()
            ->route('orangtua.dashboard')
            ->with(
                'success',
                'Bukti pembayaran berhasil dikirim. Silakan menunggu persetujuan admin.'
            );
    }

    /**
     * ============================================================
     * DOWNLOAD BUKTI PEMBAYARAN PDF
     * ============================================================
     */
    public function downloadBukti(
        PembayaranTagihan $pembayaran
    ) {
        $user = Auth::user();

        $orangTua = OrangTua::where(
            'user_id',
            $user->id
        )->first();

        if (!$orangTua) {
            abort(
                403,
                'Data orang tua tidak ditemukan.'
            );
        }

        /**
         * ========================================================
         * LOAD RELASI
         * ========================================================
         */
        $pembayaran->load([
            'tagihan.siswa.kelas',
            'tagihan.kategori',
            'tagihan.tahunAjaran',
        ]);

        /**
         * ========================================================
         * CEK TAGIHAN
         * ========================================================
         */
        if (!$pembayaran->tagihan) {
            abort(
                404,
                'Tagihan pembayaran tidak ditemukan.'
            );
        }

        /**
         * ========================================================
         * CEK HAK AKSES
         * ========================================================
         */
        if (
            !$pembayaran->tagihan->siswa ||
            $pembayaran->tagihan->siswa->orang_tua_id
                != $orangTua->id
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke pembayaran ini.'
            );
        }

        /**
         * ========================================================
         * BUAT PDF
         * ========================================================
         */
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'orangtua.pembayaran.bukti-pdf',
            compact('pembayaran')
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        /**
         * ========================================================
         * DOWNLOAD
         * ========================================================
         */
        return $pdf->download(
            'bukti-pembayaran-' .
            $pembayaran->id .
            '.pdf'
        );
    }
}