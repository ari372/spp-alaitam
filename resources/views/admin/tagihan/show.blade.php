@extends('layouts.admin')

@section('title', 'Detail Tagihan')

@section('page-title', 'Detail Tagihan')

@section('content')

<div style="
    max-width:1100px;
    margin:0 auto;
">

    {{-- ===================================================== --}}
    {{-- HEADER --}}
    {{-- ===================================================== --}}

    <div style="
        margin-bottom:25px;
    ">

        <h2 style="
            color:#0f5132;
            margin:0 0 5px 0;
            font-size:28px;
        ">
            Detail Tagihan
        </h2>

        <p style="
            color:#777;
            margin:0;
            font-size:14px;
        ">
            Informasi lengkap tagihan dan riwayat pembayaran siswa
        </p>

    </div>


    {{-- ===================================================== --}}
    {{-- INFORMASI TAGIHAN --}}
    {{-- ===================================================== --}}

    <div style="
        background:white;
        padding:30px;
        border-radius:15px;
        box-shadow:0 4px 15px rgba(0,0,0,.08);
        margin-bottom:25px;
    ">

        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:25px;
            gap:15px;
            flex-wrap:wrap;
        ">

            <h3 style="
                color:#0f5132;
                margin:0;
                font-size:20px;
            ">
                Informasi Tagihan
            </h3>

            {{-- STATUS TAGIHAN --}}
            @php

                $totalDibayar = $tagihan->pembayaran
                    ->where('status', 'dibayar')
                    ->sum('nominal');

                $sisaTagihan = max(
                    0,
                    $tagihan->nominal - $totalDibayar
                );

            @endphp


            @if($sisaTagihan <= 0)

                <span style="
                    display:inline-block;
                    background:#d1e7dd;
                    color:#0f5132;
                    padding:7px 14px;
                    border-radius:20px;
                    font-size:13px;
                    font-weight:bold;
                ">
                    Lunas
                </span>

            @else

                <span style="
                    display:inline-block;
                    background:#fff3cd;
                    color:#856404;
                    padding:7px 14px;
                    border-radius:20px;
                    font-size:13px;
                    font-weight:bold;
                ">
                    Belum Lunas
                </span>

            @endif

        </div>


        {{-- ================================================= --}}
        {{-- SISWA --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            border-bottom:1px solid #eee;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                Siswa
            </strong>

            <span style="
                color:#222;
                font-weight:500;
            ">
                {{ $tagihan->siswa?->nama ?? '-' }}
            </span>

        </div>


        {{-- ================================================= --}}
        {{-- NIS --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            border-bottom:1px solid #eee;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                NIS
            </strong>

            <span style="
                color:#222;
                font-weight:500;
            ">
                {{ $tagihan->siswa?->nis ?? '-' }}
            </span>

        </div>


        {{-- ================================================= --}}
        {{-- KELAS --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            border-bottom:1px solid #eee;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                Kelas
            </strong>

            <span style="
                color:#222;
                font-weight:500;
            ">
                {{ $tagihan->siswa?->kelas?->nama_kelas ?? '-' }}
            </span>

        </div>


        {{-- ================================================= --}}
        {{-- TAHUN AJARAN --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            border-bottom:1px solid #eee;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                Tahun Ajaran
            </strong>

            <span style="
                color:#222;
                font-weight:500;
            ">
                {{ $tagihan->tahunAjaran?->nama ?? '-' }}
            </span>

        </div>


        {{-- ================================================= --}}
        {{-- KATEGORI --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            border-bottom:1px solid #eee;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                Kategori
            </strong>

            <span style="
                color:#222;
                font-weight:500;
            ">
                {{ $tagihan->kategori?->nama ?? '-' }}
            </span>

        </div>


        {{-- ================================================= --}}
        {{-- NOMINAL TAGIHAN --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            border-bottom:1px solid #eee;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                Nominal Tagihan
            </strong>

            <span style="
                font-weight:bold;
                color:#0f5132;
                font-size:20px;
            ">

                Rp {{ number_format(
                    $tagihan->nominal ?? 0,
                    0,
                    ',',
                    '.'
                ) }}

            </span>

        </div>


        {{-- ================================================= --}}
        {{-- TOTAL DIBAYAR --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            border-bottom:1px solid #eee;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                Total Dibayar
            </strong>

            <span style="
                font-weight:bold;
                color:#198754;
                font-size:18px;
            ">

                Rp {{ number_format(
                    $totalDibayar,
                    0,
                    ',',
                    '.'
                ) }}

            </span>

        </div>


        {{-- ================================================= --}}
        {{-- SISA TAGIHAN --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            border-bottom:1px solid #eee;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                Sisa Tagihan
            </strong>

            <span style="
                font-weight:bold;
                color:{{ $sisaTagihan > 0 ? '#dc3545' : '#198754' }};
                font-size:18px;
            ">

                Rp {{ number_format(
                    $sisaTagihan,
                    0,
                    ',',
                    '.'
                ) }}

            </span>

        </div>


        {{-- ================================================= --}}
        {{-- JATUH TEMPO --}}
        {{-- ================================================= --}}

        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:15px 0;
            gap:20px;
        ">

            <strong style="
                color:#555;
            ">
                Jatuh Tempo
            </strong>

            <span style="
                color:#222;
                font-weight:500;
            ">

                @if($tagihan->jatuh_tempo)

                    {{ $tagihan->jatuh_tempo->format('d-m-Y') }}

                @else

                    -

                @endif

            </span>

        </div>

    </div>



    {{-- ===================================================== --}}
    {{-- RIWAYAT PEMBAYARAN --}}
    {{-- ===================================================== --}}

    <div style="
        background:white;
        padding:30px;
        border-radius:15px;
        box-shadow:0 4px 15px rgba(0,0,0,.08);
        margin-bottom:25px;
    ">

        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
            gap:15px;
            flex-wrap:wrap;
        ">

            <div>

                <h3 style="
                    color:#0f5132;
                    margin:0 0 5px 0;
                    font-size:20px;
                ">
                    Riwayat Pembayaran
                </h3>

                <p style="
                    color:#777;
                    margin:0;
                    font-size:13px;
                ">
                    Daftar pembayaran untuk tagihan ini
                </p>

            </div>


            {{-- JUMLAH TRANSAKSI --}}

            <span style="
                background:#e8f5ee;
                color:#0f5132;
                padding:7px 13px;
                border-radius:20px;
                font-size:13px;
                font-weight:bold;
            ">

                {{ $tagihan->pembayaran->count() }}
                Transaksi

            </span>

        </div>


        {{-- ================================================= --}}
        {{-- ADA PEMBAYARAN --}}
        {{-- ================================================= --}}

        @if($tagihan->pembayaran->count() > 0)

            <div style="
                overflow-x:auto;
            ">

                <table style="
                    width:100%;
                    border-collapse:collapse;
                    min-width:700px;
                ">

                    <thead>

                        <tr style="
                            background:#0f5132;
                            color:white;
                        ">

                            <th style="
                                padding:13px;
                                text-align:center;
                                white-space:nowrap;
                            ">
                                No
                            </th>

                            <th style="
                                padding:13px;
                                text-align:left;
                                white-space:nowrap;
                            ">
                                Tanggal Bayar
                            </th>

                            <th style="
                                padding:13px;
                                text-align:right;
                                white-space:nowrap;
                            ">
                                Nominal
                            </th>

                            <th style="
                                padding:13px;
                                text-align:center;
                                white-space:nowrap;
                            ">
                                Metode
                            </th>

                            <th style="
                                padding:13px;
                                text-align:center;
                                white-space:nowrap;
                            ">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach(
                            $tagihan->pembayaran
                            as $index => $pembayaran
                        )

                            <tr style="
                                border-bottom:1px solid #eee;
                            ">

                                {{-- NO --}}

                                <td style="
                                    padding:13px;
                                    text-align:center;
                                ">
                                    {{ $index + 1 }}
                                </td>


                                {{-- TANGGAL BAYAR --}}

                                <td style="
                                    padding:13px;
                                ">

                                    @if($pembayaran->tanggal_kirim)

                                        <div style="
                                            font-weight:600;
                                            color:#333;
                                        ">
                                            {{ $pembayaran->tanggal_kirim->format('d-m-Y') }}
                                        </div>

                                        <div style="
                                            font-size:12px;
                                            color:#888;
                                            margin-top:3px;
                                        ">
                                            {{ $pembayaran->tanggal_kirim->format('H:i') }}
                                        </div>

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- NOMINAL --}}

                                <td style="
                                    padding:13px;
                                    text-align:right;
                                    font-weight:bold;
                                    color:#0f5132;
                                    white-space:nowrap;
                                ">

                                    Rp {{ number_format(
                                        $pembayaran->nominal ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </td>


                                {{-- METODE --}}

                                <td style="
                                    padding:13px;
                                    text-align:center;
                                ">

                                    @if($pembayaran->metode)

                                        {{ ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $pembayaran->metode
                                            )
                                        ) }}

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- STATUS --}}

                                <td style="
                                    padding:13px;
                                    text-align:center;
                                ">

                                    @if($pembayaran->status === 'dibayar')

                                        <span style="
                                            display:inline-block;
                                            background:#d1e7dd;
                                            color:#0f5132;
                                            padding:6px 12px;
                                            border-radius:20px;
                                            font-size:12px;
                                            font-weight:bold;
                                        ">
                                            Dibayar
                                        </span>

                                    @elseif($pembayaran->status === 'menunggu')

                                        <span style="
                                            display:inline-block;
                                            background:#fff3cd;
                                            color:#856404;
                                            padding:6px 12px;
                                            border-radius:20px;
                                            font-size:12px;
                                            font-weight:bold;
                                        ">
                                            Menunggu
                                        </span>

                                    @elseif($pembayaran->status === 'ditolak')

                                        <span style="
                                            display:inline-block;
                                            background:#f8d7da;
                                            color:#842029;
                                            padding:6px 12px;
                                            border-radius:20px;
                                            font-size:12px;
                                            font-weight:bold;
                                        ">
                                            Ditolak
                                        </span>

                                    @else

                                        <span style="
                                            color:#777;
                                        ">
                                            {{ $pembayaran->status ?? '-' }}
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>


                    {{-- ================================================= --}}
                    {{-- TOTAL --}}
                    {{-- ================================================= --}}

                    <tfoot>

                        <tr style="
                            background:#f8f9fa;
                            border-top:2px solid #0f5132;
                        ">

                            <td
                                colspan="2"
                                style="
                                    padding:15px;
                                    text-align:right;
                                    font-weight:bold;
                                    color:#555;
                                "
                            >
                                Total Dibayar
                            </td>

                            <td style="
                                padding:15px;
                                text-align:right;
                                font-weight:bold;
                                color:#0f5132;
                                white-space:nowrap;
                            ">

                                Rp {{ number_format(
                                    $totalDibayar,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td
                                colspan="2"
                                style="
                                    padding:15px;
                                "
                            >
                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        @else

            {{-- ================================================= --}}
            {{-- BELUM ADA PEMBAYARAN --}}
            {{-- ================================================= --}}

            <div style="
                background:#f8f9fa;
                padding:25px;
                border-radius:10px;
                color:#777;
                text-align:center;
                border:1px dashed #ddd;
            ">

                <div style="
                    font-size:30px;
                    margin-bottom:8px;
                ">
                    —
                </div>

                <strong style="
                    display:block;
                    color:#555;
                    margin-bottom:5px;
                ">
                    Belum Ada Pembayaran
                </strong>

                <span style="
                    font-size:13px;
                ">
                    Belum ada transaksi pembayaran untuk tagihan ini.
                </span>

            </div>

        @endif

    </div>



    {{-- ===================================================== --}}
    {{-- AKSI --}}
    {{-- ===================================================== --}}

    <div style="
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        margin-bottom:20px;
    ">

        {{-- EDIT --}}

        <a
            href="{{ route(
                'admin.tagihan.edit',
                $tagihan->id
            ) }}"
            style="
                background:#ffc107;
                color:#212529;
                text-decoration:none;
                padding:11px 20px;
                border-radius:7px;
                font-weight:bold;
                display:inline-block;
            "
        >
            Edit
        </a>


        {{-- KEMBALI --}}

        <a
            href="{{ route(
                'admin.tagihan.index'
            ) }}"
            style="
                background:#6c757d;
                color:white;
                text-decoration:none;
                padding:11px 20px;
                border-radius:7px;
                font-weight:bold;
                display:inline-block;
            "
        >
            Kembali
        </a>

    </div>

</div>

@endsection