@extends('layouts.orangtua')

@section('title', 'Dashboard Orang Tua')

@push('styles')
    @vite('resources/css/orang-tua/dashboard.css')
@endpush

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


    {{-- =====================================================
         DATA ANAK
    ====================================================== --}}

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


    {{-- =====================================================
         RINGKASAN
    ====================================================== --}}

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
         TAGIHAN & RIWAYAT
    ====================================================== --}}

    <div class="table-card">


        {{-- =================================================
             TAB
        ================================================== --}}

        <div class="payment-tabs">

            <button
                type="button"
                class="payment-tab active"
                onclick="showPaymentSection('tagihan', this)"
            >
                Daftar Tagihan
            </button>


            <button
                type="button"
                class="payment-tab"
                onclick="showPaymentSection('riwayat', this)"
            >
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
                                    Rp {{ number_format(
                                        $item->nominal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>

                            </td>


                            <td>

                                @if ($item->jatuh_tempo)

                                    {{ \Carbon\Carbon::parse(
                                        $item->jatuh_tempo
                                    )->format('d-m-Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            <td>

                                <span class="status {{ $statusClass }}">
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
                                        !
                                    </button>


                                    @if ($sedangDiproses)

                                        <span class="status status-menunggu">
                                            Diproses
                                        </span>

                                    @else

                                        <form
                                            action="{{ route(
                                                'orangtua.pembayaran.create',
                                                ['tagihan' => $item->id]
                                            ) }}"
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

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="empty"
                            >

                                <div class="empty-payment">

                                    <strong>
                                        Semua tagihan sudah lunas 🎉
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


                    $persentasePembayaran =
                        $item->nominal > 0
                            ? min(
                                100,
                                round(
                                    ($sudahDibayar /
                                    $item->nominal) *
                                    100
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
                                    📄
                                </div>

                                <div>

                                    <h3 id="detail-tagihan-title-{{ $item->id }}">
                                        Detail Tagihan
                                    </h3>

                                    <p>
                                        Informasi lengkap tagihan siswa
                                    </p>

                                </div>

                            </div>


                            <button
                                type="button"
                                class="detail-modal-close"
                                onclick="closeDetailTagihan({{ $item->id }})"
                                aria-label="Tutup"
                            >
                                ×
                            </button>

                        </div>


                        <div class="detail-modal-body">

                            <div class="detail-pembayaran-layout">

                                <div class="detail-pembayaran-left">

                                    <div class="detail-card-heading">
                                        Detail Tagihan
                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            Kategori
                                        </span>

                                        <strong>
                                            {{ $item->kategori->nama ?? '-' }}
                                        </strong>

                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            Tahun Ajaran
                                        </span>

                                        <strong>
                                            {{ $item->tahunAjaran->nama ?? '-' }}
                                        </strong>

                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            Nama Siswa
                                        </span>

                                        <strong>
                                            {{ $siswa->nama }}
                                        </strong>

                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            NIS
                                        </span>

                                        <strong>
                                            {{ $siswa->nis }}
                                        </strong>

                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            Kelas
                                        </span>

                                        <strong>
                                            {{ $siswa->kelas->nama_kelas ?? '-' }}
                                        </strong>

                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            Total Tagihan
                                        </span>

                                        <strong>
                                            Rp {{ number_format(
                                                $item->nominal,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </strong>

                                    </div>


                                    <div class="detail-item">

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


                                    <div class="detail-item detail-item-sisa">

                                        <span>
                                            Sisa Tagihan
                                        </span>

                                        <strong>
                                            Rp {{ number_format(
                                                $sisa,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </strong>

                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            Jatuh Tempo
                                        </span>

                                        <strong>

                                            @if ($item->jatuh_tempo)

                                                {{ \Carbon\Carbon::parse(
                                                    $item->jatuh_tempo
                                                )->format('d-m-Y') }}

                                            @else

                                                Tidak ditentukan

                                            @endif

                                        </strong>

                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            Status
                                        </span>

                                        <strong>

                                            <span class="status {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>

                                        </strong>

                                    </div>


                                    @if (
                                        $sudahDibayar > 0 &&
                                        $sisa > 0
                                    )

                                        <div class="detail-payment-info">

                                            <strong>
                                                Pembayaran Sebagian
                                            </strong>

                                            <p>
                                                Tagihan sudah dibayar sebesar
                                                Rp {{ number_format(
                                                    $sudahDibayar,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}.
                                                Sisa pembayaran:
                                                Rp {{ number_format(
                                                    $sisa,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}.
                                            </p>

                                        </div>

                                    @elseif ($sedangDiproses)

                                        <div class="detail-payment-info">

                                            <strong>
                                                Pembayaran Sedang Diproses
                                            </strong>

                                            <p>
                                                Pembayaran kamu sudah dikirim
                                                dan sedang menunggu persetujuan
                                                admin.
                                            </p>

                                        </div>

                                    @elseif (
                                        $ditolak &&
                                        $sudahDibayar <= 0
                                    )

                                        <div class="detail-payment-info detail-info-danger">

                                            <strong>
                                                Pembayaran Ditolak
                                            </strong>

                                            <p>
                                                Pembayaran sebelumnya ditolak.
                                                Silakan lakukan pembayaran kembali
                                                sesuai sisa tagihan.
                                            </p>

                                        </div>

                                    @endif

                                </div>


                                <div class="detail-pembayaran-right">

                                    <div class="detail-pembayaran-right-title">
                                        Ringkasan Tagihan
                                    </div>


                                    <div class="detail-summary-box">

                                        <span>
                                            Total Tagihan
                                        </span>

                                        <strong>
                                            Rp {{ number_format(
                                                $item->nominal,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </strong>

                                    </div>


                                    <div class="detail-summary-box">

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


                                    <div class="detail-summary-box detail-summary-sisa">

                                        <span>
                                            Sisa Tagihan
                                        </span>

                                        <strong>
                                            Rp {{ number_format(
                                                $sisa,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
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

                                            <strong>
                                                Pembayaran Lunas
                                            </strong>

                                            <p>
                                                Seluruh tagihan ini sudah
                                                dibayarkan dan tidak memiliki
                                                sisa pembayaran.
                                            </p>

                                        </div>

                                    @elseif ($sedangDiproses)

                                        <div class="detail-payment-info">

                                            <strong>
                                                Pembayaran Sedang Diproses
                                            </strong>

                                            <p>
                                                Pembayaran sudah dikirim dan
                                                sedang menunggu persetujuan admin.
                                            </p>

                                        </div>

                                    @elseif ($ditolak)

                                        <div class="detail-payment-info detail-info-danger">

                                            <strong>
                                                Pembayaran Ditolak
                                            </strong>

                                            <p>
                                                Pembayaran sebelumnya ditolak.
                                                Silakan melakukan pembayaran kembali.
                                            </p>

                                        </div>

                                    @else

                                        <div class="detail-payment-info">

                                            <strong>
                                                Belum Lunas
                                            </strong>

                                            <p>
                                                Silakan melakukan pembayaran
                                                sesuai dengan sisa tagihan.
                                            </p>

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
                                $totalDibayarTagihan >=
                                $totalTagihanPembayaran;

                        @endphp


                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>

                                @if ($item->tanggal_kirim)

                                    {{ \Carbon\Carbon::parse(
                                        $item->tanggal_kirim
                                    )->format('d-m-Y') }}

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
                                    Rp {{ number_format(
                                        $item->nominal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>

                            </td>


                            <td>

                                @if ($item->metode === 'transfer')

                                    Transfer Bank

                                @elseif ($item->metode === 'qris')

                                    QRIS

                                @else

                                    {{ $item->metode ?? '-' }}

                                @endif

                            </td>


                            <td>

                                @if ($item->status === 'menunggu')

                                    <span class="status status-menunggu">
                                        Menunggu
                                    </span>


                                @elseif ($item->status === 'ditolak')

                                    <span class="status status-ditolak">
                                        Ditolak
                                    </span>


                                @elseif (
                                    in_array(
                                        $item->status,
                                        ['dibayar', 'disetujui']
                                    )
                                )

                                    @if ($tagihanLunas)

                                        <span class="status status-lunas">
                                            Disetujui &amp; Lunas
                                        </span>

                                    @else

                                        <span class="status status-lunas">
                                            Disetujui
                                        </span>

                                    @endif


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
                                    !
                                </button>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="empty"
                            >

                                <div class="empty-payment">

                                    <strong>
                                        Belum ada riwayat pembayaran.
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
                        $totalDibayarTagihan >=
                        $totalTagihanPembayaran;

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


                        {{-- =================================================
                             HEADER MODAL
                        ================================================== --}}

                        <div class="payment-detail-header">

                            <div class="payment-detail-header-left">

                                <div class="payment-detail-icon">

                                    <svg
                                        width="25"
                                        height="25"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                    >

                                        <path
                                            d="M6 2H14L19 7V22H6C4.89543 22 4 21.1046 4 20V4C4 2.89543 4.89543 2 6 2Z"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linejoin="round"
                                        />

                                        <path
                                            d="M14 2V8H20"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linejoin="round"
                                        />

                                        <path
                                            d="M8 12H16"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />

                                        <path
                                            d="M8 16H16"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />

                                    </svg>

                                </div>


                                <div>

                                    <h3 id="payment-detail-title-{{ $item->id }}">
                                        Detail Pembayaran
                                    </h3>

                                    <p>
                                        Informasi lengkap transaksi pembayaran
                                    </p>

                                </div>

                            </div>


                            <button
                                type="button"
                                class="detail-modal-close"
                                onclick="closeDetailPembayaran({{ $item->id }})"
                                aria-label="Tutup"
                            >
                                ×
                            </button>

                        </div>


                        {{-- =================================================
                             BODY
                        ================================================== --}}

                        <div class="payment-detail-body">


                            <div class="payment-detail-layout">


                                {{-- =================================================
                                     KOLOM KIRI
                                ================================================== --}}

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
                                                {{ $item->tagihan->siswa->nama ?? $siswa->nama }}
                                            </div>

                                        </div>


                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                NIS
                                            </div>

                                            <div class="payment-detail-value">
                                                {{ $item->tagihan->siswa->nis ?? $siswa->nis }}
                                            </div>

                                        </div>


                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Kelas
                                            </div>

                                            <div class="payment-detail-value">
                                                {{ $item->tagihan->siswa->kelas->nama_kelas ?? $siswa->kelas->nama_kelas ?? '-' }}
                                            </div>

                                        </div>


                                        <div class="payment-detail-row">

                                            <div class="payment-detail-label">
                                                Nominal Pembayaran
                                            </div>

                                            <div class="payment-detail-value payment-amount">
                                                Rp {{ number_format(
                                                    $item->nominal,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
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

                                                    {{ \Carbon\Carbon::parse(
                                                        $item->tanggal_kirim
                                                    )->format('d-m-Y H:i') }}

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

                                                    {{ \Carbon\Carbon::parse(
                                                        $item->tanggal_disetujui
                                                    )->format('d-m-Y H:i') }}

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


                                                @elseif (
                                                    in_array(
                                                        $item->status,
                                                        ['dibayar', 'disetujui']
                                                    )
                                                )

                                                    @if ($tagihanLunas)

                                                        <span class="payment-status-badge payment-status-success">
                                                            Disetujui &amp; Lunas
                                                        </span>

                                                    @else

                                                        <span class="payment-status-badge payment-status-success">
                                                            Disetujui
                                                        </span>

                                                    @endif


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

                                                Rp {{ number_format(
                                                    $sisaTagihanPembayaran,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}

                                            </div>

                                        </div>


                                        {{-- ==========================================
                                             INFO STATUS
                                        =========================================== --}}

                                        @if (
                                            in_array(
                                                $item->status,
                                                ['dibayar', 'disetujui']
                                            ) &&
                                            $tagihanLunas
                                        )

                                            <div class="payment-info-box payment-info-success">

                                                <div class="payment-info-icon">
                                                    ✓
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
                                            in_array(
                                                $item->status,
                                                ['dibayar', 'disetujui']
                                            ) &&
                                            !$tagihanLunas
                                        )

                                            <div class="payment-info-box payment-info-success">

                                                <div class="payment-info-icon">
                                                    ✓
                                                </div>

                                                <div>

                                                    <strong>
                                                        Pembayaran Berhasil
                                                    </strong>

                                                    <p>
                                                        Pembayaran telah disetujui
                                                        oleh admin.

                                                        Sisa tagihan:
                                                        Rp {{ number_format(
                                                            $sisaTagihanPembayaran,
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) }}.
                                                    </p>

                                                </div>

                                            </div>


                                        @elseif ($item->status === 'menunggu')

                                            <div class="payment-info-box payment-info-pending">

                                                <div class="payment-info-icon">
                                                    !
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
                                                    !
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


                                        {{-- ==========================================
                                             CATATAN ADMIN
                                        =========================================== --}}

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


                                {{-- =================================================
                                     KOLOM KANAN
                                ================================================== --}}

                                <div class="payment-detail-right">


                                    {{-- ==========================================
                                         BUKTI PEMBAYARAN
                                    =========================================== --}}

                                    <div class="payment-proof-card">

                                        <div class="payment-proof-header">

                                            <div class="payment-proof-header-icon">

                                                <svg
                                                    width="21"
                                                    height="21"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                >

                                                    <path
                                                        d="M4 5C4 3.89543 4.89543 3 6 3H18C19.1046 3 20 3.89543 20 5V19C20 20.1046 19.1046 21 18 21H6C4.89543 21 4 20.1046 4 19V5Z"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                    />

                                                    <path
                                                        d="M8 15L11 12L13 14L15 12L18 15"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />

                                                </svg>

                                            </div>


                                            <div>

                                                <h4>
                                                    Bukti Pembayaran
                                                </h4>

                                                <p>
                                                    Dokumen atau foto bukti transaksi
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


                                                {{-- LIHAT BUKTI --}}

                                                <a
                                                    href="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="btn-proof btn-proof-view"
                                                >

                                                    <svg
                                                        width="17"
                                                        height="17"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                    >

                                                        <path
                                                            d="M2.5 12C4.2 7.8 7.8 5 12 5C16.2 5 19.8 7.8 21.5 12C19.8 16.2 16.2 19 12 19C7.8 19 4.2 16.2 2.5 12Z"
                                                            stroke="currentColor"
                                                            stroke-width="1.8"
                                                        />

                                                        <circle
                                                            cx="12"
                                                            cy="12"
                                                            r="3"
                                                            stroke="currentColor"
                                                            stroke-width="1.8"
                                                        />

                                                    </svg>

                                                    Lihat Bukti

                                                </a>


                                                {{-- DOWNLOAD GAMBAR --}}

                                                <a
                                                    href="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                                    download="bukti-pembayaran-{{ $item->id }}"
                                                    class="btn-proof btn-proof-download"
                                                >

                                                    <svg
                                                        width="17"
                                                        height="17"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                    >

                                                        <path
                                                            d="M12 3V15"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                        />

                                                        <path
                                                            d="M7 10L12 15L17 10"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        />

                                                        <path
                                                            d="M5 21H19"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                        />

                                                    </svg>

                                                    Unduh Bukti

                                                </a>


                                                {{-- DOWNLOAD PDF --}}

                                                <a
                                                    href="{{ route(
                                                        'orangtua.pembayaran.download',
                                                        ['pembayaran' => $item->id]
                                                    ) }}"
                                                    class="btn-proof btn-proof-pdf"
                                                >

                                                    <svg
                                                        width="17"
                                                        height="17"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                    >

                                                        <path
                                                            d="M6 2H14L19 7V22H6C4.89543 22 4 21.1046 4 20V4C4 2.89543 4.89543 2 6 2Z"
                                                            stroke="currentColor"
                                                            stroke-width="1.8"
                                                            stroke-linejoin="round"
                                                        />

                                                        <path
                                                            d="M14 2V8H20"
                                                            stroke="currentColor"
                                                            stroke-width="1.8"
                                                        />

                                                    </svg>

                                                    Unduh Detail

                                                </a>

                                            </div>


                                        @else

                                            <div class="payment-proof-empty">

                                                <div class="payment-proof-empty-icon">
                                                    📄
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


                                    {{-- ==========================================
                                         RINGKASAN
                                    =========================================== --}}

                                    <div class="payment-side-summary">

                                        <div class="payment-side-summary-title">
                                            Ringkasan Pembayaran
                                        </div>


                                        <div class="payment-side-summary-row">

                                            <span>
                                                Total Tagihan
                                            </span>

                                            <strong>
                                                Rp {{ number_format(
                                                    $totalTagihanPembayaran,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                                            </strong>

                                        </div>


                                        <div class="payment-side-summary-row">

                                            <span>
                                                Total Dibayar
                                            </span>

                                            <strong>
                                                Rp {{ number_format(
                                                    $totalDibayarTagihan,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                                            </strong>

                                        </div>


                                        <div class="payment-side-summary-row payment-side-summary-remaining">

                                            <span>
                                                Sisa
                                            </span>

                                            <strong>
                                                Rp {{ number_format(
                                                    $sisaTagihanPembayaran,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                             FOOTER
                        ================================================== --}}

                        <div class="detail-modal-footer">

                            <button
                                type="button"
                                class="btn-detail-tutup"
                                onclick="closeDetailPembayaran({{ $item->id }})"
                            >
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
     * TAB
     * =========================================================
     */

    function showPaymentSection(section, button) {

        document
            .querySelectorAll('.payment-section')
            .forEach(function(element) {

                element.classList.remove('active');

            });


        document
            .querySelectorAll('.payment-tab')
            .forEach(function(element) {

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


        document.body.classList.add(
            'modal-open'
        );

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


        document.body.classList.remove(
            'modal-open'
        );

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


        document.body.classList.add(
            'modal-open'
        );

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


        document.body.classList.remove(
            'modal-open'
        );

    }


    /*
     * =========================================================
     * ESCAPE
     * =========================================================
     */

    document.addEventListener(
        'keydown',
        function(event) {

            if (event.key !== 'Escape') {
                return;
            }


            document
                .querySelectorAll('.detail-modal.show')
                .forEach(function(modal) {

                    modal.classList.remove(
                        'show'
                    );


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

</script>

@endsection