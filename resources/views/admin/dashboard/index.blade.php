@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard Admin')

@push('styles')
    @vite('resources/css/admin/dashboard.css')
@endpush

@section('content')

{{-- =========================================================
     HEADER
========================================================= --}}

<div class="dashboard-header">

    <h2>
        Selamat Datang, Admin
    </h2>

    <p>
        Sistem Pembayaran SPP SMP Plus Al-I'tam
    </p>

</div>


{{-- =========================================================
     STATISTIK
========================================================= --}}

<div class="stat-grid">

    {{-- TOTAL SISWA --}}

    <div class="stat-card">

        <div class="stat-icon">
            <i data-lucide="users"></i>
        </div>

        <div class="stat-title">
            Total Siswa
        </div>

        <div class="stat-value">
            {{ $totalSiswa }}
        </div>

    </div>


    {{-- ORANG TUA --}}

    <div class="stat-card">

        <div class="stat-icon">
            <i data-lucide="user-round"></i>
        </div>

        <div class="stat-title">
            Orang Tua
        </div>

        <div class="stat-value">
            {{ $totalOrangTua }}
        </div>

    </div>


    {{-- TOTAL TAGIHAN --}}

    <div class="stat-card">

        <div class="stat-icon">
            <i data-lucide="receipt"></i>
        </div>

        <div class="stat-title">
            Total Tagihan
        </div>

        <div class="stat-value">
            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
        </div>

    </div>


    {{-- TOTAL PEMBAYARAN --}}

    <div class="stat-card">

        <div class="stat-icon">
            <i data-lucide="wallet"></i>
        </div>

        <div class="stat-title">
            Total Pembayaran
        </div>

        <div class="stat-value">
            Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
        </div>

    </div>

</div>


{{-- =========================================================
     STATUS PEMBAYARAN
========================================================= --}}

<div class="status-grid">


    {{-- LUNAS --}}

    <div class="status-card">

        <h4>

            <i data-lucide="circle-check"></i>

            Lunas

        </h4>

        <div class="status-content">

            <span>
                Sudah lunas
            </span>

            <span class="status-number status-lunas">
                {{ $lunas }}
            </span>

        </div>

    </div>


    {{-- BELUM LUNAS --}}

    <div class="status-card">

        <h4>

            <i data-lucide="clock-3"></i>

            Belum Lunas

        </h4>

        <div class="status-content">

            <span>
                Belum melunasi
            </span>

            <span class="status-number status-belum">
                {{ $belumLunas }}
            </span>

        </div>

    </div>


    {{-- TERLAMBAT --}}

    <div class="status-card">

        <h4>

            <i data-lucide="triangle-alert"></i>

            Terlambat

        </h4>

        <div class="status-content">

            <span>
                Melewati jatuh tempo
            </span>

            <span class="status-number status-terlambat">
                {{ $terlambat }}
            </span>

        </div>

    </div>

</div>


{{-- =========================================================
     GRAFIK + PERSETUJUAN
========================================================= --}}

<div class="dashboard-grid">


    {{-- GRAFIK --}}

    <div class="card">

        <h3>

            <i data-lucide="chart-column"></i>

            Grafik Pembayaran

        </h3>

        <div class="chart-container">

            <canvas id="paymentChart"></canvas>

        </div>

    </div>


    {{-- MENUNGGU PERSETUJUAN --}}

    <div class="card">

        <h3>

            <i data-lucide="bell"></i>

            Menunggu Persetujuan

        </h3>

        <div class="approval-number">
            {{ $menungguPersetujuan }}
        </div>

        <div class="approval-text">
            Pembayaran menunggu persetujuan admin.
        </div>

        <a
            href="{{ route('admin.pembayaran.index') }}"
            class="btn btn-green"
        >
            Lihat Pembayaran
        </a>

    </div>

</div>


{{-- =========================================================
     RINGKASAN TAGIHAN
========================================================= --}}

<div class="card summary-card">

    <h3>

        <i data-lucide="clipboard-list"></i>

        Ringkasan Tagihan

    </h3>


    <div class="summary-item">

        <span class="summary-label">
            Total Tagihan
        </span>

        <span class="summary-value">
            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
        </span>

    </div>


    <div class="summary-item">

        <span class="summary-label">
            Total Dibayar
        </span>

        <span class="summary-value">
            Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
        </span>

    </div>


    <div class="summary-item">

        <span class="summary-label">
            Sisa Tagihan
        </span>

        <span class="summary-value">
            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
        </span>

    </div>

</div>


{{-- =========================================================
     PEMBAYARAN TERBARU
========================================================= --}}

<div class="card payment-card">

    <h3>

        <i data-lucide="credit-card"></i>

        Pembayaran Terbaru

    </h3>


    <div class="table-wrapper">

        <table class="payment-table">

            <thead>

                <tr>

                    <th>
                        Siswa
                    </th>

                    <th>
                        Kategori
                    </th>

                    <th>
                        Nominal
                    </th>

                    <th>
                        Status
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($pembayaranTerbaru as $item)

                    <tr>

                        <td>
                            {{ $item->tagihan->siswa->nama ?? '-' }}
                        </td>

                        <td>
                            {{ $item->tagihan->kategori->nama ?? '-' }}
                        </td>

                        <td>
                            Rp {{ number_format(
                                $item->nominal ?? 0,
                                0,
                                ',',
                                '.'
                            ) }}
                        </td>

                        <td>

                            @if(
                                $item->status === 'disetujui' ||
                                $item->status === 'dibayar'
                            )

                                <span class="badge badge-success">

                                    <i data-lucide="circle-check"></i>

                                    Disetujui

                                </span>

                            @elseif($item->status === 'menunggu')

                                <span class="badge badge-warning">

                                    <i data-lucide="clock-3"></i>

                                    Menunggu

                                </span>

                            @else

                                <span class="badge badge-danger">

                                    <i data-lucide="circle-x"></i>

                                    Ditolak

                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="4"
                            style="
                                text-align: center;
                                padding: 30px;
                                color: #777;
                            "
                        >
                            Belum ada pembayaran.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
     AKSI CEPAT
========================================================= --}}

<div class="card quick-card">

    <h3>

        <i data-lucide="zap"></i>

        Aksi Cepat

    </h3>


    <div class="quick-actions">

        <a
            href="{{ route('admin.siswa.create') }}"
            class="quick-action"
        >

            <i data-lucide="user-plus"></i>

            Tambah Siswa

        </a>


        <a
            href="{{ route('admin.tagihan.create') }}"
            class="quick-action"
        >

            <i data-lucide="file-plus-2"></i>

            Buat Tagihan

        </a>


        <a
            href="{{ route('admin.laporan.index') }}"
            class="quick-action"
        >

            <i data-lucide="file-chart-column"></i>

            Laporan

        </a>

    </div>

</div>


{{-- =========================================================
     LUCIDE ICON
========================================================= --}}

<script src="https://unpkg.com/lucide@latest"></script>

<script>

    lucide.createIcons();

</script>


{{-- =========================================================
     CHART.JS
========================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    const chartElement =
        document.getElementById('paymentChart');

    if (chartElement) {

        new Chart(chartElement, {

            type: 'bar',

            data: {

                labels: @json($labelGrafik),

                datasets: [{

                    label: 'Pembayaran',

                    data: @json($dataGrafik),

                    borderWidth: 1

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            callback: function(value) {

                                return 'Rp ' +
                                    value.toLocaleString('id-ID');

                            }

                        }

                    }

                }

            }

        });

    }

</script>

@endsection