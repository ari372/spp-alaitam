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
        Selamat Datang, Admin 👋
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
            👨‍🎓
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
            👨‍👩‍👧
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
            🧾
        </div>

        <div class="stat-title">
            Total Tagihan
        </div>

        <div class="stat-value">

            Rp
            {{ number_format($totalTagihan, 0, ',', '.') }}

        </div>

    </div>


    {{-- TOTAL PEMBAYARAN --}}

    <div class="stat-card">

        <div class="stat-icon">
            💰
        </div>

        <div class="stat-title">
            Total Pembayaran
        </div>

        <div class="stat-value">

            Rp
            {{ number_format($totalPembayaran, 0, ',', '.') }}

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
            🟢 Lunas
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
            🟡 Belum Lunas
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
            🔴 Terlambat
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
            📊 Grafik Pembayaran
        </h3>

        <div class="chart-container">

            <canvas id="paymentChart"></canvas>

        </div>

    </div>


    {{-- MENUNGGU PERSETUJUAN --}}

    <div class="card">

        <h3>
            🔔 Menunggu Persetujuan
        </h3>

        <div class="approval-number">
            {{ $menungguPersetujuan }}
        </div>

        <div class="approval-text">
            pembayaran menunggu persetujuan admin.
        </div>

        <a
            href="{{ route('admin.pembayaran.notifikasi') }}"
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
        📋 Ringkasan Tagihan
    </h3>


    <div class="summary-item">

        <span class="summary-label">
            Total Tagihan
        </span>

        <span class="summary-value">

            Rp
            {{ number_format($totalTagihan, 0, ',', '.') }}

        </span>

    </div>


    <div class="summary-item">

        <span class="summary-label">
            Total Dibayar
        </span>

        <span class="summary-value">

            Rp
            {{ number_format($totalPembayaran, 0, ',', '.') }}

        </span>

    </div>


    <div class="summary-item">

        <span class="summary-label">
            Sisa Tagihan
        </span>

        <span class="summary-value">

            Rp
            {{ number_format($sisaTagihan, 0, ',', '.') }}

        </span>

    </div>

</div>


{{-- =========================================================
     PEMBAYARAN TERBARU
========================================================= --}}

<div class="card payment-card">

    <h3>
        💳 Pembayaran Terbaru
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

                            Rp
                            {{ number_format(
                                $item->nominal,
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>

                        <td>

                            @if($item->status === 'disetujui' || $item->status === 'dibayar')

                                <span class="badge badge-success">
                                    Disetujui
                                </span>

                            @elseif($item->status === 'menunggu')

                                <span class="badge badge-warning">
                                    Menunggu
                                </span>

                            @else

                                <span class="badge badge-danger">
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
                                text-align:center;
                                padding:30px;
                                color:#777;
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
        ⚡ Aksi Cepat
    </h3>

    <div class="quick-actions">

        <a
            href="{{ route('admin.siswa.create') }}"
            class="quick-action"
        >
            ➕ Tambah Siswa
        </a>


        <a
            href="{{ route('admin.tagihan.create') }}"
            class="quick-action"
        >
            🧾 Buat Tagihan
        </a>


        <a
            href="{{ route('admin.laporan.index') }}"
            class="quick-action"
        >
            📊 Laporan
        </a>

    </div>

</div>


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