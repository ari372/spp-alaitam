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
         FILTER
    ====================================================== --}}

    <div class="laporan-filter">

        <h3>
            Filter Laporan
        </h3>


        <form method="GET">

            {{-- BULAN --}}

            <div class="laporan-form-group">

                <label>
                    Bulan
                </label>

                <select name="bulan">

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


            {{-- TAHUN --}}

            <div class="laporan-form-group">

                <label>
                    Tahun
                </label>

                <input
                    type="number"
                    name="tahun"
                    value="{{ request('tahun', date('Y')) }}"
                    min="2000"
                    max="2100"
                >

            </div>


<div class="laporan-actions">

    {{-- TAMPILKAN --}}
    <button
        type="submit"
        class="btn-laporan btn-laporan-green"
    >
        Tampilkan
    </button>

    {{-- CETAK PDF --}}
    <a
        href="{{ route('admin.laporan.pdf', [
            'bulan' => request('bulan'),
            'tahun' => request('tahun', date('Y'))
        ]) }}"
        target="_blank"
        class="btn-laporan btn-laporan-print"
    >
        Cetak PDF
    </a>

    {{-- EXPORT EXCEL --}}
    <a
        href="{{ route('admin.laporan.excel', [
            'bulan' => request('bulan'),
            'tahun' => request('tahun', date('Y'))
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

        <h3>
            Data Pembayaran
        </h3>


        <div class="laporan-table-wrapper">

            <table class="laporan-table">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>Siswa</th>

                        <th>Kelas</th>

                        <th>Bulan</th>

                        <th>Tahun</th>

                        <th>Nominal</th>

                        <th>Metode</th>

                        <th>Status</th>

                        <th>Tanggal Bayar</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($pembayaran as $index => $item)

                        <tr>

                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>


                            <td>
                                {{ $item->siswa->nama ?? '-' }}
                            </td>


                            <td>
                                {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                            </td>


                            <td>
                                {{ $item->bulan ?? '-' }}
                            </td>


                            <td>
                                {{ $item->tahun ?? '-' }}
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
                                {{ $item->metode_pembayaran ?? '-' }}
                            </td>


                            <td>

                                @if(($item->status ?? '') == 'disetujui')

                                    <span class="status-lunas">
                                        Disetujui
                                    </span>

                                @else

                                    <span class="status-belum">
                                        {{ $item->status ?? 'Belum' }}
                                    </span>

                                @endif

                            </td>


                            <td>
                                {{ $item->tanggal_bayar ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="laporan-empty"
                            >
                                Belum ada data pembayaran.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection