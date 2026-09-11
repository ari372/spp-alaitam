@extends('layouts.admin')

@section('title', 'Laporan')

@section('page-title', 'Laporan')

@push('styles')
    @vite('resources/css/admin/laporan.css')
@endpush

@section('content')

<div class="laporan-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="laporan-header">

        <div>
            <h2>
                Laporan Pembayaran
            </h2>

            <p>
                Melihat dan mencetak laporan pembayaran siswa
            </p>
        </div>

    </div>


    {{-- =====================================================
         PESAN SUCCESS
    ====================================================== --}}
    @if(session('success'))

        <div class="laporan-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- =====================================================
         PESAN ERROR
    ====================================================== --}}
    @if(session('error'))

        <div class="laporan-error">
            {{ session('error') }}
        </div>

    @endif


    {{-- =====================================================
         FILTER LAPORAN
    ====================================================== --}}
    <div class="laporan-filter">

        <h3>
            Filter Laporan
        </h3>

        <form
            method="GET"
            action="{{ route('admin.laporan.index') }}"
        >

            {{-- ================= BULAN ================= --}}
            <div class="laporan-form-group">

                <label for="bulan">
                    Bulan
                </label>

                <select
                    name="bulan"
                    id="bulan"
                >

                    <option value="">
                        Semua Bulan
                    </option>

                    @foreach([
                        'Januari',
                        'Februari',
                        'Maret',
                        'April',
                        'Mei',
                        'Juni',
                        'Juli',
                        'Agustus',
                        'September',
                        'Oktober',
                        'November',
                        'Desember'
                    ] as $namaBulan)

                        <option
                            value="{{ $namaBulan }}"
                            {{ request('bulan') == $namaBulan ? 'selected' : '' }}
                        >
                            {{ $namaBulan }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ================= TAHUN ================= --}}
            <div class="laporan-form-group">

                <label for="tahun">
                    Tahun
                </label>

                <input
                    type="number"
                    name="tahun"
                    id="tahun"
                    value="{{ request('tahun') }}"
                    min="2000"
                    max="2100"
                    placeholder="Semua Tahun"
                >

            </div>


            {{-- ================= BUTTON ================= --}}
            <div class="laporan-actions">

                {{-- TAMPILKAN --}}
                <button
                    type="submit"
                    class="btn-laporan btn-laporan-green"
                >
                    Tampilkan
                </button>


                {{-- RESET --}}
                <a
                    href="{{ route('admin.laporan.index') }}"
                    class="btn-laporan btn-laporan-reset"
                >
                    Reset
                </a>


                {{-- PDF --}}
                <a
                    href="{{ route('admin.laporan.pdf', [
                        'bulan' => request('bulan'),
                        'tahun' => request('tahun')
                    ]) }}"
                    target="_blank"
                    class="btn-laporan btn-laporan-print"
                >
                    Cetak PDF
                </a>


                {{-- EXCEL --}}
                <a
                    href="{{ route('admin.laporan.excel', [
                        'bulan' => request('bulan'),
                        'tahun' => request('tahun')
                    ]) }}"
                    class="btn-laporan btn-laporan-excel"
                >
                    Export Excel
                </a>

            </div>

        </form>

    </div>


    {{-- =====================================================
         RINGKASAN
    ====================================================== --}}
    <div class="laporan-summary">


        {{-- TOTAL PEMBAYARAN --}}
        <div class="laporan-summary-card">

            <h4>
                Total Pembayaran
            </h4>

            <strong>
                Rp {{ number_format(
                    $totalPembayaran ?? 0,
                    0,
                    ',',
                    '.'
                ) }}
            </strong>

        </div>


        {{-- TOTAL TAGIHAN --}}
        <div class="laporan-summary-card">

            <h4>
                Total Tagihan
            </h4>

            <strong>
                Rp {{ number_format(
                    $totalTagihan ?? 0,
                    0,
                    ',',
                    '.'
                ) }}
            </strong>

        </div>


        {{-- SISA TAGIHAN --}}
        <div class="laporan-summary-card">

            <h4>
                Sisa Tagihan
            </h4>

            <strong>
                Rp {{ number_format(
                    $sisaTagihan ?? 0,
                    0,
                    ',',
                    '.'
                ) }}
            </strong>

        </div>

    </div>


    {{-- =====================================================
         DATA PEMBAYARAN
    ====================================================== --}}
    <div class="laporan-card">


        {{-- ================= HEADER CARD ================= --}}
        <div class="laporan-card-header">

            <div>

                <h3>
                    Data Pembayaran
                </h3>

                <p>
                    Daftar pembayaran siswa yang tercatat dalam sistem.
                </p>

            </div>


            <div class="laporan-total-data">

                {{ $pembayaran->count() }}

                Data

            </div>

        </div>


        {{-- =====================================================
             TABLE
        ====================================================== --}}
        <div class="laporan-table-wrapper">

            <table class="laporan-table">

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Siswa
                        </th>

                        <th>
                            NIS
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th>
                            Bulan
                        </th>

                        <th>
                            Tahun
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

                        <th>
                            Tanggal Bayar
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($pembayaran as $index => $item)

                        <tr>


                            {{-- =================================================
                                 NO
                            ================================================== --}}
                            <td class="text-center">

                                {{ $index + 1 }}

                            </td>


                            {{-- =================================================
                                 SISWA
                            ================================================== --}}
                            <td>

                                <div class="siswa-cell">

                                    <strong>
                                        {{ $item->tagihan?->siswa?->nama ?? '-' }}
                                    </strong>

                                </div>

                            </td>


                            {{-- =================================================
                                 NIS
                            ================================================== --}}
                            <td>

                                {{ $item->tagihan?->siswa?->nis ?? '-' }}

                            </td>


                            {{-- =================================================
                                 KELAS
                            ================================================== --}}
                            <td>

                                {{ $item->tagihan?->siswa?->kelas?->nama_kelas ?? '-' }}

                            </td>


                            {{-- =================================================
                                 BULAN
                                 Menggunakan tanggal pembayaran
                            ================================================== --}}
                            <td>

                                @php

                                    $bulanIndonesia = [
                                        1  => 'Januari',
                                        2  => 'Februari',
                                        3  => 'Maret',
                                        4  => 'April',
                                        5  => 'Mei',
                                        6  => 'Juni',
                                        7  => 'Juli',
                                        8  => 'Agustus',
                                        9  => 'September',
                                        10 => 'Oktober',
                                        11 => 'November',
                                        12 => 'Desember',
                                    ];

                                @endphp


                                @if($item->tanggal_disetujui)

                                    {{ $bulanIndonesia[
                                        $item->tanggal_disetujui->month
                                    ] ?? '-' }}

                                @elseif($item->tanggal_kirim)

                                    {{ $bulanIndonesia[
                                        $item->tanggal_kirim->month
                                    ] ?? '-' }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- =================================================
                                 TAHUN
                            ================================================== --}}
                            <td>

                                @if($item->tanggal_disetujui)

                                    {{ $item->tanggal_disetujui->format('Y') }}

                                @elseif($item->tanggal_kirim)

                                    {{ $item->tanggal_kirim->format('Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- =================================================
                                 NOMINAL
                            ================================================== --}}
                            <td>

                                <strong class="nominal">

                                    Rp {{ number_format(
                                        $item->nominal ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </td>


                            {{-- =================================================
                                 METODE
                            ================================================== --}}
                            <td>

                                <span class="metode-badge">

                                    {{ strtoupper(
                                        $item->metode ?? '-'
                                    ) }}

                                </span>

                            </td>


                            {{-- =================================================
                                 STATUS
                            ================================================== --}}
                            <td>

                                @if($item->status === 'dibayar')

                                    <span class="status-lunas">
                                        Dibayar
                                    </span>

                                @elseif($item->status === 'ditolak')

                                    <span class="status-belum">
                                        Ditolak
                                    </span>

                                @elseif($item->status === 'menunggu')

                                    <span class="status-menunggu">
                                        Menunggu
                                    </span>

                                @else

                                    <span class="status-belum">
                                        {{ $item->status ?? '-' }}
                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 TANGGAL BAYAR
                            ================================================== --}}
                            <td>

                                @if($item->tanggal_disetujui)

                                    {{ $item->tanggal_disetujui->format(
                                        'd-m-Y H:i'
                                    ) }}

                                @elseif($item->tanggal_kirim)

                                    {{ $item->tanggal_kirim->format(
                                        'd-m-Y H:i'
                                    ) }}

                                @else

                                    -

                                @endif

                            </td>

                        </tr>


                    @empty

                        {{-- =================================================
                             DATA KOSONG
                        ================================================== --}}
                        <tr>

                            <td
                                colspan="10"
                                class="laporan-empty"
                            >

                                <div class="empty-icon">
                                    📄
                                </div>

                                <strong>
                                    Belum ada data pembayaran
                                </strong>

                                <p>
                                    Data pembayaran akan muncul
                                    setelah pembayaran tercatat.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection