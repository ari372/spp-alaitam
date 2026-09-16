@extends('layouts.admin')

@section('title', 'Persetujuan Pembayaran')

@section('page-title', 'Persetujuan Pembayaran')

@section('content')

@vite('resources/css/admin/pembayaran.css')

<div class="pembayaran-container">

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="pembayaran-header">

        <div class="pembayaran-header-content">

            <div class="pembayaran-header-icon">
                <i data-lucide="credit-card"></i>
            </div>

            <div>

                <h2>
                    Persetujuan Pembayaran
                </h2>

                <p>
                    Periksa detail, bukti pembayaran, dan proses pembayaran siswa.
                </p>

            </div>

        </div>


        <a
            href="{{ route('admin.pembayaran.manual') }}"
            class="btn-manual"
        >

            <i data-lucide="plus"></i>

            Pembayaran Manual

        </a>

    </div>


    {{-- =====================================================
        SUCCESS
    ====================================================== --}}

    @if(session('success'))

        <div class="pembayaran-alert pembayaran-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
        ERROR
    ====================================================== --}}

    @if(session('error'))

        <div class="pembayaran-alert pembayaran-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
        VALIDATION ERROR
    ====================================================== --}}

    @if($errors->any())

        <div class="pembayaran-alert pembayaran-alert-error">

            <i data-lucide="circle-alert"></i>

            <div>

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        </div>

    @endif


    {{-- =====================================================
        DAFTAR PEMBAYARAN
    ====================================================== --}}

    @forelse($pembayaran as $item)

        @php

            $tagihan = $item->tagihan;

            $totalTagihan = (float) ($tagihan->nominal ?? 0);

            $sudahDibayar = $tagihan
                ? $tagihan->pembayaran
                    ->whereIn('status', ['dibayar', 'disetujui'])
                    ->sum('nominal')
                : 0;

            $sisaTagihan = max(
                $totalTagihan - $sudahDibayar,
                0
            );

        @endphp


        <div class="pembayaran-card">


            {{-- =================================================
                CARD HEADER
            ================================================== --}}

            <div class="pembayaran-card-header">

                <div class="pembayaran-card-title">

                    <div class="pembayaran-title-icon">
                        <i data-lucide="credit-card"></i>
                    </div>

                    <div>

                        <h3>
                            Pembayaran Baru
                        </h3>

                        <p>
                            Menunggu pemeriksaan admin
                        </p>

                    </div>

                </div>


                <span class="pembayaran-status">

                    <i data-lucide="clock-3"></i>

                    Menunggu Persetujuan

                </span>

            </div>



            {{-- =================================================
                MAIN GRID
            ================================================== --}}

            <div class="pembayaran-main-grid">


                {{-- =================================================
                    INFORMASI PEMBAYARAN
                ================================================== --}}

                <div class="pembayaran-detail-card">


                    <div class="pembayaran-section-header">

                        <div class="pembayaran-section-icon">
                            <i data-lucide="clipboard-list"></i>
                        </div>

                        <div>

                            <h4>
                                Informasi Pembayaran
                            </h4>

                            <p>
                                Detail pembayaran yang dikirim
                            </p>

                        </div>

                    </div>


                    <div class="pembayaran-detail-list">


                        {{-- SISWA --}}

                        <div class="pembayaran-detail-row">

                            <div class="pembayaran-detail-label">

                                <i data-lucide="user-round"></i>

                                <span>
                                    Siswa
                                </span>

                            </div>

                            <div class="pembayaran-detail-value">

                                {{ $tagihan->siswa->nama ?? '-' }}

                            </div>

                        </div>


                        {{-- NIS --}}

                        <div class="pembayaran-detail-row">

                            <div class="pembayaran-detail-label">

                                <i data-lucide="badge"></i>

                                <span>
                                    NIS
                                </span>

                            </div>

                            <div class="pembayaran-detail-value">

                                {{ $tagihan->siswa->nis ?? '-' }}

                            </div>

                        </div>


                        {{-- TAHUN AJARAN --}}

                        <div class="pembayaran-detail-row">

                            <div class="pembayaran-detail-label">

                                <i data-lucide="calendar-days"></i>

                                <span>
                                    Tahun Ajaran
                                </span>

                            </div>

                            <div class="pembayaran-detail-value">

                                {{ $tagihan->tahunAjaran->nama ?? '-' }}

                            </div>

                        </div>


                        {{-- KATEGORI --}}

                        <div class="pembayaran-detail-row">

                            <div class="pembayaran-detail-label">

                                <i data-lucide="tag"></i>

                                <span>
                                    Kategori
                                </span>

                            </div>

                            <div class="pembayaran-detail-value">

                                {{ $tagihan->kategori->nama ?? '-' }}

                            </div>

                        </div>


                        {{-- NOMINAL --}}

                        <div class="pembayaran-detail-row">

                            <div class="pembayaran-detail-label">

                                <i data-lucide="circle-dollar-sign"></i>

                                <span>
                                    Nominal
                                </span>

                            </div>

                            <div class="pembayaran-detail-value">

                                @if($item->nominal !== null)

                                    <strong class="nominal-normal">

                                        Rp {{ number_format(
                                            $item->nominal,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </strong>

                                @else

                                    <span class="nominal-belum">

                                        Belum ditentukan

                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- METODE --}}

                        <div class="pembayaran-detail-row">

                            <div class="pembayaran-detail-label">

                                <i data-lucide="wallet-cards"></i>

                                <span>
                                    Metode Pembayaran
                                </span>

                            </div>

                            <div class="pembayaran-detail-value">

                                @if($item->metode)

                                    <span class="metode-badge">

                                        {{ strtoupper($item->metode) }}

                                    </span>

                                @else

                                    -

                                @endif

                            </div>

                        </div>


                        {{-- WAKTU --}}

                        <div class="pembayaran-detail-row">

                            <div class="pembayaran-detail-label">

                                <i data-lucide="clock"></i>

                                <span>
                                    Waktu Pengiriman
                                </span>

                            </div>

                            <div class="pembayaran-detail-value">

                                {{ $item->tanggal_kirim
                                    ? $item->tanggal_kirim->format('d-m-Y H:i')
                                    : '-'
                                }}

                            </div>

                        </div>


                    </div>



                    {{-- =================================================
                        RINGKASAN TAGIHAN
                    ================================================== --}}

                    <div class="pembayaran-summary">


                        <div class="pembayaran-summary-header">

                            <i data-lucide="receipt"></i>

                            <span>
                                Ringkasan Tagihan
                            </span>

                        </div>


                        <div class="pembayaran-summary-grid">


                            <div class="pembayaran-summary-item">

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


                            <div class="pembayaran-summary-item">

                                <span>
                                    Sudah Dibayar
                                </span>

                                <strong>

                                    Rp {{ number_format(
                                        $sudahDibayar,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </div>


                            <div class="pembayaran-summary-item">

                                <span>
                                    Sisa Tagihan
                                </span>

                                <strong class="summary-sisa">

                                    Rp {{ number_format(
                                        $sisaTagihan,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </div>


                        </div>

                    </div>


                </div>



                {{-- =================================================
                    BUKTI PEMBAYARAN
                ================================================== --}}

                <div class="pembayaran-proof-card">


                    <div class="pembayaran-section-header">

                        <div class="pembayaran-section-icon">

                            <i data-lucide="image"></i>

                        </div>

                        <div>

                            <h4>
                                Bukti Pembayaran
                            </h4>

                            <p>
                                Bukti yang dikirim oleh orang tua
                            </p>

                        </div>

                    </div>


                    @if($item->bukti_pembayaran)


                        <div class="pembayaran-proof-wrapper">

                            <img
                                src="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                alt="Bukti pembayaran"
                                class="pembayaran-proof-image"
                            >

                        </div>


                        <a
                            href="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="pembayaran-proof-button"
                        >

                            <i data-lucide="maximize-2"></i>

                            Lihat Bukti Penuh

                        </a>


                    @else


                        <div class="pembayaran-proof-empty">

                            <i data-lucide="image-off"></i>

                            <strong>
                                Tidak ada bukti pembayaran
                            </strong>

                            <span>
                                Bukti pembayaran belum tersedia.
                            </span>

                        </div>


                    @endif


                </div>


            </div>



            {{-- =================================================
                TINDAKAN PEMBAYARAN
            ================================================== --}}

            <div class="pembayaran-action-card">


                <div class="pembayaran-action-header">

                    <div class="pembayaran-action-title">

                        <div class="pembayaran-action-icon">

                            <i data-lucide="zap"></i>

                        </div>

                        <div>

                            <h4>
                                Tindakan Pembayaran
                            </h4>

                            <p>
                                Pilih aksi yang ingin dilakukan untuk pembayaran ini.
                            </p>

                        </div>

                    </div>

                </div>



                <div class="pembayaran-actions">


                    {{-- =================================================
                        KOREKSI
                    ================================================== --}}

                    <a
                        href="{{ route(
                            'admin.pembayaran.edit',
                            $item->id
                        ) }}"
                        class="pembayaran-btn pembayaran-btn-koreksi"
                    >

                        <i data-lucide="pencil"></i>

                        Koreksi Pembayaran

                    </a>



                    {{-- =================================================
                        TOLAK
                    ================================================== --}}

                    <form
                        action="{{ route(
                            'admin.pembayaran.tolak',
                            $item->id
                        ) }}"
                        method="POST"
                        class="pembayaran-tolak-form"
                    >

                        @csrf

                        @method('PATCH')


                        <textarea
                            name="catatan"
                            placeholder="Alasan penolakan..."
                            required
                            class="pembayaran-tolak-textarea"
                        ></textarea>


                        <button
                            type="submit"
                            class="pembayaran-btn pembayaran-btn-tolak"
                            onclick="return confirm(
                                'Yakin ingin menolak pembayaran ini?'
                            )"
                        >

                            <i data-lucide="x"></i>

                            Tolak Pembayaran

                        </button>


                    </form>



                    {{-- =================================================
                        SETUJUI
                    ================================================== --}}

                    <form
                        action="{{ route(
                            'admin.pembayaran.setujui',
                            $item->id
                        ) }}"
                        method="POST"
                    >

                        @csrf

                        @method('PATCH')


                        <button
                            type="submit"
                            class="pembayaran-btn pembayaran-btn-setujui"
                            onclick="return confirm(
                                'Yakin pembayaran ini benar dan ingin menyetujuinya?'
                            )"
                        >

                            <i data-lucide="check"></i>

                            Setujui Pembayaran

                        </button>


                    </form>


                </div>


            </div>


        </div>


    @empty


        {{-- =================================================
            EMPTY STATE
        ================================================== --}}

        <div class="pembayaran-empty">


            <div class="pembayaran-empty-icon">

                <i data-lucide="circle-check"></i>

            </div>


            <h3>
                Tidak ada pembayaran baru
            </h3>


            <p>
                Semua pembayaran sudah diproses.
            </p>


            <a
                href="{{ route('admin.dashboard') }}"
                class="pembayaran-empty-button"
            >

                <i data-lucide="layout-dashboard"></i>

                Kembali ke Dashboard

            </a>


        </div>


    @endforelse


</div>



{{-- =========================================================
    LUCIDE ICON
========================================================= --}}

<script src="https://unpkg.com/lucide@latest"></script>

<script>

    document.addEventListener('DOMContentLoaded', function () {

        if (typeof lucide !== 'undefined') {

            lucide.createIcons();

        }

    });

</script>

@endsection