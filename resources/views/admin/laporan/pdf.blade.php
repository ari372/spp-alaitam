<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Laporan Pembayaran
    </title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
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
        }

        td {
            padding: 7px;
            border: 1px solid #ddd;
        }

        .summary {
            margin-top: 20px;
            width: 100%;
        }

        .summary td {
            border: none;
            padding: 5px;
        }

        .lunas {
            color: #198754;
            font-weight: bold;
        }

        .belum {
            color: #dc3545;
            font-weight: bold;
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

            @forelse($tagihan as $item)

                @php

                    $dibayar = $item->pembayaran
                        ->where('status', 'disetujui')
                        ->sum('nominal');

                    $sisa = max(
                        0,
                        $item->nominal - $dibayar
                    );

                @endphp

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->siswa->nama ?? '-' }}
                    </td>

                    <td>
                        {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                    </td>

                    <td>
                        {{ $item->tahunAjaran->nama ?? '-' }}
                    </td>

                    <td>
                        {{ $item->kategori->nama ?? '-' }}
                    </td>

                    <td>
                        Rp
                        {{ number_format(
                            $item->nominal,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>

                    <td>
                        Rp
                        {{ number_format(
                            $dibayar,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>

                    <td>
                        Rp
                        {{ number_format(
                            $sisa,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>

                    <td>

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
                        colspan="9"
                        style="text-align:center;"
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

            <td>
                Rp
                {{ number_format(
                    $totalTagihan,
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

            <td>
                Rp
                {{ number_format(
                    $totalPembayaran,
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

            <td>
                Rp
                {{ number_format(
                    $sisaTagihan,
                    0,
                    ',',
                    '.'
                ) }}
            </td>

        </tr>

    </table>

</body>

</html>