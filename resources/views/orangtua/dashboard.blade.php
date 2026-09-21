
@extends('layouts.orangtua')

@section('title', 'Dashboard Orang Tua')

@push('styles')
    @vite('resources/css/orang-tua/dashboard.css')
@endpush

@section('content')

@if (session('error'))
    <div class="alert alert-error">
        <i data-lucide="alert-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        <i data-lucide="check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="page-title">

    <div>
        <h1>Dashboard Orang Tua</h1>

        <p>
            Selamat datang di Sistem Pembayaran SPP.
        </p>
    </div>

    <div class="page-title-icon">
        <i data-lucide="layout-dashboard"></i>
    </div>

</div>

@if (!$siswa)

    <div class="table-card">

        <div class="empty-payment">

            <div class="empty-payment-icon">
                <i data-lucide="user-round-x"></i>
            </div>

            <strong>Data siswa belum ditemukan</strong>

            <span>
                Silakan hubungi admin sekolah untuk menghubungkan data siswa.
            </span>

        </div>

    </div>

@else

    {{-- =====================================================
         DATA ANAK DAN ORANG TUA
    ====================================================== --}}

    <div class="anak-card">

        <div class="anak-card-header">

            <div class="anak-card-header-icon">
                <i data-lucide="graduation-cap"></i>
            </div>

            <div class="anak-card-header-content">

                <h3>Data Anak</h3>

                <p>
                    Informasi siswa dan orang tua yang terhubung.
                </p>

            </div>

        </div>

        <div class="anak-info">

            <div class="anak-item anak-item-primary">

                <span>
                    <i data-lucide="user-round"></i>
                    Nama Siswa
                </span>

                <strong>
                    {{ $siswa->nama ?? '-' }}
                </strong>

            </div>

            <div class="anak-item">

                <span>
                    <i data-lucide="id-card"></i>
                    NIS
                </span>

                <strong>
                    {{ $siswa->nis ?? '-' }}
                </strong>

            </div>

            <div class="anak-item">

                <span>
                    <i data-lucide="school"></i>
                    Kelas
                </span>

                <strong>
                    {{ $siswa->kelas?->nama_kelas ?? $siswa->kelas?->nama ?? '-' }}
                </strong>

            </div>

            <div class="anak-item anak-item-parent">

                <span>
                    <i data-lucide="users"></i>
                    Nama Orang Tua
                </span>

                <strong>
                    {{ $siswa->orangTua?->nama ?? '-' }}
                </strong>

            </div>

        </div>

    </div>


    {{-- =====================================================
         RINGKASAN PEMBAYARAN
    ====================================================== --}}

    <div class="summary">

        <div class="summary-card">

            <div class="summary-card-header">

                <span>Total Tagihan</span>

                <div class="summary-card-icon">
                    <i data-lucide="receipt"></i>
                </div>

            </div>

            <strong>
                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
            </strong>

            <small>
                Total seluruh tagihan
            </small>

        </div>


        <div class="summary-card summary-card-success">

            <div class="summary-card-header">

                <span>Sudah Dibayar</span>

                <div class="summary-card-icon">
                    <i data-lucide="circle-check"></i>
                </div>

            </div>

            <strong>
                Rp {{ number_format($totalDibayar, 0, ',', '.') }}
            </strong>

            <small>
                Total pembayaran disetujui
            </small>

        </div>


        <div class="summary-card summary-card-warning">

            <div class="summary-card-header">

                <span>Sisa Tagihan</span>

                <div class="summary-card-icon">
                    <i data-lucide="wallet"></i>
                </div>

            </div>

            <strong>
                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
            </strong>

            <small>
                Total tagihan yang belum lunas
            </small>

        </div>

    </div>


    {{-- =====================================================
         FILTER TAGIHAN BELUM LUNAS
    ====================================================== --}}

    @php

        $tagihanBelumLunas = $tagihan
            ->filter(function ($item) {

                $sudahDibayar = $item->pembayaran
                    ->whereIn('status', [
                        'dibayar',
                        'disetujui'
                    ])
                    ->sum('nominal');

                $sisa = max(
                    (float) $item->nominal -
                    (float) $sudahDibayar,
                    0
                );

                return $sisa > 0;

            })
            ->values();

    @endphp


    {{-- =====================================================
         CARD TAGIHAN DAN RIWAYAT
    ====================================================== --}}

    <div class="table-card">

        <div class="table-card-header">

            <div class="table-card-header-left">

                <div class="table-card-header-icon">
                    <i data-lucide="wallet-cards"></i>
                </div>

                <div class="table-card-title">

                    <h3>Informasi Pembayaran</h3>

                    <p>
                        Kelola tagihan dan lihat riwayat pembayaran.
                    </p>

                </div>

            </div>

        </div>


        {{-- =================================================
             TAB
        ================================================== --}}

        <div class="payment-tabs">

            <button
                type="button"
                class="payment-tab active"
                onclick="showPaymentSection('tagihan', this)"
            >
                <i data-lucide="receipt-text"></i>
                Daftar Tagihan
            </button>

            <button
                type="button"
                class="payment-tab"
                onclick="showPaymentSection('riwayat', this)"
            >
                <i data-lucide="history"></i>
                Riwayat Pembayaran
            </button>

        </div>


        {{-- =================================================
             DAFTAR TAGIHAN
        ================================================== --}}

        <div
            id="section-tagihan"
            class="payment-section active"
        >

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Tahun Ajaran</th>
                            <th>Nominal</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse ($tagihanBelumLunas as $index => $item)

                        @php

                            $sudahDibayar = $item->pembayaran
                                ->whereIn('status', [
                                    'dibayar',
                                    'disetujui'
                                ])
                                ->sum('nominal');

                            $sisa = max(
                                (float) $item->nominal -
                                (float) $sudahDibayar,
                                0
                            );

                            $sedangDiproses = $item->pembayaran
                                ->contains('status', 'menunggu');

                            $ditolak = $item->pembayaran
                                ->contains('status', 'ditolak');

                            if ($sisa <= 0) {

                                $statusLabel = 'Lunas';
                                $statusClass = 'status-lunas';

                            } elseif ($sedangDiproses) {

                                $statusLabel = 'Menunggu Persetujuan';
                                $statusClass = 'status-menunggu';

                            } elseif (
                                $ditolak &&
                                $sudahDibayar <= 0
                            ) {

                                $statusLabel = 'Ditolak';
                                $statusClass = 'status-ditolak';

                            } elseif (
                                $sudahDibayar > 0 &&
                                $sisa > 0
                            ) {

                                $statusLabel = 'Sebagian';
                                $statusClass = 'status-sebagian';

                            } else {

                                $statusLabel = 'Belum Bayar';
                                $statusClass = 'status-belum';

                            }

                        @endphp

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $item->kategori->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $item->tahunAjaran->nama ?? '-' }}
                            </td>

                            <td>
                                <strong>
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </strong>
                            </td>

                            <td>

                                @if ($item->jatuh_tempo)

                                    {{ \Carbon\Carbon::parse($item->jatuh_tempo)->format('d-m-Y') }}

                                @else

                                    -

                                @endif

                            </td>

                            <td>

                                <span class="status {{ $statusClass }}">

                                    @if ($statusClass === 'status-menunggu')
                                        <i data-lucide="clock"></i>
                                    @elseif ($statusClass === 'status-ditolak')
                                        <i data-lucide="circle-x"></i>
                                    @elseif ($statusClass === 'status-lunas')
                                        <i data-lucide="check-circle"></i>
                                    @else
                                        <i data-lucide="circle-alert"></i>
                                    @endif

                                    {{ $statusLabel }}

                                </span>

                            </td>

                            <td>

                                <div class="payment-actions">

                                    <button
                                        type="button"
                                        class="btn-detail"
                                        title="Detail Tagihan"
                                        aria-label="Detail Tagihan"
                                        onclick="openDetailTagihan({{ $item->id }})"
                                    >
                                        <i data-lucide="info"></i>
                                    </button>

                                    @if ($sedangDiproses)

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
                                                <i data-lucide="credit-card"></i>
                                                Bayar
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="empty">

                                <div class="empty-payment">

                                    <div class="empty-payment-icon">
                                        <i data-lucide="check-circle"></i>
                                    </div>

                                    <strong>
                                        Semua tagihan sudah lunas
                                    </strong>

                                    <span>
                                        Tidak ada tagihan yang perlu dibayar saat ini.
                                    </span>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                 MODAL DETAIL TAGIHAN
            ================================================== --}}

            @foreach ($tagihanBelumLunas as $item)

                @php

                    $sudahDibayar = $item->pembayaran
                        ->whereIn('status', [
                            'dibayar',
                            'disetujui'
                        ])
                        ->sum('nominal');

                    $sisa = max(
                        (float) $item->nominal -
                        (float) $sudahDibayar,
                        0
                    );

                    $sedangDiproses = $item->pembayaran
                        ->contains('status', 'menunggu');

                    $ditolak = $item->pembayaran
                        ->contains('status', 'ditolak');

                    if ($sisa <= 0) {

                        $statusLabel = 'Lunas';
                        $statusClass = 'status-lunas';

                    } elseif ($sedangDiproses) {

                        $statusLabel = 'Menunggu Persetujuan';
                        $statusClass = 'status-menunggu';

                    } elseif (
                        $ditolak &&
                        $sudahDibayar <= 0
                    ) {

                        $statusLabel = 'Ditolak';
                        $statusClass = 'status-ditolak';

                    } elseif (
                        $sudahDibayar > 0 &&
                        $sisa > 0
                    ) {

                        $statusLabel = 'Sebagian';
                        $statusClass = 'status-sebagian';

                    } else {

                        $statusLabel = 'Belum Bayar';
                        $statusClass = 'status-belum';

                    }

                    $persentasePembayaran = $item->nominal > 0
                        ? min(
                            100,
                            round(
                                ($sudahDibayar / $item->nominal) * 100
                            )
                        )
                        : 0;

                @endphp

                <div
                    id="detail-tagihan-{{ $item->id }}"
                    class="detail-modal"
                    aria-hidden="true"
                >

                    <div
                        class="detail-modal-overlay"
                        onclick="closeDetailTagihan({{ $item->id }})"
                    ></div>

                    <div
                        class="detail-modal-content"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="detail-tagihan-title-{{ $item->id }}"
                    >

                        <div class="detail-modal-header">

                            <div class="detail-modal-header-left">

                                <div class="detail-header-icon">
                                    <i data-lucide="file-text"></i>
                                </div>

                                <div>

                                    <h3 id="detail-tagihan-title-{{ $item->id }}">
                                        Detail Tagihan
                                    </h3>

                                    <p>
                                        Informasi lengkap tagihan siswa.
                                    </p>

                                </div>

                            </div>

                            <button
                                type="button"
                                class="detail-modal-close"
                                onclick="closeDetailTagihan({{ $item->id }})"
                                aria-label="Tutup"
                            >
                                <i data-lucide="x"></i>
                            </button>

                        </div>


                        <div class="detail-modal-body">

                            <div class="detail-pembayaran-layout">

                                <div class="detail-pembayaran-left">

                                    <div class="detail-card-heading">
                                        Detail Tagihan
                                    </div>

                                    <div class="detail-item">
                                        <span>Kategori</span>
                                        <strong>
                                            {{ $item->kategori->nama ?? '-' }}
                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>Tahun Ajaran</span>
                                        <strong>
                                            {{ $item->tahunAjaran->nama ?? '-' }}
                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>Nama Siswa</span>
                                        <strong>
                                            {{ $siswa->nama ?? '-' }}
                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>Nama Orang Tua</span>
                                        <strong>
                                            {{ $siswa->orangTua?->nama ?? '-' }}
                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>NIS</span>
                                        <strong>
                                            {{ $siswa->nis ?? '-' }}
                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>Kelas</span>
                                        <strong>
                                            {{ $siswa->kelas?->nama_kelas ?? $siswa->kelas?->nama ?? '-' }}
                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>Total Tagihan</span>
                                        <strong>
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>Sudah Dibayar</span>
                                        <strong>
                                            Rp {{ number_format($sudahDibayar, 0, ',', '.') }}
                                        </strong>
                                    </div>

                                    <div class="detail-item detail-item-sisa">
                                        <span>Sisa Tagihan</span>
                                        <strong>
                                            Rp {{ number_format($sisa, 0, ',', '.') }}
                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>Jatuh Tempo</span>
                                        <strong>

                                            @if ($item->jatuh_tempo)

                                                {{ \Carbon\Carbon::parse($item->jatuh_tempo)->format('d-m-Y') }}

                                            @else

                                                Tidak ditentukan

                                            @endif

                                        </strong>
                                    </div>

                                    <div class="detail-item">
                                        <span>Status</span>
                                        <strong>
                                            <span class="status {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </strong>
                                    </div>

                                    @if ($sudahDibayar > 0 && $sisa > 0)

                                        <div class="detail-payment-info">

                                            <i data-lucide="info"></i>

                                            <div>

                                                <strong>
                                                    Pembayaran Sebagian
                                                </strong>

                                                <p>
                                                    Tagihan sudah dibayar sebesar
                                                    Rp {{ number_format($sudahDibayar, 0, ',', '.') }}.
                                                    Sisa pembayaran:
                                                    Rp {{ number_format($sisa, 0, ',', '.') }}.
                                                </p>

                                            </div>

                                        </div>

                                    @elseif ($sedangDiproses)

                                        <div class="detail-payment-info">

                                            <i data-lucide="clock"></i>

                                            <div>

                                                <strong>
                                                    Pembayaran Sedang Diproses
                                                </strong>

                                                <p>
                                                    Pembayaran sudah dikirim dan
                                                    sedang menunggu persetujuan admin.
                                                </p>

                                            </div>

                                        </div>

                                    @elseif ($ditolak && $sudahDibayar <= 0)

                                        <div class="detail-payment-info detail-info-danger">

                                            <i data-lucide="circle-x"></i>

                                            <div>

                                                <strong>
                                                    Pembayaran Ditolak
                                                </strong>

                                                <p>
                                                    Pembayaran sebelumnya ditolak.
                                                    Silakan lakukan pembayaran kembali
                                                    sesuai sisa tagihan.
                                                </p>

                                            </div>

                                        </div>

                                    @endif

                                </div>


                                <div class="detail-pembayaran-right">

                                    <div class="detail-pembayaran-right-title">
                                        Ringkasan Tagihan
                                    </div>

                                    <div class="detail-summary-box">

                                        <span>Total Tagihan</span>

                                        <strong>
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </strong>

                                    </div>

                                    <div class="detail-summary-box">

                                        <span>Sudah Dibayar</span>

                                        <strong>
                                            Rp {{ number_format($sudahDibayar, 0, ',', '.') }}
                                        </strong>

                                    </div>

                                    <div class="detail-summary-box detail-summary-sisa">

                                        <span>Sisa Tagihan</span>

                                        <strong>
                                            Rp {{ number_format($sisa, 0, ',', '.') }}
                                        </strong>

                                    </div>

                                    <div class="detail-progress-box">

                                        <div class="detail-progress-header">

                                            <span>
                                                Progress Pembayaran
                                            </span>

                                            <strong>
                                                {{ $persentasePembayaran }}%
                                            </strong>

                                        </div>

                                        <div class="detail-progress">

                                            <div
                                                class="detail-progress-bar"
                                                style="width: {{ $persentasePembayaran }}%;"
                                            ></div>

                                        </div>

                                    </div>

                                    <div class="detail-status-box">

                                        <span>
                                            Status Pembayaran
                                        </span>

                                        <span class="status {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>

                                    </div>

                                    @if ($sudahDibayar >= $item->nominal)

                                        <div class="detail-payment-info">

                                            <i data-lucide="check-circle"></i>

                                            <div>

                                                <strong>
                                                    Pembayaran Lunas
                                                </strong>

                                                <p>
                                                    Seluruh tagihan ini sudah
                                                    dibayarkan dan tidak memiliki
                                                    sisa pembayaran.
                                                </p>

                                            </div>

                                        </div>

                                    @elseif ($sedangDiproses)

                                        <div class="detail-payment-info">

                                            <i data-lucide="clock"></i>

                                            <div>

                                                <strong>
                                                    Pembayaran Sedang Diproses
                                                </strong>

                                                <p>
                                                    Pembayaran sudah dikirim dan
                                                    sedang menunggu persetujuan admin.
                                                </p>

                                            </div>

                                        </div>

                                    @elseif ($ditolak)

                                        <div class="detail-payment-info detail-info-danger">

                                            <i data-lucide="circle-x"></i>

                                            <div>

                                                <strong>
                                                    Pembayaran Ditolak
                                                </strong>

                                                <p>
                                                    Pembayaran sebelumnya ditolak.
                                                    Silakan melakukan pembayaran kembali.
                                                </p>

                                            </div>

                                        </div>

                                    @else

                                        <div class="detail-payment-info">

                                            <i data-lucide="info"></i>

                                            <div>

                                                <strong>
                                                    Belum Lunas
                                                </strong>

                                                <p>
                                                    Silakan melakukan pembayaran
                                                    sesuai dengan sisa tagihan.
                                                </p>

                                            </div>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="detail-modal-footer">

                            <button
                                type="button"
                                class="btn-detail-tutup"
                                onclick="closeDetailTagihan({{ $item->id }})"
                            >
                                <i data-lucide="x"></i>
                                Tutup
                            </button>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        {{-- =================================================
             RIWAYAT PEMBAYARAN
        ================================================== --}}

        <div
            id="section-riwayat"
            class="payment-section"
        >

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Tahun Ajaran</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse ($pembayaran as $index => $item)

                        @php

                            $totalTagihanPembayaran = $item->tagihan
                                ? (float) $item->tagihan->nominal
                                : 0;

                            $totalDibayarTagihan = $item->tagihan
                                ? $item->tagihan->pembayaran
                                    ->whereIn('status', [
                                        'dibayar',
                                        'disetujui'
                                    ])
                                    ->sum('nominal')
                                : 0;

                            $tagihanLunas =
                                $totalTagihanPembayaran > 0 &&
                                $totalDibayarTagihan >= $totalTagihanPembayaran;

                        @endphp

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>

                                @if ($item->tanggal_kirim)

                                    {{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('d-m-Y') }}

                                @else

                                    -

                                @endif

                            </td>

                            <td>
                                {{ $item->tagihan->kategori->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $item->tagihan->tahunAjaran->nama ?? '-' }}
                            </td>

                            <td>
                                <strong>
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </strong>
                            </td>

                            <td>

                                @if ($item->metode === 'transfer')

                                    Transfer Bank

                                @elseif ($item->metode === 'qris')

                                    QRIS

                                @elseif ($item->metode === 'cash')

                                    Tunai

                                @else

                                    {{ $item->metode ?? '-' }}

                                @endif

                            </td>

                            <td>

                                @if ($item->status === 'menunggu')

                                    <span class="status status-menunggu">
                                        <i data-lucide="clock"></i>
                                        Menunggu
                                    </span>

                                @elseif ($item->status === 'ditolak')

                                    <span class="status status-ditolak">
                                        <i data-lucide="circle-x"></i>
                                        Ditolak
                                    </span>

                                @elseif (in_array($item->status, ['dibayar', 'disetujui']))

                                    <span class="status status-lunas">
                                        <i data-lucide="check-circle"></i>

                                        @if ($tagihanLunas)
                                            Disetujui &amp; Lunas
                                        @else
                                            Disetujui
                                        @endif

                                    </span>

                                @else

                                    <span class="status status-belum">
                                        {{ ucfirst($item->status) }}
                                    </span>

                                @endif

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="btn-detail"
                                    title="Detail Pembayaran"
                                    aria-label="Detail Pembayaran"
                                    onclick="openDetailPembayaran({{ $item->id }})"
                                >
                                    <i data-lucide="info"></i>
                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="empty">

                                <div class="empty-payment">

                                    <div class="empty-payment-icon">
                                        <i data-lucide="history"></i>
                                    </div>

                                    <strong>
                                        Belum ada riwayat pembayaran
                                    </strong>

                                    <span>
                                        Transaksi pembayaran akan muncul di sini.
                                    </span>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                 MODAL DETAIL PEMBAYARAN
            ================================================== --}}

            @foreach ($pembayaran as $item)

                @php

                    $totalTagihanPembayaran = $item->tagihan
                        ? (float) $item->tagihan->nominal
                        : 0;

                    $totalDibayarTagihan = $item->tagihan
                        ? $item->tagihan->pembayaran
                            ->whereIn('status', [
                                'dibayar',
                                'disetujui'
                            ])
                            ->sum('nominal')
                        : 0;

                    $tagihanLunas =
                        $totalTagihanPembayaran > 0 &&
                        $totalDibayarTagihan >= $totalTagihanPembayaran;

                    $sisaTagihanPembayaran = max(
                        $totalTagihanPembayaran -
                        $totalDibayarTagihan,
                        0
                    );

                @endphp

                <div
                    id="detail-pembayaran-{{ $item->id }}"
                    class="detail-modal payment-detail-modal"
                    aria-hidden="true"
                >

                    <div
                        class="detail-modal-overlay"
                        onclick="closeDetailPembayaran({{ $item->id }})"
                    ></div>

                    <div
                        class="detail-modal-content payment-detail-modal-content"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="payment-detail-title-{{ $item->id }}"
                    >

                        <div class="payment-detail-header">

                            <div class="payment-detail-header-left">

                                <div class="payment-detail-icon">
                                    <i data-lucide="file-text"></i>
                                </div>

                                <div>

                                    <h3 id="payment-detail-title-{{ $item->id }}">
                                        Detail Pembayaran
                                    </h3>

                                    <p>
                                        Informasi lengkap transaksi pembayaran.
                                    </p>

                                </div>

                            </div>

                            <button
                                type="button"
                                class="detail-modal-close"
                                onclick="closeDetailPembayaran({{ $item->id }})"
                                aria-label="Tutup"
                            >
                                <i data-lucide="x"></i>
                            </button>

                        </div>


                        <div class="payment-detail-body">

                            <div class="payment-detail-layout">

                                <div class="payment-detail-left">

                                    <div class="payment-detail-card">

                                        <div class="payment-detail-card-title">

                                            <h4>
                                                Detail Transaksi
                                            </h4>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Kategori
                                            </div>

                                            <div class="payment-detail-value">
                                                {{ $item->tagihan->kategori->nama ?? '-' }}
                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Tahun Ajaran
                                            </div>

                                            <div class="payment-detail-value">
                                                {{ $item->tagihan->tahunAjaran->nama ?? '-' }}
                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Nama Siswa
                                            </div>

                                            <div class="payment-detail-value">
                                                {{ $item->tagihan->siswa->nama ?? $siswa->nama ?? '-' }}
                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Nama Orang Tua
                                            </div>

                                            <div class="payment-detail-value">
                                                {{ $siswa->orangTua?->nama ?? '-' }}
                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                NIS
                                            </div>

                                            <div class="payment-detail-value">
                                                {{ $item->tagihan->siswa->nis ?? $siswa->nis ?? '-' }}
                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Kelas
                                            </div>

                                            <div class="payment-detail-value">
                                                {{ $item->tagihan->siswa->kelas?->nama_kelas ?? $siswa->kelas?->nama_kelas ?? '-' }}
                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Nominal Pembayaran
                                            </div>

                                            <div class="payment-detail-value payment-amount">
                                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Metode Pembayaran
                                            </div>

                                            <div class="payment-detail-value">

                                                @if ($item->metode === 'transfer')

                                                    Transfer Bank

                                                @elseif ($item->metode === 'qris')

                                                    QRIS

                                                @elseif ($item->metode === 'cash')

                                                    Tunai

                                                @else

                                                    {{ $item->metode ?? '-' }}

                                                @endif

                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Tanggal Pembayaran
                                            </div>

                                            <div class="payment-detail-value">

                                                @if ($item->tanggal_kirim)

                                                    {{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('d-m-Y H:i') }}

                                                @else

                                                    -

                                                @endif

                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Tanggal Disetujui
                                            </div>

                                            <div class="payment-detail-value">

                                                @if ($item->tanggal_disetujui)

                                                    {{ \Carbon\Carbon::parse($item->tanggal_disetujui)->format('d-m-Y H:i') }}

                                                @else

                                                    -

                                                @endif

                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Status
                                            </div>

                                            <div class="payment-detail-value">

                                                @if ($item->status === 'menunggu')

                                                    <span class="payment-status-badge payment-status-pending">
                                                        Menunggu
                                                    </span>

                                                @elseif ($item->status === 'ditolak')

                                                    <span class="payment-status-badge payment-status-danger">
                                                        Ditolak
                                                    </span>

                                                @elseif (in_array($item->status, ['dibayar', 'disetujui']))

                                                    <span class="payment-status-badge payment-status-success">

                                                        @if ($tagihanLunas)
                                                            Disetujui &amp; Lunas
                                                        @else
                                                            Disetujui
                                                        @endif

                                                    </span>

                                                @else

                                                    <span class="payment-status-badge">
                                                        {{ ucfirst($item->status) }}
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Sisa Tagihan
                                            </div>

                                            <div class="payment-detail-value payment-remaining">
                                                Rp {{ number_format($sisaTagihanPembayaran, 0, ',', '.') }}
                                            </div>

                                        </div>


                                        {{-- INFO STATUS --}}

                                        @if (
                                            in_array($item->status, ['dibayar', 'disetujui']) &&
                                            $tagihanLunas
                                        )

                                            <div class="payment-info-box payment-info-success">

                                                <div class="payment-info-icon">
                                                    <i data-lucide="check"></i>
                                                </div>

                                                <div>

                                                    <strong>
                                                        Pembayaran Berhasil
                                                    </strong>

                                                    <p>
                                                        Pembayaran telah disetujui
                                                        oleh admin dan seluruh
                                                        tagihan ini sudah lunas.
                                                    </p>

                                                </div>

                                            </div>

                                        @elseif (
                                            in_array($item->status, ['dibayar', 'disetujui']) &&
                                            !$tagihanLunas
                                        )

                                            <div class="payment-info-box payment-info-success">

                                                <div class="payment-info-icon">
                                                    <i data-lucide="check"></i>
                                                </div>

                                                <div>

                                                    <strong>
                                                        Pembayaran Berhasil
                                                    </strong>

                                                    <p>
                                                        Pembayaran telah disetujui
                                                        oleh admin.

                                                        Sisa tagihan:
                                                        Rp {{ number_format($sisaTagihanPembayaran, 0, ',', '.') }}.
                                                    </p>

                                                </div>

                                            </div>

                                        @elseif ($item->status === 'menunggu')

                                            <div class="payment-info-box payment-info-pending">

                                                <div class="payment-info-icon">
                                                    <i data-lucide="clock"></i>
                                                </div>

                                                <div>

                                                    <strong>
                                                        Pembayaran Sedang Diproses
                                                    </strong>

                                                    <p>
                                                        Pembayaran sudah dikirim
                                                        dan sedang menunggu
                                                        persetujuan admin.
                                                    </p>

                                                </div>

                                            </div>

                                        @elseif ($item->status === 'ditolak')

                                            <div class="payment-info-box payment-info-danger">

                                                <div class="payment-info-icon">
                                                    <i data-lucide="x"></i>
                                                </div>

                                                <div>

                                                    <strong>
                                                        Pembayaran Ditolak
                                                    </strong>

                                                    <p>
                                                        Pembayaran ini ditolak
                                                        oleh admin.
                                                    </p>

                                                </div>

                                            </div>

                                        @endif


                                        {{-- CATATAN ADMIN --}}

                                        @if (
                                            $item->status === 'ditolak' &&
                                            $item->catatan
                                        )

                                            <div class="payment-admin-note">

                                                <strong>
                                                    Catatan Admin
                                                </strong>

                                                <p>
                                                    {{ $item->catatan }}
                                                </p>

                                            </div>

                                        @endif

                                    </div>

                                </div>


                                {{-- KOLOM KANAN --}}

                                <div class="payment-detail-right">

                                    <div class="payment-proof-card">

                                        <div class="payment-proof-header">

                                            <div class="payment-proof-header-icon">
                                                <i data-lucide="image"></i>
                                            </div>

                                            <div>

                                                <h4>
                                                    Bukti Pembayaran
                                                </h4>

                                                <p>
                                                    Dokumen atau foto bukti transaksi.
                                                </p>

                                            </div>

                                        </div>


                                        @if ($item->bukti_pembayaran)

                                            <div class="payment-proof-image-wrapper">

                                                <a
                                                    href="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >

                                                    <img
                                                        src="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                                        alt="Bukti Pembayaran"
                                                        class="payment-proof-image"
                                                    >

                                                </a>

                                            </div>


                                            <div class="proof-actions">

                                                <a
                                                    href="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="btn-proof btn-proof-view"
                                                >
                                                    <i data-lucide="eye"></i>
                                                    Lihat Bukti
                                                </a>

                                                <a
                                                    href="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                                    download="bukti-pembayaran-{{ $item->id }}"
                                                    class="btn-proof btn-proof-download"
                                                >
                                                    <i data-lucide="download"></i>
                                                    Unduh Bukti
                                                </a>

                                                <a
                                                    href="{{ route('orangtua.pembayaran.download', ['pembayaran' => $item->id]) }}"
                                                    class="btn-proof btn-proof-pdf"
                                                >
                                                    <i data-lucide="file-down"></i>
                                                    Unduh Detail
                                                </a>

                                            </div>

                                        @else

                                            <div class="payment-proof-empty">

                                                <div class="payment-proof-empty-icon">
                                                    <i data-lucide="file-x"></i>
                                                </div>

                                                <strong>
                                                    Bukti pembayaran tidak tersedia
                                                </strong>

                                                <span>
                                                    Tidak ada file bukti pembayaran
                                                    pada transaksi ini.
                                                </span>

                                            </div>

                                        @endif

                                    </div>


                                    {{-- RINGKASAN PEMBAYARAN --}}

                                    <div class="payment-side-summary">

                                        <div class="payment-side-summary-title">
                                            Ringkasan Pembayaran
                                        </div>

                                        <div class="payment-side-summary-row">

                                            <span>
                                                Total Tagihan
                                            </span>

                                            <strong>
                                                Rp {{ number_format($totalTagihanPembayaran, 0, ',', '.') }}
                                            </strong>

                                        </div>

                                        <div class="payment-side-summary-row">

                                            <span>
                                                Total Dibayar
                                            </span>

                                            <strong>
                                                Rp {{ number_format($totalDibayarTagihan, 0, ',', '.') }}
                                            </strong>

                                        </div>

                                        <div class="payment-side-summary-row payment-side-summary-remaining">

                                            <span>
                                                Sisa
                                            </span>

                                            <strong>
                                                Rp {{ number_format($sisaTagihanPembayaran, 0, ',', '.') }}
                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="detail-modal-footer">

                            <button
                                type="button"
                                class="btn-detail-tutup"
                                onclick="closeDetailPembayaran({{ $item->id }})"
                            >
                                <i data-lucide="x"></i>
                                Tutup
                            </button>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

