@extends('layouts.admin')

@section('title', 'Laporan')

@push('styles')
    @vite('resources/css/admin/laporan.css')
@endpush

@section('content')

<div class="laporan-page">

    {{-- =========================================================
        HEADER HALAMAN
    ========================================================== --}}
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


    {{-- =========================================================
        FILTER
    ========================================================== --}}
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


        <form
            method="GET"
            action="{{ route('admin.laporan.index') }}"
            class="laporan-filter-form"
        >

            {{-- BULAN --}}
            <div class="laporan-form-group">

                <label for="bulan">Bulan</label>

                <select name="bulan" id="bulan">

                    <option value="">
                        Semua Bulan
                    </option>

                    @foreach ([
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
                        12 => 'Desember',
                    ] as $nomor => $nama)

                        <option
                            value="{{ $nomor }}"
                            {{ request('bulan') == $nomor ? 'selected' : '' }}
                        >
                            {{ $nama }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- TAHUN --}}
            <div class="laporan-form-group">

                <label for="tahun">Tahun</label>

                <select name="tahun" id="tahun">

                    <option value="">
                        Semua Tahun
                    </option>

                    @for ($tahun = date('Y'); $tahun >= date('Y') - 5; $tahun--)

                        <option
                            value="{{ $tahun }}"
                            {{ request('tahun') == $tahun ? 'selected' : '' }}
                        >
                            {{ $tahun }}
                        </option>

                    @endfor

                </select>

            </div>


            {{-- ACTION FILTER --}}
            <div class="laporan-filter-actions">

                {{-- TAMPILKAN --}}
                <button
                    type="submit"
                    class="btn-laporan btn-laporan-green"
                >
                    <i data-lucide="search"></i>
                    <span>Tampilkan</span>
                </button>


                {{-- RESET --}}
                <a
                    href="{{ route('admin.laporan.index') }}"
                    class="btn-laporan btn-laporan-reset"
                >
                    <i data-lucide="rotate-ccw"></i>
                    <span>Reset</span>
                </a>


                {{-- PDF --}}
                <a
                    href="{{ route('admin.laporan.pdf', request()->query()) }}"
                    class="btn-laporan btn-laporan-print"
                    target="_blank"
                >
                    <i data-lucide="file-text"></i>
                    <span>Cetak PDF</span>
                </a>


                {{-- EXCEL --}}
                <a
                    href="{{ route('admin.laporan.excel', request()->query()) }}"
                    class="btn-laporan btn-laporan-excel"
                >
                    <i data-lucide="file-spreadsheet"></i>
                    <span>Export Excel</span>
                </a>

            </div>

        </form>

    </div>


    {{-- =========================================================
        RINGKASAN
    ========================================================== --}}
    <div class="laporan-summary">

        {{-- TOTAL PEMBAYARAN --}}
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


        {{-- TOTAL TAGIHAN --}}
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


        {{-- SISA TAGIHAN --}}
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


    {{-- =========================================================
        DATA PEMBAYARAN
    ========================================================== --}}
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

                <span>
                    {{ $pembayaran->count() }} Data
                </span>

            </div>

        </div>


        {{-- =====================================================
            TOOLBAR
        ====================================================== --}}
        <div class="laporan-table-toolbar">

            {{-- SEARCH --}}
            <div class="laporan-search">

                <i data-lucide="search"></i>

                <input
                    type="text"
                    id="searchLaporan"
                    placeholder="Cari nama siswa, NIS, kelas, metode..."
                    autocomplete="off"
                >

            </div>


            {{-- ACTION --}}
            <div class="laporan-toolbar-actions">

                <span
                    id="jumlahDipilih"
                    class="jumlah-dipilih"
                >
                    0 dipilih
                </span>


                <button
                    type="button"
                    id="btnHapusTerpilih"
                    class="btn-hapus-terpilih"
                    disabled
                >
                    <i data-lucide="trash-2"></i>

                    <span>
                        Hapus Terpilih
                    </span>

                </button>

            </div>

        </div>


        {{-- =====================================================
            TABLE
        ====================================================== --}}
        <div class="laporan-table-wrapper">

            <table class="laporan-table">

                <thead>

                    <tr>

                        {{-- CHECK ALL --}}
                        <th class="col-check">

                            <input
                                type="checkbox"
                                id="checkAll"
                                title="Pilih semua"
                            >

                        </th>


                        {{-- NO --}}
                        <th class="col-no">
                            No
                        </th>


                        {{-- SISWA --}}
                        <th class="col-siswa">
                            Siswa
                        </th>


                        {{-- NIS --}}
                        <th class="col-nis">
                            NIS
                        </th>


                        {{-- KELAS --}}
                        <th class="col-kelas">
                            Kelas
                        </th>


                        {{-- BULAN --}}
                        <th class="col-bulan">
                            Bulan
                        </th>


                        {{-- TAHUN --}}
                        <th class="col-tahun">
                            Tahun
                        </th>


                        {{-- NOMINAL --}}
                        <th class="col-nominal">
                            Nominal
                        </th>


                        {{-- METODE --}}
                        <th class="col-metode">
                            Metode
                        </th>


                        {{-- STATUS --}}
                        <th class="col-status">
                            Status
                        </th>


                        {{-- TANGGAL --}}
                        <th class="col-tanggal">
                            Tanggal Bayar
                        </th>


                        {{-- AKSI --}}
                        <th class="col-aksi">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($pembayaran as $index => $item)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | DATA SISWA
                            |--------------------------------------------------------------------------
                            */

                            $siswa = $item->tagihan?->siswa;

                            $kelas = $siswa?->kelas;

                            $namaSiswa = $siswa?->nama ?? '-';


                            /*
                            |--------------------------------------------------------------------------
                            | INITIAL
                            |--------------------------------------------------------------------------
                            */

                            $initial = strtoupper(
                                substr(
                                    trim($namaSiswa),
                                    0,
                                    1
                                )
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | TANGGAL PEMBAYARAN
                            |--------------------------------------------------------------------------
                            */

                            $tanggal =
                                $item->tanggal_disetujui
                                ?? (
                                    $item->tanggal_kirim
                                    ?? $item->created_at
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | STATUS
                            |--------------------------------------------------------------------------
                            */

                            $status = strtolower(
                                $item->status ?? ''
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | METODE
                            |--------------------------------------------------------------------------
                            */

                            $metode = strtolower(
                                $item->metode ?? ''
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | PERIODE TAGIHAN
                            |--------------------------------------------------------------------------
                            |
                            | Untuk SPP:
                            | gunakan bulan + tahun tagihan.
                            |
                            | Untuk tagihan non-SPP:
                            | gunakan tanggal pembayaran.
                            |
                            */

                            $kategoriNama = strtolower(
                                trim(
                                    $item->tagihan?->kategori?->nama ?? ''
                                )
                            );

                            $isSpp = $kategoriNama === 'spp';


                            if (
                                $isSpp &&
                                $item->tagihan?->bulan &&
                                $item->tagihan?->tahun
                            ) {

                                $namaBulan = \Carbon\Carbon::create(
                                    $item->tagihan->tahun,
                                    $item->tagihan->bulan,
                                    1
                                )->translatedFormat('F');

                                $tahunBayar = $item->tagihan->tahun;

                            } else {

                                $namaBulan = $tanggal
                                    ? \Carbon\Carbon::parse($tanggal)
                                        ->translatedFormat('F')
                                    : '-';

                                $tahunBayar = $tanggal
                                    ? \Carbon\Carbon::parse($tanggal)
                                        ->format('Y')
                                    : '-';

                            }

                        @endphp


                        <tr>

                            {{-- CHECKBOX --}}
                            <td class="col-check">

                                <input
                                    type="checkbox"
                                    class="payment-checkbox"
                                    name="payment_ids[]"
                                    value="{{ $item->id }}"
                                >

                            </td>


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

                                        <strong>
                                            {{ $namaSiswa }}
                                        </strong>

                                        @if ($item->tagihan?->kategori)

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

                                    {{ $kelas?->nama_kelas ?? '-' }}

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
                                    Rp
                                    {{ number_format($item->nominal ?? 0, 0, ',', '.') }}
                                </strong>

                            </td>


                            {{-- METODE --}}
                            <td class="col-metode">

                                @if ($metode === 'transfer')

                                    <span class="metode-badge metode-transfer">

                                        <i data-lucide="landmark"></i>

                                        Transfer

                                    </span>

                                @elseif ($metode === 'qris')

                                    <span class="metode-badge metode-qris">

                                        <i data-lucide="qr-code"></i>

                                        QRIS

                                    </span>

                                @elseif ($metode === 'cash')

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

                                @if ($status === 'dibayar')

                                    <span class="status-badge status-dibayar">

                                        <i data-lucide="circle-check"></i>

                                        Dibayar

                                    </span>

                                @elseif ($status === 'menunggu')

                                    <span class="status-badge status-menunggu">

                                        <i data-lucide="clock-3"></i>

                                        Menunggu

                                    </span>

                                @elseif ($status === 'ditolak')

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

                                @if ($tanggal)

                                    <div class="tanggal-cell">

                                        <strong>

                                            {{
                                                \Carbon\Carbon::parse($tanggal)
                                                    ->timezone('Asia/Jakarta')
                                                    ->format('d-m-Y')
                                            }}

                                        </strong>

                                        <span>

                                            {{
                                                \Carbon\Carbon::parse($tanggal)
                                                    ->timezone('Asia/Jakarta')
                                                    ->format('H:i')
                                            }}

                                        </span>

                                    </div>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td class="col-aksi">

                                <div class="laporan-actions-table">


                                    {{-- DETAIL --}}
                                    <a
                                        href="{{ route('admin.laporan.show', $item->id) }}"
                                        class="laporan-action laporan-action-detail"
                                        title="Detail"
                                    >
                                        <i data-lucide="info"></i>
                                    </a>


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('admin.laporan.edit', $item->id) }}"
                                        class="laporan-action laporan-action-edit"
                                        title="Edit"
                                    >
                                        <i data-lucide="square-pen"></i>
                                    </a>


                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route('admin.laporan.destroy', $item->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pembayaran ini?')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="laporan-action laporan-action-delete"
                                            title="Hapus"
                                        >
                                            <i data-lucide="trash-2"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="12">

                                <div class="laporan-empty">

                                    <div class="laporan-empty-icon">

                                        <i data-lucide="file-x-2"></i>

                                    </div>


                                    <h3>
                                        Belum Ada Data Pembayaran
                                    </h3>


                                    <p>
                                        Belum terdapat data pembayaran
                                        yang sesuai dengan filter
                                        yang dipilih.
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


{{-- =============================================================
    SCRIPT
============================================================= --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const checkAll =
        document.getElementById('checkAll');

    const checkboxes =
        document.querySelectorAll('.payment-checkbox');

    const btnHapusTerpilih =
        document.getElementById('btnHapusTerpilih');

    const jumlahDipilih =
        document.getElementById('jumlahDipilih');

    const searchLaporan =
        document.getElementById('searchLaporan');


    /*
    |--------------------------------------------------------------------------
    | UPDATE JUMLAH DIPILIH
    |--------------------------------------------------------------------------
    */

    function updateSelected() {

        const selected =
            document.querySelectorAll(
                '.payment-checkbox:checked'
            );


        jumlahDipilih.textContent =
            selected.length + ' dipilih';


        btnHapusTerpilih.disabled =
            selected.length === 0;


        if (selected.length === 0) {

            checkAll.checked = false;

            checkAll.indeterminate = false;

        }

        else if (
            selected.length === checkboxes.length
        ) {

            checkAll.checked = true;

            checkAll.indeterminate = false;

        }

        else {

            checkAll.checked = false;

            checkAll.indeterminate = true;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | PILIH SEMUA
    |--------------------------------------------------------------------------
    */

    if (checkAll) {

        checkAll.addEventListener(
            'change',
            function () {

                checkboxes.forEach(
                    function (checkbox) {

                        const row =
                            checkbox.closest('tr');


                        if (
                            row &&
                            row.style.display !== 'none'
                        ) {

                            checkbox.checked =
                                checkAll.checked;

                        }

                    }
                );


                updateSelected();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CHECKBOX INDIVIDUAL
    |--------------------------------------------------------------------------
    */

    checkboxes.forEach(
        function (checkbox) {

            checkbox.addEventListener(
                'change',
                function () {

                    updateSelected();

                }
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if (searchLaporan) {

        searchLaporan.addEventListener(
            'input',
            function () {

                const keyword =
                    this.value
                        .toLowerCase()
                        .trim();


                const rows =
                    document.querySelectorAll(
                        '.laporan-table tbody tr'
                    );


                rows.forEach(
                    function (row) {

                        const checkbox =
                            row.querySelector(
                                '.payment-checkbox'
                            );


                        if (!checkbox) {
                            return;
                        }


                        const text =
                            row.textContent
                                .toLowerCase();


                        if (
                            text.includes(keyword)
                        ) {

                            row.style.display = '';

                        } else {

                            row.style.display = 'none';

                            checkbox.checked = false;

                        }

                    }
                );


                updateSelected();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS TERPILIH
    |--------------------------------------------------------------------------
    */

    if (btnHapusTerpilih) {

        btnHapusTerpilih.addEventListener(
            'click',
            function () {

                const selected =
                    document.querySelectorAll(
                        '.payment-checkbox:checked'
                    );


                if (selected.length === 0) {
                    return;
                }


                const konfirmasi =
                    confirm(
                        'Apakah Anda yakin ingin menghapus ' +
                        selected.length +
                        ' data pembayaran yang dipilih?'
                    );


                if (!konfirmasi) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | BUAT FORM
                |--------------------------------------------------------------------------
                */

                const form =
                    document.createElement('form');


                /*
                |--------------------------------------------------------------------------
                | METHOD
                |--------------------------------------------------------------------------
                */

                form.method = 'POST';


                /*
                |--------------------------------------------------------------------------
                | ACTION
                |--------------------------------------------------------------------------
                */

                form.action =
                    '{{ route('admin.laporan.bulk-destroy') }}';


                /*
                |--------------------------------------------------------------------------
                | CSRF
                |--------------------------------------------------------------------------
                */

                const csrf =
                    document.createElement('input');


                csrf.type = 'hidden';

                csrf.name = '_token';

                csrf.value =
                    '{{ csrf_token() }}';


                form.appendChild(csrf);


                /*
                |--------------------------------------------------------------------------
                | PAYMENT IDS
                |--------------------------------------------------------------------------
                */

                selected.forEach(
                    function (checkbox) {

                        const input =
                            document.createElement('input');


                        input.type = 'hidden';

                        input.name =
                            'payment_ids[]';

                        input.value =
                            checkbox.value;


                        form.appendChild(input);

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | MASUKKAN FORM KE BODY
                |--------------------------------------------------------------------------
                */

                document.body.appendChild(form);


                /*
                |--------------------------------------------------------------------------
                | SUBMIT
                |--------------------------------------------------------------------------
                */

                form.submit();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | LUCIDE ICON
    |--------------------------------------------------------------------------
    */

    if (
        typeof lucide !== 'undefined'
    ) {

        lucide.createIcons();

    }

});

</script>

@endpush

@endsection