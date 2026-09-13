@extends('layouts.admin')

@section('title', 'Detail Tagihan')

@section('page-title', 'Detail Tagihan')

@push('styles')
    @vite('resources/css/admin/tagihan.css')

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
@endpush

@section('content')

<div class="tagihan-detail-container">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="tagihan-detail-header">

        <div>
            <h2>
                Detail Tagihan
            </h2>

            <p>
                Informasi lengkap tagihan dan riwayat pembayaran siswa
            </p>
        </div>

    </div>


    {{-- =====================================================
         HITUNG PEMBAYARAN
    ====================================================== --}}

    @php

        $totalDibayar = $tagihan->pembayaran
            ->where('status', 'dibayar')
            ->sum('nominal');

        $sisaTagihan = max(
            0,
            ($tagihan->nominal ?? 0) - $totalDibayar
        );

    @endphp


    {{-- =====================================================
         INFORMASI TAGIHAN
    ====================================================== --}}

    <div class="tagihan-detail-card">

        <div class="tagihan-detail-card-header">

            <div class="tagihan-detail-heading">

                <div class="tagihan-detail-heading-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <div>
                    <h3>
                        Informasi Tagihan
                    </h3>

                    <p>
                        Rincian data tagihan siswa
                    </p>
                </div>

            </div>


            {{-- STATUS TAGIHAN --}}

            @if($sisaTagihan <= 0)

                <span class="tagihan-status tagihan-status-lunas">
                    <i class="bi bi-check-circle"></i>
                    Lunas
                </span>

            @else

                <span class="tagihan-status tagihan-status-belum">
                    <i class="bi bi-clock-history"></i>
                    Belum Lunas
                </span>

            @endif

        </div>


        {{-- SISWA --}}

        <div class="tagihan-detail-row">

            <strong>
                Siswa
            </strong>

            <span>
                {{ $tagihan->siswa?->nama ?? '-' }}
            </span>

        </div>


        {{-- NIS --}}

        <div class="tagihan-detail-row">

            <strong>
                NIS
            </strong>

            <span>
                {{ $tagihan->siswa?->nis ?? '-' }}
            </span>

        </div>


        {{-- KELAS --}}

        <div class="tagihan-detail-row">

            <strong>
                Kelas
            </strong>

            <span>
                {{ $tagihan->siswa?->kelas?->nama_kelas ?? '-' }}
            </span>

        </div>


        {{-- TAHUN AJARAN --}}

        <div class="tagihan-detail-row">

            <strong>
                Tahun Ajaran
            </strong>

            <span>
                {{ $tagihan->tahunAjaran?->nama ?? '-' }}
            </span>

        </div>


        {{-- KATEGORI --}}

        <div class="tagihan-detail-row">

            <strong>
                Kategori
            </strong>

            <span>
                {{ $tagihan->kategori?->nama ?? '-' }}
            </span>

        </div>


        {{-- NOMINAL TAGIHAN --}}

        <div class="tagihan-detail-row">

            <strong>
                Nominal Tagihan
            </strong>

            <span class="tagihan-nominal">

                Rp {{ number_format(
                    $tagihan->nominal ?? 0,
                    0,
                    ',',
                    '.'
                ) }}

            </span>

        </div>


        {{-- TOTAL DIBAYAR --}}

        <div class="tagihan-detail-row">

            <strong>
                Total Dibayar
            </strong>

            <span class="tagihan-total-dibayar">

                Rp {{ number_format(
                    $totalDibayar,
                    0,
                    ',',
                    '.'
                ) }}

            </span>

        </div>


        {{-- SISA TAGIHAN --}}

        <div class="tagihan-detail-row">

            <strong>
                Sisa Tagihan
            </strong>

            <span class="tagihan-sisa
                {{ $sisaTagihan > 0
                    ? 'tagihan-sisa-belum'
                    : 'tagihan-sisa-lunas'
                }}">

                Rp {{ number_format(
                    $sisaTagihan,
                    0,
                    ',',
                    '.'
                ) }}

            </span>

        </div>


        {{-- JATUH TEMPO --}}

        <div class="tagihan-detail-row">

            <strong>
                Jatuh Tempo
            </strong>

            <span>

                @if($tagihan->jatuh_tempo)

                    {{ \Carbon\Carbon::parse($tagihan->jatuh_tempo)->format('d-m-Y') }}

                @else

                    -

                @endif

            </span>

        </div>

    </div>


    {{-- =====================================================
         RIWAYAT PEMBAYARAN
    ====================================================== --}}

    <div class="tagihan-detail-card">

        <div class="tagihan-history-header">

            <div class="tagihan-history-title">

                <div class="tagihan-history-heading">

                    <div class="tagihan-detail-heading-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div>
                        <h3>
                            Riwayat Pembayaran
                        </h3>

                        <p>
                            Daftar pembayaran untuk tagihan ini
                        </p>
                    </div>

                </div>

            </div>


            <span class="tagihan-transaksi-count">

                <i class="bi bi-wallet2"></i>

                {{ $tagihan->pembayaran->count() }}
                Transaksi

            </span>

        </div>


        {{-- ADA PEMBAYARAN --}}

        @if($tagihan->pembayaran->count() > 0)

            <div class="tagihan-table-wrapper">

                <table class="tagihan-detail-table">

                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                Tanggal Bayar
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

                        @foreach($tagihan->pembayaran as $index => $pembayaran)

                            <tr>

                                {{-- NOMOR --}}

                                <td class="tagihan-table-no">
                                    {{ $index + 1 }}
                                </td>


                                {{-- TANGGAL BAYAR --}}

                                <td>

                                    @if($pembayaran->tanggal_kirim)

                                        <div class="tagihan-table-date">

                                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_kirim)->format('d-m-Y') }}

                                        </div>

                                        <div class="tagihan-table-time">

                                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_kirim)->format('H:i') }}

                                        </div>

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- NOMINAL --}}

                                <td class="tagihan-table-nominal">

                                    Rp {{ number_format(
                                        $pembayaran->nominal ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </td>


                                {{-- METODE --}}

                                <td class="tagihan-table-method">

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

                                <td class="tagihan-table-status">

                                    @if($pembayaran->status === 'dibayar')

                                        <span class="tagihan-payment-status tagihan-payment-dibayar">
                                            <i class="bi bi-check-circle"></i>
                                            Dibayar
                                        </span>

                                    @elseif($pembayaran->status === 'menunggu')

                                        <span class="tagihan-payment-status tagihan-payment-menunggu">
                                            <i class="bi bi-clock"></i>
                                            Menunggu
                                        </span>

                                    @elseif($pembayaran->status === 'ditolak')

                                        <span class="tagihan-payment-status tagihan-payment-ditolak">
                                            <i class="bi bi-x-circle"></i>
                                            Ditolak
                                        </span>

                                    @else

                                        <span class="tagihan-payment-default">
                                            {{ $pembayaran->status ?? '-' }}
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>


                    {{-- TOTAL PEMBAYARAN --}}

                    <tfoot>

                        <tr>

                            <td
                                colspan="2"
                                class="tagihan-total-label"
                            >
                                Total Dibayar
                            </td>

                            <td class="tagihan-total-value">

                                Rp {{ number_format(
                                    $totalDibayar,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td colspan="2"></td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        @else

            {{-- BELUM ADA PEMBAYARAN --}}

            <div class="tagihan-empty">

                <div class="tagihan-empty-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <strong class="tagihan-empty-title">
                    Belum Ada Pembayaran
                </strong>

                <span class="tagihan-empty-description">
                    Belum ada transaksi pembayaran untuk tagihan ini.
                </span>

            </div>

        @endif

    </div>


    {{-- =====================================================
         TOMBOL AKSI
    ====================================================== --}}

    <div class="tagihan-detail-actions">

        {{-- KEMBALI --}}

        <a
            href="{{ route('admin.tagihan.index') }}"
            class="tagihan-detail-btn tagihan-detail-btn-kembali"
        >
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>


        {{-- EDIT --}}

        <a
            href="{{ route('admin.tagihan.edit', $tagihan->id) }}"
            class="tagihan-detail-btn tagihan-detail-btn-edit"
        >
            <i class="bi bi-pencil-square"></i>
            <span>Edit Tagihan</span>
        </a>

    </div>

</div>

@endsection