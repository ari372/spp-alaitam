<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Tagihan;
use App\Models\PembayaranTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranOrangTuaController extends Controller
{
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
     * CARI TAGIHAN SPP UNTUK SISWA DAN TAHUN AJARAN YANG SAMA
     * ============================================================
     */
    private function getTagihanSpp(Tagihan $tagihan)
    {
        return Tagihan::where(
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
        ->first();
    }


    /**
     * ============================================================
     * HITUNG TOTAL SPP YANG SUDAH DIBAYAR
     * ============================================================
     */
    private function getTotalSppDibayar(Tagihan $spp)
    {
        return PembayaranTagihan::where(
            'tagihan_id',
            $spp->id
        )
        ->whereIn('status', [
            'dibayar',
            'disetujui'
        ])
        ->sum('nominal');
    }


    /**
     * ============================================================
     * FORM PEMBAYARAN
     * ============================================================
     */
    public function create(Tagihan $tagihan)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | CEK DATA ORANG TUA
        |--------------------------------------------------------------------------
        */

        $orangTua = $user->orangTua;

        if (!$orangTua) {
            abort(
                404,
                'Data orang tua tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN TAGIHAN MILIK ANAK
        |--------------------------------------------------------------------------
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


        /*
        |--------------------------------------------------------------------------
        | LOAD RELASI
        |--------------------------------------------------------------------------
        */

        $tagihan->load([
            'siswa',
            'tahunAjaran',
            'kategori',
            'pembayaran'
        ]);


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAGIHAN YANG SUDAH DIBAYAR
        |--------------------------------------------------------------------------
        */

        $totalDibayar = $tagihan->pembayaran
            ->whereIn('status', [
                'dibayar',
                'disetujui'
            ])
            ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | HITUNG SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            (float) $tagihan->nominal
                - (float) $totalDibayar,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | JIKA TAGIHAN SUDAH LUNAS
        |--------------------------------------------------------------------------
        */

        if ($sisaTagihan <= 0) {

            return redirect()
                ->route('orangtua.dashboard')
                ->with(
                    'error',
                    'Tagihan ini sudah lunas.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK PEMBAYARAN YANG MASIH MENUNGGU
        |--------------------------------------------------------------------------
        */

        $sedangDiproses = $tagihan->pembayaran
            ->where(
                'status',
                'menunggu'
            )
            ->count();

        if ($sedangDiproses > 0) {

            return redirect()
                ->route('orangtua.dashboard')
                ->with(
                    'error',
                    'Pembayaran untuk tagihan ini sedang menunggu persetujuan admin.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK KHUSUS PTS / UJIAN
        |--------------------------------------------------------------------------
        | SPP HARUS LUNAS TERLEBIH DAHULU
        |--------------------------------------------------------------------------
        */

        if ($this->isPtsAtauUjian($tagihan)) {

            $spp = $this->getTagihanSpp($tagihan);


            /*
            |--------------------------------------------------------------------------
            | JIKA TAGIHAN SPP TIDAK DITEMUKAN
            |--------------------------------------------------------------------------
            */

            if (!$spp) {

                return redirect()
                    ->route('orangtua.dashboard')
                    ->with(
                        'error',
                        'Tagihan SPP untuk tahun ajaran ini belum ditemukan.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | HITUNG TOTAL SPP YANG SUDAH DIBAYAR
            |--------------------------------------------------------------------------
            */

            $totalSPPDibayar = $this->getTotalSppDibayar(
                $spp
            );


            /*
            |--------------------------------------------------------------------------
            | HITUNG SISA SPP
            |--------------------------------------------------------------------------
            */

            $sisaSPP = max(
                (float) $spp->nominal
                    - (float) $totalSPPDibayar,
                0
            );


            /*
            |--------------------------------------------------------------------------
            | SPP BELUM LUNAS
            |--------------------------------------------------------------------------
            */

            if ($sisaSPP > 0) {

                return redirect()
                    ->route('orangtua.dashboard')
                    ->with(
                        'error',
                        'SPP harus lunas terlebih dahulu sebelum membayar PTS/Ujian.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN FORM
        |--------------------------------------------------------------------------
        */

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
     */
    public function store(
        Request $request,
        Tagihan $tagihan
    ) {
        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | CEK DATA ORANG TUA
        |--------------------------------------------------------------------------
        */

        $orangTua = $user->orangTua;

        if (!$orangTua) {

            abort(
                404,
                'Data orang tua tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PASTIKAN TAGIHAN MILIK ANAK
        |--------------------------------------------------------------------------
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


        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'nominal' => [
                'required',
                'numeric',
                'min:1'
            ],

            'metode' => [
                'required',
                'in:transfer,qris'
            ],

            'bukti_pembayaran' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | LOAD RELASI
        |--------------------------------------------------------------------------
        */

        $tagihan->load([
            'siswa',
            'tahunAjaran',
            'kategori',
            'pembayaran'
        ]);


        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMBAYARAN YANG SUDAH DISETUJUI
        |--------------------------------------------------------------------------
        */

        $totalDibayar = PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
        ->whereIn('status', [
            'dibayar',
            'disetujui'
        ])
        ->sum('nominal');


        /*
        |--------------------------------------------------------------------------
        | SISA TAGIHAN
        |--------------------------------------------------------------------------
        */

        $sisaTagihan = max(
            (float) $tagihan->nominal
                - (float) $totalDibayar,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | JIKA SUDAH LUNAS
        |--------------------------------------------------------------------------
        */

        if ($sisaTagihan <= 0) {

            return back()
                ->with(
                    'error',
                    'Tagihan sudah lunas.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | NOMINAL TIDAK BOLEH MELEBIHI SISA
        |--------------------------------------------------------------------------
        */

        if (
            (float) $validated['nominal']
            > $sisaTagihan
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Nominal pembayaran melebihi sisa tagihan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK PEMBAYARAN MENUNGGU
        |--------------------------------------------------------------------------
        */

        $menunggu = PembayaranTagihan::where(
            'tagihan_id',
            $tagihan->id
        )
        ->where(
            'status',
            'menunggu'
        )
        ->exists();

        if ($menunggu) {

            return back()
                ->with(
                    'error',
                    'Masih ada pembayaran yang menunggu persetujuan admin.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK KHUSUS PTS / UJIAN
        |--------------------------------------------------------------------------
        | SPP HARUS LUNAS SEBELUM PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        if ($this->isPtsAtauUjian($tagihan)) {

            $spp = $this->getTagihanSpp($tagihan);


            /*
            |--------------------------------------------------------------------------
            | JIKA SPP TIDAK DITEMUKAN
            |--------------------------------------------------------------------------
            */

            if (!$spp) {

                return back()
                    ->with(
                        'error',
                        'Tagihan SPP untuk tahun ajaran ini belum ditemukan.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | TOTAL SPP
            |--------------------------------------------------------------------------
            */

            $totalSPP = $this->getTotalSppDibayar(
                $spp
            );


            /*
            |--------------------------------------------------------------------------
            | SISA SPP
            |--------------------------------------------------------------------------
            */

            $sisaSPP = max(
                (float) $spp->nominal
                    - (float) $totalSPP,
                0
            );


            /*
            |--------------------------------------------------------------------------
            | SPP BELUM LUNAS
            |--------------------------------------------------------------------------
            */

            if ($sisaSPP > 0) {

                return back()
                    ->with(
                        'error',
                        'SPP harus lunas terlebih dahulu sebelum membayar PTS/Ujian.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPLOAD BUKTI PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $file = $request->file(
            'bukti_pembayaran'
        );

        $path = $file->store(
            'bukti-pembayaran',
            'public'
        );


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        PembayaranTagihan::create([

            'tagihan_id' => $tagihan->id,

            'user_id' => $user->id,

            'nominal' => $validated['nominal'],

            'metode' => $validated['metode'],

            'bukti_pembayaran' => $path,

            'status' => 'menunggu',

            'tanggal_kirim' => now(),

        ]);


        /*
        |--------------------------------------------------------------------------
        | KEMBALI KE DASHBOARD
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('orangtua.dashboard')
            ->with(
                'success',
                'Bukti pembayaran berhasil dikirim. Silakan menunggu persetujuan admin.'
            );
    }

    public function downloadBukti(PembayaranTagihan $pembayaran)
{
    $user = Auth::user();

    $orangTua = OrangTua::where('user_id', $user->id)->first();

    if (!$orangTua) {
        abort(403, 'Data orang tua tidak ditemukan.');
    }

    $pembayaran->load([
        'tagihan.siswa.kelas',
        'tagihan.kategori',
        'tagihan.tahunAjaran',
    ]);

    if (!$pembayaran->tagihan) {
        abort(404, 'Tagihan pembayaran tidak ditemukan.');
    }

    if (
        !$pembayaran->tagihan->siswa ||
        $pembayaran->tagihan->siswa->orang_tua_id != $orangTua->id
    ) {
        abort(403, 'Anda tidak memiliki akses ke pembayaran ini.');
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'orangtua.pembayaran.bukti-pdf',
        compact('pembayaran')
    );

    $pdf->setPaper('A4', 'portrait');

    return $pdf->download(
        'bukti-pembayaran-' . $pembayaran->id . '.pdf'
    );
}
}