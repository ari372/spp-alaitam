<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan Pembayaran</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 18px 20px 20px 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        /* ================================
           HEADER
        ================================= */

        .pdf-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .pdf-header h2 {
            margin: 0 0 4px;
            color: #0f5132;
            font-size: 18px;
            font-weight: bold;
        }

        .pdf-header p {
            margin: 0;
            color: #666666;
            font-size: 9px;
        }

        /* ================================
           FILTER INFO
        ================================= */

        .pdf-period {
            text-align: right;
            margin-bottom: 10px;
            color: #666666;
            font-size: 8px;
        }

        /* ================================
           TABLE
        ================================= */

        .laporan-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .laporan-table th {
            background: #0f5132;
            color: #ffffff;
            border: 1px solid #0b3d25;
            padding: 6px 4px;
            font-size: 7.5px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .laporan-table td {
            border: 1px solid #d7ddd9;
            padding: 5px 4px;
            font-size: 7.5px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .laporan-table tbody tr:nth-child(even) {
            background: #f7faf8;
        }

        /* ================================
           LEBAR KOLOM
           TOTAL = 100%
        ================================= */

        .col-no {
            width: 4%;
        }

        .col-siswa {
            width: 14%;
        }

        .col-nis {
            width: 8%;
        }

        .col-kelas {
            width: 7%;
        }

        .col-tahun {
            width: 10%;
        }

        .col-bulan {
            width: 10%;
        }

        .col-kategori {
            width: 10%;
        }

        .col-nominal {
            width: 9%;
        }

        .col-status {
            width: 9%;
        }

        /* ================================
           ALIGNMENT
        ================================= */

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        /* ================================
           NOMINAL
        ================================= */

        .nominal {
            white-space: nowrap;
            text-align: right;
        }

        /* ================================
           STATUS
        ================================= */

        .status-lunas {
            color: #198754;
            font-weight: bold;
        }

        .status-belum {
            color: #dc3545;
            font-weight: bold;
        }

        /* ================================
           SUMMARY
        ================================= */

        .summary-wrapper {
            width: 100%;
            margin-top: 14px;
        }

        .summary {
            width: 38%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary td {
            border: none;
            padding: 4px 5px;
            font-size: 8px;
        }

        .summary-label {
            text-align: left;
            color: #555555;
        }

        .summary-value {
            text-align: right;
            color: #333333;
        }

        .summary-total td {
            padding-top: 6px;
            border-top: 1.5px solid #0f5132;
            color: #0f5132;
            font-weight: bold;
        }

        /* ================================
           FOOTER
        ================================= */

        .pdf-footer {
            margin-top: 15px;
            text-align: right;
            font-size: 7px;
            color: #777777;
        }
    </style>
</head>

<body>

    {{-- ================================
         HEADER
    ================================= --}}

    <div class="pdf-header">

        <h2>
            SMP Plus Al-I'tam
        </h2>

        <p>
            Laporan Pembayaran Siswa
        </p>

    </div>


    {{-- ================================
         PERIODE
    ================================= --}}

    <div class="pdf-period">

        @if(request('bulan') && request('tahun'))

            Periode:

            {{ \Carbon\Carbon::create()
                ->month((int) request('bulan'))
                ->translatedFormat('F') }}

            {{ request('tahun') }}

        @elseif(request('tahun'))

            Tahun:
            {{ request('tahun') }}

        @elseif(request('bulan'))

            Bulan:

            {{ \Carbon\Carbon::create()
                ->month((int) request('bulan'))
                ->translatedFormat('F') }}

        @else

            Semua Periode

        @endif

    </div>


    {{-- ================================
         TABEL
    ================================= --}}

    <table class="laporan-table">

        <thead>

            <tr>

                <th class="col-no">
                    No
                </th>

                <th class="col-siswa">
                    Siswa
                </th>

                <th class="col-nis">
                    NIS
                </th>

                <th class="col-kelas">
                    Kelas
                </th>

                <th class="col-tahun">
                    Tahun Ajaran
                </th>

                <th class="col-bulan">
                    Bulan
                </th>

                <th class="col-kategori">
                    Kategori
                </th>

                <th class="col-nominal">
                    Tagihan
                </th>

                <th class="col-nominal">
                    Dibayar
                </th>

                <th class="col-nominal">
                    Sisa
                </th>

                <th class="col-status">
                    Status
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($pembayaran as $item)

                @php

                    $tagihan = $item->tagihan;

                    /*
                    |--------------------------------------------------------------------------
                    | NOMINAL TAGIHAN
                    |--------------------------------------------------------------------------
                    */

                    $nominalTagihan = (float) (
                        $tagihan?->nominal ?? 0
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL PEMBAYARAN
                    |--------------------------------------------------------------------------
                    |
                    | Gunakan seluruh pembayaran yang sudah dibayar
                    | pada tagihan tersebut.
                    |
                    */

                    $totalDibayar = 0;

                    if ($tagihan) {

                        if ($tagihan->relationLoaded('pembayaran')) {

                            $totalDibayar = (float) $tagihan
                                ->pembayaran
                                ->whereIn(
                                    'status',
                                    [
                                        'dibayar',
                                        'disetujui'
                                    ]
                                )
                                ->sum('nominal');

                        } else {

                            $totalDibayar = (float) \App\Models\PembayaranTagihan::where(
                                'tagihan_id',
                                $tagihan->id
                            )
                            ->whereIn(
                                'status',
                                [
                                    'dibayar',
                                    'disetujui'
                                ]
                            )
                            ->sum('nominal');

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SISA
                    |--------------------------------------------------------------------------
                    */

                    $sisa = max(
                        0,
                        $nominalTagihan - $totalDibayar
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | BULAN / PERIODE TAGIHAN
                    |--------------------------------------------------------------------------
                    |
                    | SPP:
                    | gunakan bulan dan tahun pada tagihan.
                    |
                    | Baju / PTS / Ujian:
                    | gunakan bulan dari tanggal pembayaran.
                    |
                    */

                    $bulanTagihan = '-';

                    $kategoriNama = strtolower(
                        trim(
                            $tagihan?->kategori?->nama ?? ''
                        )
                    );

                    $isSpp = $kategoriNama === 'spp';


                    /*
                    |--------------------------------------------------------------------------
                    | SPP
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $isSpp &&
                        $tagihan &&
                        $tagihan->bulan &&
                        $tagihan->tahun
                    ) {

                        $bulanTagihan = \Carbon\Carbon::create(
                            $tagihan->tahun,
                            $tagihan->bulan,
                            1
                        )->translatedFormat('F Y');

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | NON-SPP
                    |--------------------------------------------------------------------------
                    |
                    | Contoh:
                    |
                    | Baju  -> September 2028
                    | PTS   -> Oktober 2028
                    | Ujian -> November 2028
                    |
                    */

                    else {

                        $tanggalPembayaran =
                            $item->tanggal_disetujui
                            ?? $item->tanggal_kirim
                            ?? $item->created_at;

                        if ($tanggalPembayaran) {

                            $bulanTagihan = \Carbon\Carbon::parse(
                                $tanggalPembayaran
                            )
                            ->timezone('Asia/Jakarta')
                            ->translatedFormat('F Y');

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    $statusLunas = $sisa <= 0;


                    /*
                    |--------------------------------------------------------------------------
                    | TANGGAL BAYAR
                    |--------------------------------------------------------------------------
                    */

                    $tanggalBayar = $item->tanggal_kirim
                        ? \Carbon\Carbon::parse(
                            $item->tanggal_kirim
                        )
                        ->timezone('Asia/Jakarta')
                        ->format('d-m-Y H:i')
                        : '-';

                @endphp


                <tr>

                    {{-- NO --}}

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>


                    {{-- SISWA --}}

                    <td class="text-left">
                        {{ $tagihan?->siswa?->nama ?? '-' }}
                    </td>


                    {{-- NIS --}}

                    <td class="text-center">
                        {{ $tagihan?->siswa?->nis ?? '-' }}
                    </td>


                    {{-- KELAS --}}

                    <td class="text-center">

                        {{
                            $tagihan?->siswa?->kelas?->nama_kelas
                            ?? $tagihan?->siswa?->kelas?->nama
                            ?? '-'
                        }}

                    </td>


                    {{-- TAHUN AJARAN --}}

                    <td class="text-center">
                        {{ $tagihan?->tahunAjaran?->nama ?? '-' }}
                    </td>


                    {{-- BULAN --}}

                    <td class="text-center">
                        {{ $bulanTagihan }}
                    </td>


                    {{-- KATEGORI --}}

                    <td class="text-left">
                        {{ $tagihan?->kategori?->nama ?? '-' }}
                    </td>


                    {{-- TAGIHAN --}}

                    <td class="nominal">

                        Rp
                        {{ number_format(
                            $nominalTagihan,
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- DIBAYAR --}}

                    <td class="nominal">

                        Rp
                        {{ number_format(
                            $totalDibayar,
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- SISA --}}

                    <td class="nominal">

                        Rp
                        {{ number_format(
                            $sisa,
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- STATUS --}}

                    <td class="text-center">

                        @if($statusLunas)

                            <span class="status-lunas">
                                Lunas
                            </span>

                        @else

                            <span class="status-belum">
                                Belum Lunas
                            </span>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="11"
                        class="text-center"
                    >
                        Belum ada data laporan.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- ================================
         SUMMARY
    ================================= --}}

    <div class="summary-wrapper">

        <table class="summary">

            <tr>

                <td class="summary-label">
                    Total Tagihan
                </td>

                <td class="summary-value">

                    Rp
                    {{ number_format(
                        $totalTagihan ?? 0,
                        0,
                        ',',
                        '.'
                    ) }}

                </td>

            </tr>


            <tr>

                <td class="summary-label">
                    Total Pembayaran
                </td>

                <td class="summary-value">

                    Rp
                    {{ number_format(
                        $totalPembayaran ?? 0,
                        0,
                        ',',
                        '.'
                    ) }}

                </td>

            </tr>


            <tr class="summary-total">

                <td>
                    Sisa Tagihan
                </td>

                <td class="summary-value">

                    Rp
                    {{ number_format(
                        $sisaTagihan ?? 0,
                        0,
                        ',',
                        '.'
                    ) }}

                </td>

            </tr>

        </table>

    </div>


    {{-- ================================
         FOOTER
    ================================= --}}

    <div class="pdf-footer">

        Dicetak pada:

        {{ now()
            ->timezone('Asia/Jakarta')
            ->format('d/m/Y H:i') }}

    </div>

</body>

</html>