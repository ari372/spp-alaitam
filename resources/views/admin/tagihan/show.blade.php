@extends('layouts.admin')

@section('title', 'Detail Tagihan')

@section('page-title', 'Detail Tagihan')

@section('content')

<div style="max-width:900px;">

    {{-- HEADER --}}
    <div style="
        margin-bottom:25px;
    ">

        <h2 style="
            color:#0f5132;
            margin-bottom:5px;
        ">
            Detail Tagihan
        </h2>

        <p style="
            color:#777;
            margin:0;
        ">
            Informasi lengkap tagihan siswa
        </p>

    </div>


    {{-- DETAIL TAGIHAN --}}
    <div style="
        background:white;
        padding:30px;
        border-radius:15px;
        box-shadow:0 4px 15px rgba(0,0,0,.08);
        margin-bottom:25px;
    ">

        <h3 style="
            color:#0f5132;
            margin-top:0;
            margin-bottom:25px;
        ">
            Informasi Tagihan
        </h3>


        {{-- SISWA --}}
        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:14px 0;
            border-bottom:1px solid #eee;
        ">

            <strong>
                Siswa
            </strong>

            <span>
                {{ $tagihan->siswa->nama ?? '-' }}
            </span>

        </div>


        {{-- KELAS --}}
        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:14px 0;
            border-bottom:1px solid #eee;
        ">

            <strong>
                Kelas
            </strong>

            <span>
                {{ $tagihan->siswa->kelas->nama ?? '-' }}
            </span>

        </div>


        {{-- TAHUN AJARAN --}}
        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:14px 0;
            border-bottom:1px solid #eee;
        ">

            <strong>
                Tahun Ajaran
            </strong>

            <span>
                {{ $tagihan->tahunAjaran->nama ?? '-' }}
            </span>

        </div>


        {{-- KATEGORI --}}
        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:14px 0;
            border-bottom:1px solid #eee;
        ">

            <strong>
                Kategori
            </strong>

            <span>
                {{ $tagihan->kategori->nama ?? '-' }}
            </span>

        </div>


        {{-- NOMINAL --}}
        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:14px 0;
            border-bottom:1px solid #eee;
        ">

            <strong>
                Nominal Tagihan
            </strong>

            <span style="
                font-weight:bold;
                color:#0f5132;
                font-size:18px;
            ">

                Rp
                {{ number_format(
                    $tagihan->nominal,
                    0,
                    ',',
                    '.'
                ) }}

            </span>

        </div>


        {{-- JATUH TEMPO --}}
        <div style="
            display:grid;
            grid-template-columns:200px 1fr;
            padding:14px 0;
        ">

            <strong>
                Jatuh Tempo
            </strong>

            <span>

                @if($tagihan->jatuh_tempo)

                    {{ $tagihan->jatuh_tempo->format('d-m-Y') }}

                @else

                    -

                @endif

            </span>

        </div>

    </div>


    {{-- RIWAYAT PEMBAYARAN --}}
    <div style="
        background:white;
        padding:30px;
        border-radius:15px;
        box-shadow:0 4px 15px rgba(0,0,0,.08);
        margin-bottom:25px;
    ">

        <h3 style="
            color:#0f5132;
            margin-top:0;
            margin-bottom:20px;
        ">
            Riwayat Pembayaran
        </h3>


        @if($tagihan->pembayaran->count() > 0)

            <div style="overflow-x:auto;">

                <table style="
                    width:100%;
                    border-collapse:collapse;
                ">

                    <thead>

                        <tr style="
                            background:#0f5132;
                            color:white;
                        ">

                            <th style="padding:13px;">
                                No
                            </th>

                            <th style="padding:13px;">
                                Tanggal Bayar
                            </th>

                            <th style="padding:13px;">
                                Nominal
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

                                <td style="
                                    padding:13px;
                                    text-align:center;
                                ">
                                    {{ $index + 1 }}
                                </td>


                                <td style="padding:13px;">

                                    @if($pembayaran->tanggal_bayar)

                                        {{ $pembayaran->tanggal_bayar->format('d-m-Y') }}

                                    @else

                                        -

                                    @endif

                                </td>


                                <td style="
                                    padding:13px;
                                    font-weight:bold;
                                ">

                                    Rp
                                    {{ number_format(
                                        $pembayaran->nominal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div style="
                background:#f8f9fa;
                padding:20px;
                border-radius:8px;
                color:#777;
                text-align:center;
            ">

                Belum ada pembayaran untuk tagihan ini.

            </div>

        @endif

    </div>


    {{-- AKSI --}}
    <div style="
        display:flex;
        gap:10px;
    ">

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
            "
        >
            Edit
        </a>


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
            "
        >
            Kembali
        </a>

    </div>

</div>

@endsection