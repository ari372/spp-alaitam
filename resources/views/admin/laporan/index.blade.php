@extends('layouts.admin')

@section('title', 'Laporan')

@push('styles')
    @vite('resources/css/admin/laporan.css')
@endpush

@section('content')

<div class="laporan-page">

    {{-- HEADER HALAMAN --}}
    <div class="laporan-header">
        <div class="laporan-header-content">
            <div class="laporan-header-icon">
                <i data-lucide="bar-chart-3"></i>
            </div>

            <div>
                <h1>Laporan</h1>
                <p>Daftar laporan pembayaran siswa SMP Plus Al-I'tam</p>
            </div>
        </div>
    </div>


    {{-- FILTER --}}
    <div class="laporan-filter-card">

        <div class="laporan-filter-header">
            <div class="laporan-filter-title">
                <div class="laporan-filter-icon">
                    <i data-lucide="filter"></i>
                </div>

                <div>
                    <h3>Filter Laporan</h3>
                    <p>Filter data pembayaran berdasarkan periode.</p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.laporan.index') }}" class="laporan-filter-form">

            <div class="laporan-form-group">
                <label for="bulan">Bulan</label>

                <select name="bulan" id="bulan">
                    <option value="">Semua Bulan</option>

                    @foreach([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ] as $nomor => $nama)
                        <option value="{{ $nomor }}"
                            {{ request('bulan') == $nomor ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="laporan-form-group">
                <label for="tahun">Tahun</label>

                <select name="tahun" id="tahun">
                    <option value="">Semua Tahun</option>

                    @for($tahun = date('Y'); $tahun >= date('Y') - 5; $tahun--)
                        <option value="{{ $tahun }}"
                            {{ request('tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endfor
                </select>
            </div>


            <div class="laporan-filter-actions">

                <button type="submit" class="btn-laporan btn-laporan-green">
                    <i data-lucide="search"></i>
                    <span>Tampilkan</span>
                </button>

                <a href="{{ route('admin.laporan.index') }}"
                   class="btn-laporan btn-laporan-reset">
                    <i data-lucide="rotate-ccw"></i>
                    <span>Reset</span>
                </a>

                <a href="{{ route('admin.laporan.pdf', request()->query()) }}"
                   class="btn-laporan btn-laporan-print"
                   target="_blank">
                    <i data-lucide="file-text"></i>
                    <span>Cetak PDF</span>
                </a>

                <a href="{{ route('admin.laporan.excel', request()->query()) }}"
                   class="btn-laporan btn-laporan-excel">
                    <i data-lucide="file-spreadsheet"></i>
                    <span>Export Excel</span>
                </a>

            </div>

        </form>
    </div>


    {{-- RINGKASAN --}}
    <div class="laporan-summary">

        <div class="laporan-summary-card">
            <div class="laporan-summary-icon">
                <i data-lucide="wallet"></i>
            </div>

            <div class="laporan-summary-content">
                <span>Total Pembayaran</span>
                <strong>
                    Rp {{ number_format($totalPembayaran ?? 0, 0, ',', '.') }}
                </strong>
            </div>
        </div>


        <div class="laporan-summary-card">
            <div class="laporan-summary-icon">
                <i data-lucide="receipt"></i>
            </div>

            <div class="laporan-summary-content">
                <span>Total Tagihan</span>
                <strong>
                    Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}
                </strong>
            </div>
        </div>


        <div class="laporan-summary-card">
            <div class="laporan-summary-icon">
                <i data-lucide="circle-dollar-sign"></i>
            </div>

            <div class="laporan-summary-content">
                <span>Sisa Tagihan</span>
                <strong>
                    Rp {{ number_format($sisaTagihan ?? 0, 0, ',', '.') }}
                </strong>
            </div>
        </div>

    </div>


    {{-- DATA PEMBAYARAN --}}
    <div class="laporan-card">

        {{-- CARD HEADER --}}
        <div class="laporan-card-header">

            <div class="laporan-card-title">

                <div class="laporan-card-icon">
                    <i data-lucide="table-2"></i>
                </div>

                <div>
                    <h2>Data Pembayaran</h2>
                    <p>
                        Daftar pembayaran siswa yang tercatat dalam sistem.
                    </p>
                </div>

            </div>


            <div class="laporan-total-data">
                <i data-lucide="file-check-2"></i>
                <span>{{ $pembayaran->count() }} Data</span>
            </div>

        </div>


        {{-- TABLE --}}
        <div class="laporan-table-wrapper">

            <table class="laporan-table">

                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th class="col-siswa">Siswa</th>
                        <th class="col-nis">NIS</th>
                        <th class="col-kelas">Kelas</th>
                        <th class="col-bulan">Bulan</th>
                        <th class="col-tahun">Tahun</th>
                        <th class="col-nominal">Nominal</th>
                        <th class="col-metode">Metode</th>
                        <th class="col-status">Status</th>
                        <th class="col-tanggal">Tanggal Bayar</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>


                <tbody>

                    @forelse($pembayaran as $index => $item)

                        @php
                            $siswa = $item->tagihan?->siswa;
                            $kelas = $siswa?->kelas;
                            $namaSiswa = $siswa?->nama ?? '-';

                            $initial = strtoupper(
                                substr(trim($namaSiswa), 0, 1)
                            );

                            $tanggal = $item->tanggal_disetujui
                                ?? $item->tanggal_kirim
                                ?? $item->created_at;

                            $namaBulan = $tanggal
                                ? \Carbon\Carbon::parse($tanggal)->translatedFormat('F')
                                : '-';

                            $tahunBayar = $tanggal
                                ? \Carbon\Carbon::parse($tanggal)->format('Y')
                                : '-';

                            $status = strtolower($item->status ?? '');

                            $metode = strtolower($item->metode ?? '');
                        @endphp

                        <tr>

                            {{-- NO --}}
                            <td class="col-no">
                                {{ $index + 1 }}
                            </td>


                            {{-- SISWA --}}
                            <td class="col-siswa">
                                <div class="siswa-cell">

                                    <div class="siswa-avatar">
                                        {{ $initial }}
                                    </div>

                                    <div class="siswa-info">
                                        <strong>{{ $namaSiswa }}</strong>

                                        @if($item->tagihan?->kategori)
                                            <span>
                                                {{ $item->tagihan->kategori->nama }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </td>


                            {{-- NIS --}}
                            <td class="col-nis">
                                <span class="nis-text">
                                    {{ $siswa?->nis ?? '-' }}
                                </span>
                            </td>


                            {{-- KELAS --}}
                            <td class="col-kelas">
                                <span class="kelas-badge">
                                    <i data-lucide="school"></i>
                                    {{ $kelas?->nama ?? '-' }}
                                </span>
                            </td>


                            {{-- BULAN --}}
                            <td class="col-bulan">
                                {{ $namaBulan }}
                            </td>


                            {{-- TAHUN --}}
                            <td class="col-tahun">
                                {{ $tahunBayar }}
                            </td>


                            {{-- NOMINAL --}}
                            <td class="col-nominal">
                                <strong>
                                    Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}
                                </strong>
                            </td>


                            {{-- METODE --}}
                            <td class="col-metode">

                                @if($metode === 'transfer')
                                    <span class="metode-badge metode-transfer">
                                        <i data-lucide="landmark"></i>
                                        Transfer
                                    </span>

                                @elseif($metode === 'qris')
                                    <span class="metode-badge metode-qris">
                                        <i data-lucide="qr-code"></i>
                                        QRIS
                                    </span>

                                @elseif($metode === 'cash')
                                    <span class="metode-badge metode-cash">
                                        <i data-lucide="banknote"></i>
                                        Cash
                                    </span>

                                @else
                                    <span class="metode-badge">
                                        {{ ucfirst($item->metode ?? '-') }}
                                    </span>
                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td class="col-status">

                                @if($status === 'dibayar')

                                    <span class="status-badge status-dibayar">
                                        <i data-lucide="circle-check"></i>
                                        Dibayar
                                    </span>

                                @elseif($status === 'menunggu')

                                    <span class="status-badge status-menunggu">
                                        <i data-lucide="clock-3"></i>
                                        Menunggu
                                    </span>

                                @elseif($status === 'ditolak')

                                    <span class="status-badge status-ditolak">
                                        <i data-lucide="circle-x"></i>
                                        Ditolak
                                    </span>

                                @else

                                    <span class="status-badge">
                                        {{ ucfirst($item->status ?? '-') }}
                                    </span>

                                @endif

                            </td>


                            {{-- TANGGAL --}}
                            <td class="col-tanggal">

                                @if($tanggal)
                                    <div class="tanggal-cell">
                                        <strong>
                                            {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
                                        </strong>

                                        <span>
                                            {{ \Carbon\Carbon::parse($tanggal)->format('H:i') }}
                                        </span>
                                    </div>
                                @else
                                    -
                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td class="col-aksi">

                                <div class="laporan-actions-table">

                                    <a href="{{ route('admin.laporan.show', $item->id) }}"
                                       class="laporan-action laporan-action-detail"
                                       title="Detail">

                                        <i data-lucide="info"></i>

                                    </a>


                                    <a href="{{ route('admin.laporan.edit', $item->id) }}"
                                       class="laporan-action laporan-action-edit"
                                       title="Edit">

                                        <i data-lucide="square-pen"></i>

                                    </a>


                                    <form action="{{ route('admin.laporan.destroy', $item->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pembayaran ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="laporan-action laporan-action-delete"
                                                title="Hapus">

                                            <i data-lucide="trash-2"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="11">

                                <div class="laporan-empty">

                                    <div class="laporan-empty-icon">
                                        <i data-lucide="file-x-2"></i>
                                    </div>

                                    <h3>Belum Ada Data Pembayaran</h3>

                                    <p>
                                        Belum terdapat data pembayaran yang sesuai
                                        dengan filter yang dipilih.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush

@endsection