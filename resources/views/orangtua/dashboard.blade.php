@extends('layouts.orangtua')

@section('title', 'Dashboard Orang Tua')

@section('content')
@if(session('error'))
    <div style="
        background:#f8d7da;
        color:#842029;
        padding:15px 20px;
        border-radius:10px;
        margin-bottom:20px;
        border:1px solid #f5c2c7;
        font-weight:600;
    ">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div style="
        background:#d1e7dd;
        color:#0f5132;
        padding:15px 20px;
        border-radius:10px;
        margin-bottom:20px;
        border:1px solid #badbcc;
        font-weight:600;
    ">
        {{ session('success') }}
    </div>
@endif

<style>
    .page-title {
        margin-bottom: 30px;
    }

    .page-title h1 {
        color: #0f5132;
        font-size: 36px;
        margin-bottom: 10px;
    }

    .page-title p {
        color: #777;
        font-size: 18px;
        margin-bottom: 0;
    }

    .anak-card,
    .summary-card,
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .06);
    }

    .anak-card {
        padding: 28px;
        margin-bottom: 25px;
    }

    .section-title {
        color: #0f5132;
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 22px;
    }

    .anak-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .anak-item {
        background: #f5f7f6;
        border-radius: 10px;
        padding: 18px;
    }

    .anak-item span {
        display: block;
        color: #777;
        font-size: 14px;
        margin-bottom: 7px;
    }

    .anak-item strong {
        color: #333;
        font-size: 17px;
    }

    .summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    .summary-card {
        padding: 25px;
    }

    .summary-card span {
        display: block;
        color: #777;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .summary-card strong {
        font-size: 25px;
        color: #0f5132;
    }

    .table-card {
        padding: 28px;
        margin-bottom: 25px;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    th {
        background: #0f5132;
        color: white;
        padding: 15px;
        text-align: left;
        white-space: nowrap;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    tr:last-child td {
        border-bottom: none;
    }

    .status {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        white-space: nowrap;
    }

    .status-lunas {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status-belum {
        background: #fff3cd;
        color: #856404;
    }

    .status-menunggu {
        background: #cff4fc;
        color: #055160;
    }

    .status-ditolak {
        background: #f8d7da;
        color: #842029;
    }

    .btn {
        display: inline-block;
        padding: 9px 15px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: bold;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        transition: .2s;
    }

    .btn-bayar {
        background: #198754;
        color: white;
    }

    .btn-bayar:hover {
        background: #157347;
        color: white;
    }

    .empty {
        text-align: center;
        padding: 30px;
        color: #777;
    }

    @media (max-width: 768px) {
        .anak-info,
        .summary {
            grid-template-columns: 1fr;
        }

        .page-title h1 {
            font-size: 30px;
        }

        .page-title p {
            font-size: 15px;
        }

        .table-card,
        .anak-card {
            padding: 20px;
        }
    }
</style>


<div class="page-title">

    <h1>
        Dashboard Orang Tua
    </h1>

    <p>
        Selamat datang di Sistem Pembayaran SPP.
    </p>

</div>


@if (!$siswa)

    <div class="table-card">

        <div class="empty">
            Data siswa belum ditemukan.
        </div>

    </div>

@else


    {{-- DATA ANAK --}}

    <div class="anak-card">

        <h2 class="section-title">
            Data Anak
        </h2>

        <div class="anak-info">

            <div class="anak-item">

                <span>
                    Nama Siswa
                </span>

                <strong>
                    {{ $siswa->nama }}
                </strong>

            </div>

            <div class="anak-item">

                <span>
                    NIS
                </span>

                <strong>
                    {{ $siswa->nis }}
                </strong>

            </div>

            <div class="anak-item">

                <span>
                    Kelas
                </span>

                <strong>
                    {{ $siswa->kelas->nama_kelas ?? '-' }}
                </strong>

            </div>

        </div>

    </div>


    {{-- RINGKASAN --}}

    <div class="summary">

        <div class="summary-card">

            <span>
                Total Tagihan
            </span>

            <strong>
                Rp {{ number_format(
                    $totalTagihan,
                    0,
                    ',',
                    '.'
                ) }}
            </strong>

        </div>


        <div class="summary-card">

            <span>
                Sudah Dibayar
            </span>

            <strong>
                Rp {{ number_format(
                    $totalDibayar,
                    0,
                    ',',
                    '.'
                ) }}
            </strong>

        </div>


        <div class="summary-card">

            <span>
                Sisa Tagihan
            </span>

            <strong>
                Rp {{ number_format(
                    $sisaTagihan,
                    0,
                    ',',
                    '.'
                ) }}
            </strong>

        </div>

    </div>


    {{-- DAFTAR TAGIHAN --}}

    <div class="table-card">

        <h2 class="section-title">
            Daftar Tagihan
        </h2>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Tahun Ajaran
                        </th>

                        <th>
                            Nominal
                        </th>

                        <th>
                            Jatuh Tempo
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

                    @forelse ($tagihan as $index => $item)

                        @php

                            $sudahDibayar = \App\Models\PembayaranTagihan::where(
                                'tagihan_id',
                                $item->id
                            )
                            ->where('status', 'dibayar')
                            ->sum('nominal');

                            $sisa = max(
                                $item->nominal - $sudahDibayar,
                                0
                            );

                            $sedangDiproses = \App\Models\PembayaranTagihan::where(
                                'tagihan_id',
                                $item->id
                            )
                            ->where('status', 'menunggu')
                            ->exists();

                            $ditolak = \App\Models\PembayaranTagihan::where(
                                'tagihan_id',
                                $item->id
                            )
                            ->where('status', 'ditolak')
                            ->exists();

                        @endphp

                        <tr>

                            {{-- NO --}}

                            <td>
                                {{ $index + 1 }}
                            </td>


                            {{-- KATEGORI --}}

                            <td>
                                {{ $item->kategori->nama ?? '-' }}
                            </td>


                            {{-- TAHUN AJARAN --}}

                            <td>
                                {{ $item->tahunAjaran->nama ?? '-' }}
                            </td>


                            {{-- NOMINAL --}}

                            <td>

                                <strong>
                                    Rp {{ number_format(
                                        $item->nominal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>

                            </td>


                            {{-- JATUH TEMPO --}}

                            <td>

                                @if ($item->jatuh_tempo)

                                    {{ \Carbon\Carbon::parse(
                                        $item->jatuh_tempo
                                    )->format('d-m-Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- STATUS --}}

                            <td>

                                @if ($sisa <= 0)

                                    <span class="status status-lunas">
                                        Lunas
                                    </span>

                                @elseif ($sedangDiproses)

                                    <span class="status status-menunggu">
                                        Menunggu Persetujuan
                                    </span>

                                @elseif ($ditolak)

                                    <span class="status status-ditolak">
                                        Ditolak
                                    </span>

                                @else

                                    <span class="status status-belum">
                                        Belum Bayar
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}

<td>

    @if($sisa <= 0)

        <span class="status status-lunas">
            Selesai
        </span>

    @elseif($sedangDiproses)

        <span class="status status-menunggu">
            Diproses
        </span>

@else

    <form
        action="{{ route('orangtua.pembayaran.create', ['tagihan' => $item->id]) }}"
        method="GET"
        style="display:inline;"
    >
        <button
            type="submit"
            class="btn btn-bayar"
        >
            Bayar
        </button>
    </form>

@endif
</td>
                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="empty"
                            >
                                Belum ada tagihan.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- RIWAYAT PEMBAYARAN --}}

    <div class="table-card">

        <h2 class="section-title">
            Riwayat Pembayaran
        </h2>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Nominal
                        </th>

                        <th>
                            Metode
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($pembayaran as $index => $item)

                        <tr>

                            {{-- NO --}}

                            <td>
                                {{ $index + 1 }}
                            </td>


                            {{-- TANGGAL --}}

                            <td>

                                @if ($item->tanggal_kirim)

                                    {{ \Carbon\Carbon::parse(
                                        $item->tanggal_kirim
                                    )->format('d-m-Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- KATEGORI --}}

                            <td>

                                {{ $item->tagihan->kategori->nama ?? '-' }}

                            </td>


                            {{-- NOMINAL --}}

                            <td>

                                <strong>
                                    Rp {{ number_format(
                                        $item->nominal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>

                            </td>


                            {{-- METODE --}}

                            <td>

                                @if ($item->metode === 'transfer')

                                    Transfer Bank

                                @elseif ($item->metode === 'qris')

                                    QRIS

                                @else

                                    {{ $item->metode ?? '-' }}

                                @endif

                            </td>


                            {{-- STATUS --}}

                            <td>

                                @if ($item->status === 'dibayar')

                                    <span class="status status-lunas">
                                        Disetujui
                                    </span>

                                @elseif ($item->status === 'menunggu')

                                    <span class="status status-menunggu">
                                        Menunggu
                                    </span>

                                @elseif ($item->status === 'ditolak')

                                    <span class="status status-ditolak">
                                        Ditolak
                                    </span>

                                @else

                                    <span class="status status-belum">
                                        {{ ucfirst($item->status) }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="empty"
                            >
                                Belum ada riwayat pembayaran.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endif

@endsection