<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan Pembayaran</title>

    {{-- Ambil CSS langsung dari resources --}}
    <style>
        {!! file_get_contents(resource_path('css/admin/laporan.css')) !!}
    </style>

    {{-- CSS khusus PDF --}}
    <style>

        @page {
            margin: 25px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* ==============================
           HEADER PDF
        ============================== */

        .pdf-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .pdf-header h2 {
            margin: 0 0 5px;
            color: #0f5132;
            font-size: 20px;
            font-weight: bold;
        }

        .pdf-header p {
            margin: 0;
            color: #666;
            font-size: 11px;
        }


        /* ==============================
           TABLE
        ============================== */

        .laporan-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .laporan-table th {
            background: #0f5132;
            color: #ffffff;
            padding: 7px 5px;
            border: 1px solid #0b3d25;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
        }

        .laporan-table td {
            padding: 6px 5px;
            border: 1px solid #d9d9d9;
            font-size: 8px;
            vertical-align: middle;
        }

        .laporan-table tbody tr:nth-child(even) {
            background: #f8faf9;
        }


        /* ==============================
           LEBAR KOLOM
        ============================== */

        .col-no {
            width: 4%;
        }

        .col-siswa {
            width: 14%;
        }

        .col-nis {
            width: 9%;
        }

        .col-kelas {
            width: 11%;
        }

        .col-tahun {
            width: 12%;
        }

        .col-kategori {
            width: 11%;
        }

        .col-nominal {
            width: 12%;
        }

        .col-status {
            width: 10%;
        }


        /* ==============================
           TEXT
        ============================== */

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }


        /* ==============================
           STATUS
        ============================== */

        .lunas {
            color: #198754;
            font-weight: bold;
        }

        .belum {
            color: #dc3545;
            font-weight: bold;
        }


        /* ==============================
           SUMMARY
        ============================== */

        .summary {
            margin-top: 20px;
            margin-left: auto;
            width: 45%;
            border-collapse: collapse;
        }

        .summary td {
            border: none;
            padding: 5px;
            font-size: 9px;
        }

        .summary tr:last-child td {
            border-top: 2px solid #0f5132;
            color: #0f5132;
            font-weight: bold;
        }


        /* ==============================
           FOOTER
        ============================== */

        .pdf-footer {
            margin-top: 25px;
            text-align: right;
            font-size: 8px;
            color: #777;
        }

    </style>

</head>


<body>


    {{-- ==============================
         HEADER
    ============================== --}}

    <div class="pdf-header">

        <h2>
            SMP Plus Al-I'tam
        </h2>

        <p>
            Laporan Pembayaran Siswa
        </p>

    </div>


    {{-- ==============================
         TABEL PEMBAYARAN
    ============================== --}}

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

                    $nominalTagihan = $tagihan?->nominal ?? 0;

                    $dibayar = $item->nominal ?? 0;

                    $sisa = max(
                        0,
                        $nominalTagihan - $dibayar
                    );

                @endphp


                <tr>

                    {{-- NO --}}
                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>


                    {{-- SISWA --}}
                    <td>
                        {{ $tagihan?->siswa?->nama ?? '-' }}
                    </td>


                    {{-- NIS --}}
                    <td class="text-center">
                        {{ $tagihan?->siswa?->nis ?? '-' }}
                    </td>


                    {{-- KELAS --}}
                    <td class="text-center">
                        {{ $tagihan?->siswa?->kelas?->nama_kelas ?? '-' }}
                    </td>


                    {{-- TAHUN AJARAN --}}
                    <td class="text-center">
                        {{ $tagihan?->tahunAjaran?->nama ?? '-' }}
                    </td>


                    {{-- KATEGORI --}}
                    <td>
                        {{ $tagihan?->kategori?->nama ?? '-' }}
                    </td>


                    {{-- TAGIHAN --}}
                    <td class="text-right">
                        Rp {{ number_format(
                            $nominalTagihan,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>


                    {{-- DIBAYAR --}}
                    <td class="text-right">
                        Rp {{ number_format(
                            $dibayar,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>


                    {{-- SISA --}}
                    <td class="text-right">
                        Rp {{ number_format(
                            $sisa,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>


                    {{-- STATUS --}}
                    <td class="text-center">

                        @if($sisa <= 0)

                            <span class="lunas">
                                Lunas
                            </span>

                        @else

                            <span class="belum">
                                Belum Lunas
                            </span>

                        @endif

                    </td>

                </tr>


            @empty

                <tr>

                    <td
                        colspan="10"
                        class="text-center"
                    >
                        Belum ada data laporan.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- ==============================
         RINGKASAN
    ============================== --}}

    <table class="summary">

        <tr>

            <td>
                <strong>
                    Total Tagihan
                </strong>
            </td>

            <td class="text-right">

                Rp {{ number_format(
                    $totalTagihan ?? 0,
                    0,
                    ',',
                    '.'
                ) }}

            </td>

        </tr>


        <tr>

            <td>
                <strong>
                    Total Pembayaran
                </strong>
            </td>

            <td class="text-right">

                Rp {{ number_format(
                    $totalPembayaran ?? 0,
                    0,
                    ',',
                    '.'
                ) }}

            </td>

        </tr>


        <tr>

            <td>
                <strong>
                    Sisa Tagihan
                </strong>
            </td>

            <td class="text-right">

                Rp {{ number_format(
                    $sisaTagihan ?? 0,
                    0,
                    ',',
                    '.'
                ) }}

            </td>

        </tr>

    </table>


    {{-- ==============================
         FOOTER
    ============================== --}}

    <div class="pdf-footer">

        Dicetak pada:
        {{ now()->format('d/m/Y H:i') }}

    </div>


</body>

</html>