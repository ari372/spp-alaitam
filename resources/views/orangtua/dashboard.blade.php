@extends('layouts.orangtua')

@section('title', 'Dashboard Orang Tua')

@section('content')

@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

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
                                        class="payment-form"
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