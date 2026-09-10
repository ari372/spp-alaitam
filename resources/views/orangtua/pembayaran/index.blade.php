<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tagihan Pembayaran</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7f6;
            color: #333;
        }

        .navbar {
            background: #0f5132;
            color: white;
            padding: 18px 30px;
        }

        .navbar strong {
            font-size: 18px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        h2 {
            color: #0f5132;
            margin-bottom: 25px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .error {
            background: #f8d7da;
            color: #842029;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 25px;

            box-shadow:
                0 4px 15px rgba(0,0,0,.07);
        }

        .anak {
            font-size: 20px;
            font-weight: bold;
            color: #0f5132;
            margin-bottom: 20px;
        }

        .anak small {
            display: block;
            margin-top: 6px;
            color: #777;
            font-size: 14px;
            font-weight: normal;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        th {
            background: #0f5132;
            color: white;
            padding: 13px;
            text-align: left;
            white-space: nowrap;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .nominal {
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .belum {
            background: #fff3cd;
            color: #664d03;
        }

        .menunggu {
            background: #cff4fc;
            color: #055160;
        }

        .lunas {
            background: #d1e7dd;
            color: #0f5132;
        }

        .ditolak {
            background: #f8d7da;
            color: #842029;
        }

        .btn {
            display: inline-block;
            padding: 9px 15px;
            border-radius: 7px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-bayar {
            background: #198754;
            color: white;
        }

        .btn-bayar:hover {
            background: #157347;
        }

        .btn-disabled {
            background: #adb5bd;
            color: white;
            cursor: default;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        @media(max-width: 768px) {

            .container {
                margin: 20px auto;
            }

            .card {
                padding: 18px;
            }

            h2 {
                font-size: 24px;
            }

        }

    </style>

</head>

<body>

<div class="navbar">

    <strong>
        SMP Plus Al-I'tam
    </strong>

</div>


<div class="container">

    <h2>
        Tagihan Pembayaran
    </h2>


    {{-- ==============================
         PESAN SUCCESS
    =============================== --}}

    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ==============================
         PESAN ERROR
    =============================== --}}

    @if(session('error'))

        <div class="alert error">
            {{ session('error') }}
        </div>

    @endif


    {{-- ==============================
         DATA ANAK
    =============================== --}}

    @forelse($siswa as $anak)

        <div class="card">

            <div class="anak">

                {{ $anak->nama }}

                @if($anak->kelas)

                    <small>
                        Kelas:
                        {{ $anak->kelas->nama_kelas }}
                    </small>

                @endif

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                Tahun Ajaran
                            </th>

                            <th>
                                Kategori
                            </th>

                            <th>
                                Tagihan
                            </th>

                            <th>
                                Sudah Dibayar
                            </th>

                            <th>
                                Sisa
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @php
                        $tagihanAnak = $tagihan->where(
                            'siswa_id',
                            $anak->id
                        );
                    @endphp


                    @forelse($tagihanAnak as $index => $item)

                        @php

                            /*
                             * Total pembayaran yang SUDAH DISETUJUI
                             */

                            $dibayar = $item->pembayaran
                                ->where('status', 'dibayar')
                                ->sum('nominal');


                            /*
                             * Sisa tagihan
                             */

                            $sisa = max(
                                $item->nominal - $dibayar,
                                0
                            );


                            /*
                             * Cek apakah ada pembayaran
                             * yang masih menunggu
                             */

                            $menunggu = $item->pembayaran
                                ->where('status', 'menunggu')
                                ->count();


                            /*
                             * Cek apakah pembayaran terakhir
                             * ditolak
                             */

                            $ditolak = $item->pembayaran
                                ->where('status', 'ditolak')
                                ->count();


                            /*
                             * Tentukan status
                             */

                            if ($sisa <= 0) {

                                $status = 'lunas';

                            } elseif ($menunggu > 0) {

                                $status = 'menunggu';

                            } elseif ($ditolak > 0) {

                                $status = 'ditolak';

                            } else {

                                $status = 'belum';

                            }

                        @endphp


                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>
                                {{ $item->tahunAjaran->nama ?? '-' }}
                            </td>


                            <td>
                                {{ $item->kategori->nama ?? '-' }}
                            </td>


                            <td class="nominal">

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

                            <td>

                                @if($status === 'lunas')

                                    <span class="status lunas">
                                        Lunas
                                    </span>

                                @elseif($status === 'menunggu')

                                    <span class="status menunggu">
                                        Menunggu Persetujuan
                                    </span>

                                @elseif($status === 'ditolak')

                                    <span class="status ditolak">
                                        Ditolak
                                    </span>

                                @else

                                    <span class="status belum">
                                        Belum Bayar
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}

                            <td>

                                @if($status === 'lunas')

                                    <span class="status lunas">
                                        Selesai
                                    </span>


                                @elseif($status === 'menunggu')

                                    <span class="status menunggu">
                                        Diproses
                                    </span>


                                @else

                                    <a
                                        href="{{ route(
                                            'orangtua.pembayaran.create',
                                            $item->id
                                        ) }}"
                                        class="btn btn-bayar"
                                    >
                                        Bayar
                                    </a>

                                @endif

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="empty"
                            >
                                Belum ada tagihan untuk siswa ini.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    @empty

        <div class="card">

            <div class="empty">

                Belum ada data anak.

            </div>

        </div>

    @endforelse

</div>

</body>

</html>