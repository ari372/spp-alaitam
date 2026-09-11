<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Laporan Pembayaran</title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
            color: #0f5132;
        }

        p {
            text-align: center;
            margin-top: 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #0f5132;
            color: white;
            padding: 7px;
            border: 1px solid #ddd;
            text-align: center;
        }

        td {
            padding: 7px;
            border: 1px solid #ddd;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .lunas {
            color: #198754;
            font-weight: bold;
        }

        .belum {
            color: #dc3545;
            font-weight: bold;
        }

        .summary {
            margin-top: 20px;
            width: 50%;
            margin-left: auto;
        }

        .summary td {
            border: none;
            padding: 5px;
        }

    </style>

</head>

<body>

    <h2>
        SMP Plus Al-I'tam
    </h2>

    <p>
        Laporan Pembayaran Siswa
    </p>


    <table>

        <thead>

            <tr>

                <th>No</th>

                <th>Siswa</th>

                <th>NIS</th>

                <th>Kelas</th>

                <th>Tahun Ajaran</th>

                <th>Kategori</th>

                <th>Tagihan</th>

                <th>Dibayar</th>

                <th>Sisa</th>

                <th>Status</th>

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

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>


                    <td>
                        {{ $tagihan?->siswa?->nama ?? '-' }}
                    </td>


                    <td>
                        {{ $tagihan?->siswa?->nis ?? '-' }}
                    </td>


                    <td>
                        {{ $tagihan?->siswa?->kelas?->nama_kelas ?? '-' }}
                    </td>


                    <td>
                        {{ $tagihan?->tahunAjaran?->nama ?? '-' }}
                    </td>


                    <td>
                        {{ $tagihan?->kategori?->nama ?? '-' }}
                    </td>


                    <td class="text-right">

                        Rp
                        {{ number_format(
                            $nominalTagihan,
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>


                    <td class="text-right">

                        Rp
                        {{ number_format(
                            $dibayar,
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>


                    <td class="text-right">

                        Rp
                        {{ number_format(
                            $sisa,
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>


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


    <table class="summary">

        <tr>

            <td>
                <strong>
                    Total Tagihan
                </strong>
            </td>

            <td class="text-right">

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

            <td>
                <strong>
                    Total Pembayaran
                </strong>
            </td>

            <td class="text-right">

                Rp
                {{ number_format(
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

</body>

</html>