@endif


{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

<script>

    /*
     * =========================================================
     * INISIALISASI LUCIDE
     * =========================================================
     */

    function refreshLucideIcons() {

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

    }


    /*
     * =========================================================
     * TAB PEMBAYARAN
     * =========================================================
     */

    function showPaymentSection(section, button) {

        document
            .querySelectorAll('.payment-section')
            .forEach(function (element) {

                element.classList.remove('active');

            });


        document
            .querySelectorAll('.payment-tab')
            .forEach(function (element) {

                element.classList.remove('active');

            });


        const target = document.getElementById(
            'section-' + section
        );


        if (target) {

            target.classList.add('active');

        }


        if (button) {

            button.classList.add('active');

        }


        refreshLucideIcons();

    }


    /*
     * =========================================================
     * DETAIL TAGIHAN
     * =========================================================
     */

    function openDetailTagihan(id) {

        const modal = document.getElementById(
            'detail-tagihan-' + id
        );


        if (!modal) {
            return;
        }


        modal.classList.add('show');

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add('modal-open');

        refreshLucideIcons();

    }


    function closeDetailTagihan(id) {

        const modal = document.getElementById(
            'detail-tagihan-' + id
        );


        if (!modal) {
            return;
        }


        modal.classList.remove('show');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );


        const activeModal = document.querySelector(
            '.detail-modal.show'
        );


        if (!activeModal) {

            document.body.classList.remove(
                'modal-open'
            );

        }

    }


    /*
     * =========================================================
     * DETAIL PEMBAYARAN
     * =========================================================
     */

    function openDetailPembayaran(id) {

        const modal = document.getElementById(
            'detail-pembayaran-' + id
        );


        if (!modal) {
            return;
        }


        modal.classList.add('show');

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add('modal-open');

        refreshLucideIcons();

    }


    function closeDetailPembayaran(id) {

        const modal = document.getElementById(
            'detail-pembayaran-' + id
        );


        if (!modal) {
            return;
        }


        modal.classList.remove('show');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );


        const activeModal = document.querySelector(
            '.detail-modal.show'
        );


        if (!activeModal) {

            document.body.classList.remove(
                'modal-open'
            );

        }

    }


    /*
     * =========================================================
     * ESCAPE UNTUK MENUTUP MODAL
     * =========================================================
     */

    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key !== 'Escape') {
                return;
            }


            document
                .querySelectorAll('.detail-modal.show')
                .forEach(function (modal) {

                    modal.classList.remove('show');

                    modal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                });


            document.body.classList.remove(
                'modal-open'
            );

        }
    );


    /*
     * =========================================================
     * INISIALISASI HALAMAN
     * =========================================================
     */

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            refreshLucideIcons();

        }
    );

</script>

@endsection