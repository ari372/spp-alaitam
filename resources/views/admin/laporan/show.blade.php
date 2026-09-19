@extends('layouts.admin')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan')

@push('styles')
    @vite('resources/css/admin/laporan.css')
@endpush

@section('content')

<div class="laporan-detail-page">

    {{-- HEADER --}}
    <div class="laporan-detail-header">

        <div class="laporan-detail-header-content">

            <div class="laporan-detail-header-icon">
                <i data-lucide="file-search"></i>
            </div>

            <div>
                <h2>Detail Pembayaran</h2>
                <p>Informasi lengkap data pembayaran siswa.</p>
            </div>

        </div>

        <a
            href="{{ route('admin.laporan.index') }}"
            class="laporan-back-button"
        >
            <i data-lucide="arrow-left"></i>
            Kembali
        </a>

    </div>


    {{-- ALERT --}}
    @if(session('success'))
        <div class="laporan-success">
            <i data-lucide="circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="laporan-error">
            <i data-lucide="circle-alert"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif


    <div class="laporan-detail-grid">

        {{-- INFORMASI SISWA --}}
        <div class="laporan-detail-card">

            <div class="laporan-detail-card-header">

                <div class="laporan-detail-card-header-icon">
                    <i data-lucide="user-round"></i>
                </div>

                <h3>Informasi Siswa</h3>

            </div>


            <div class="laporan-detail-list">

                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="user"></i>
                        Nama Siswa
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->tagihan?->siswa?->nama ?? '-' }}
                    </strong>

                </div>


                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="badge"></i>
                        NIS
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->tagihan?->siswa?->nis ?? '-' }}
                    </strong>

                </div>


                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="school"></i>
                        Kelas
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->tagihan?->siswa?->kelas?->nama_kelas ?? '-' }}
                    </strong>

                </div>

            </div>

        </div>


        {{-- INFORMASI TAGIHAN --}}
        <div class="laporan-detail-card">

            <div class="laporan-detail-card-header">

                <div class="laporan-detail-card-header-icon">
                    <i data-lucide="receipt"></i>
                </div>

                <h3>Informasi Tagihan</h3>

            </div>


            <div class="laporan-detail-list">

                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="tag"></i>
                        Kategori
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->tagihan?->kategori?->nama ?? '-' }}
                    </strong>

                </div>


                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="calendar-days"></i>
                        Tahun Ajaran
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->tagihan?->tahunAjaran?->nama ?? '-' }}
                    </strong>

                </div>


                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="receipt-text"></i>
                        Total Tagihan
                    </span>

                    <strong class="laporan-detail-value nominal-detail">
                        Rp {{ number_format(
                            $pembayaran->tagihan?->nominal ?? 0,
                            0,
                            ',',
                            '.'
                        ) }}
                    </strong>

                </div>

            </div>

        </div>


        {{-- INFORMASI PEMBAYARAN --}}
        <div class="laporan-detail-card">

            <div class="laporan-detail-card-header">

                <div class="laporan-detail-card-header-icon">
                    <i data-lucide="wallet"></i>
                </div>

                <h3>Informasi Pembayaran</h3>

            </div>


            <div class="laporan-detail-list">

                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="banknote"></i>
                        Nominal
                    </span>

                    <strong class="laporan-detail-value nominal-detail">
                        Rp {{ number_format(
                            $pembayaran->nominal ?? 0,
                            0,
                            ',',
                            '.'
                        ) }}
                    </strong>

                </div>


                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="credit-card"></i>
                        Metode
                    </span>

                    <strong class="laporan-detail-value">
                        {{ strtoupper($pembayaran->metode ?? '-') }}
                    </strong>

                </div>


                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="circle-check"></i>
                        Status
                    </span>

                    <strong class="laporan-detail-value">

                        @if($pembayaran->status === 'dibayar')

                            <span class="status-lunas">
                                Dibayar
                            </span>

                        @elseif($pembayaran->status === 'menunggu')

                            <span class="status-menunggu">
                                Menunggu
                            </span>

                        @else

                            <span class="status-belum">
                                Ditolak
                            </span>

                        @endif

                    </strong>

                </div>

            </div>

        </div>


        {{-- INFORMASI WAKTU --}}
        <div class="laporan-detail-card">

            <div class="laporan-detail-card-header">

                <div class="laporan-detail-card-header-icon">
                    <i data-lucide="clock"></i>
                </div>

                <h3>Informasi Waktu</h3>

            </div>


            <div class="laporan-detail-list">

                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="send"></i>
                        Tanggal Kirim
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->tanggal_kirim
                            ? $pembayaran->tanggal_kirim->format('d-m-Y H:i')
                            : '-'
                        }}
                    </strong>

                </div>


                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="circle-check"></i>
                        Tanggal Disetujui
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->tanggal_disetujui
                            ? $pembayaran->tanggal_disetujui->format('d-m-Y H:i')
                            : '-'
                        }}
                    </strong>

                </div>


                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        <i data-lucide="calendar"></i>
                        Dibuat
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->created_at
                            ? $pembayaran->created_at->format('d-m-Y H:i')
                            : '-'
                        }}
                    </strong>

                </div>

            </div>

        </div>

    </div>


    {{-- CATATAN --}}
    @if($pembayaran->catatan)

        <div class="laporan-detail-card">

            <div class="laporan-detail-card-header">

                <div class="laporan-detail-card-header-icon">
                    <i data-lucide="message-square-text"></i>
                </div>

                <h3>Catatan</h3>

            </div>

            <div class="laporan-detail-list">

                <div class="laporan-detail-row">

                    <span class="laporan-detail-label">
                        Catatan Pembayaran
                    </span>

                    <strong class="laporan-detail-value">
                        {{ $pembayaran->catatan }}
                    </strong>

                </div>

            </div>

        </div>

    @endif


    {{-- ACTION --}}
    <div class="laporan-detail-actions">

        <a
            href="{{ route('admin.laporan.index') }}"
            class="laporan-detail-button laporan-detail-button-back"
        >
            <i data-lucide="arrow-left"></i>
            Kembali
        </a>

        <a
            href="{{ route('admin.laporan.edit', $pembayaran->id) }}"
            class="laporan-detail-button laporan-detail-button-edit"
        >
            <i data-lucide="pencil"></i>
            Edit Pembayaran
        </a>

    </div>

</div>


<script src="https://unpkg.com/lucide@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>

@endsection