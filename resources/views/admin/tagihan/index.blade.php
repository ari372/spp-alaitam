@extends('layouts.admin')

@section('title', 'Tagihan')

@section('page-title', 'Tagihan')

@push('styles')
    @vite('resources/css/admin/tagihan.css')
@endpush

@section('content')

<div class="tagihan-container">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="tagihan-header">

        <div class="tagihan-header-title">

            <h2>Data Tagihan</h2>

            <p>
                Daftar tagihan siswa berdasarkan kelompok siswa dan tahun ajaran
            </p>

        </div>

    </div>


    {{-- =====================================================
         TOOLBAR PENCARIAN DAN BUAT TAGIHAN
    ====================================================== --}}

    <div class="tagihan-toolbar">

        {{-- FORM PENCARIAN --}}

        <form
            action="{{ route('admin.tagihan.index') }}"
            method="GET"
            class="tagihan-search-form"
        >

            <div class="tagihan-search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    class="tagihan-search-input"
                    placeholder="Cari nama siswa, NIS, kategori, atau tahun ajaran..."
                >

            </div>


            <button
                type="submit"
                class="tagihan-search-button"
            >

                <i class="bi bi-search"></i>

                <span>Cari</span>

            </button>


            @if(request('search'))

                <a
                    href="{{ route('admin.tagihan.index') }}"
                    class="tagihan-reset-button"
                >

                    <i class="bi bi-arrow-counterclockwise"></i>

                    <span>Reset</span>

                </a>

            @endif

        </form>


        {{-- TOMBOL BUAT TAGIHAN --}}

        <a
            href="{{ route('admin.tagihan.create') }}"
            class="btn-tambah-tagihan"
        >

            <i class="bi bi-plus-lg"></i>

            <span>Buat Tagihan</span>

        </a>

    </div>


    {{-- =====================================================
         INFORMASI HASIL PENCARIAN
    ====================================================== --}}

    @if(request('search'))

        <div class="tagihan-search-result">

            <i class="bi bi-info-circle"></i>

            <span>
                Menampilkan hasil pencarian untuk:
            </span>

            <strong>
                "{{ request('search') }}"
            </strong>

        </div>

    @endif


    {{-- =====================================================
         SUCCESS ALERT
    ====================================================== --}}

    @if(session('success'))

        <div class="alert alert-success">

            <i class="bi bi-check-circle"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
         ERROR ALERT
    ====================================================== --}}

    @if(session('error'))

        <div class="alert alert-error">

            <i class="bi bi-exclamation-triangle"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
         VALIDATION ERROR
    ====================================================== --}}

    @if($errors->any())

        <div class="alert alert-error">

            <i class="bi bi-exclamation-triangle"></i>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =====================================================
         TOOLBAR PILIH DAN HAPUS
    ====================================================== --}}

    <div class="tagihan-bulk-toolbar">

        <div class="tagihan-bulk-info">

            <i class="bi bi-check2-square"></i>

            <span id="jumlahTagihanDipilih">
                0 tagihan dipilih
            </span>

        </div>


        <button
            type="button"
            id="btnHapusTagihanTerpilih"
            class="btn-hapus-tagihan-terpilih"
            disabled
        >

            <i class="bi bi-trash3"></i>

            <span>
                Hapus Terpilih
            </span>

        </button>

    </div>


    {{-- =====================================================
         TABLE CARD
    ====================================================== --}}

    <div class="tagihan-table-card">

        <div class="tagihan-table-wrapper">

            <table class="tagihan-table">

                <thead>

                    <tr>

                        {{-- PILIH SEMUA --}}

                        <th class="kolom-check">

                            <input
                                type="checkbox"
                                id="checkAllTagihan"
                                title="Pilih semua tagihan"
                            >

                        </th>


                        {{-- NOMOR --}}

                        <th class="kolom-no">
                            No
                        </th>


                        {{-- SISWA --}}

                        <th>
                            Siswa
                        </th>


                        {{-- KELAS --}}

                        <th>
                            Kelas
                        </th>


                        {{-- DAFTAR TAGIHAN --}}

                        <th>
                            Daftar Tagihan
                        </th>


                        {{-- TOTAL --}}

                        <th>
                            Total Tagihan
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($tagihan as $siswaId => $daftarTagihan)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | DATA SISWA
                            |--------------------------------------------------------------------------
                            */

                            $tagihanPertama =
                                $daftarTagihan->first();

                            $siswa =
                                $tagihanPertama?->siswa;


                            /*
                            |--------------------------------------------------------------------------
                            | TOTAL SEMUA TAGIHAN SISWA
                            |--------------------------------------------------------------------------
                            */

                            $totalTagihan =
                                $daftarTagihan->sum('nominal');


                            /*
                            |--------------------------------------------------------------------------
                            | GROUPING BERDASARKAN TAHUN AJARAN
                            |--------------------------------------------------------------------------
                            */

                            $tagihanPerTahun =
                                $daftarTagihan
                                    ->groupBy(function ($item) {
                                        return $item->tahun_ajaran_id;
                                    });

                        @endphp


                        <tr>

                            {{-- =================================================
                                 CHECKBOX SISWA
                            ================================================== --}}

                            <td class="kolom-check">

                                {{--

                                    Tidak memilih seluruh siswa sekaligus.
                                    Checkbox utama berada di header.

                                --}}

                            </td>


                            {{-- =================================================
                                 NOMOR
                            ================================================== --}}

                            <td class="no">

                                {{ $loop->iteration }}

                            </td>


                            {{-- =================================================
                                 DATA SISWA
                            ================================================== --}}

                            <td>

                                <div class="tagihan-siswa-info">

                                    <strong>
                                        {{ $siswa?->nama ?? '-' }}
                                    </strong>


                                    @if($siswa?->nis)

                                        <small>
                                            NIS: {{ $siswa->nis }}
                                        </small>

                                    @endif

                                </div>

                            </td>


                            {{-- =================================================
                                 KELAS
                            ================================================== --}}

                            <td>

                                <span class="tagihan-kelas">

                                    {{ $siswa?->kelas?->nama_kelas ?? '-' }}

                                </span>

                            </td>


                            {{-- =================================================
                                 DAFTAR TAGIHAN PER TAHUN AJARAN
                            ================================================== --}}

                            <td>

                                <div class="tagihan-year-list">


                                    @foreach($tagihanPerTahun as $tahunId => $daftarTahun)

                                        @php

                                            $tahunAjaran =
                                                $daftarTahun
                                                    ->first()
                                                    ?->tahunAjaran;

                                            $totalTahun =
                                                $daftarTahun
                                                    ->sum('nominal');

                                        @endphp


                                        {{-- =====================================
                                             GROUP TAHUN AJARAN
                                        ====================================== --}}

                                        <div class="tagihan-year-group">


                                            {{-- HEADER TAHUN AJARAN --}}

                                            <div class="tagihan-year-header">


                                                <div class="tagihan-year-title">


                                                    <div class="tagihan-year-icon">

                                                        <i class="bi bi-calendar3"></i>

                                                    </div>


                                                    <div>

                                                        <strong>

                                                            Tahun Ajaran
                                                            {{ $tahunAjaran?->nama ?? '-' }}

                                                        </strong>


                                                        <small>

                                                            {{ $daftarTahun->count() }}
                                                            jenis tagihan

                                                        </small>

                                                    </div>

                                                </div>


                                                <div class="tagihan-year-total">

                                                    Rp
                                                    {{ number_format($totalTahun, 0, ',', '.') }}

                                                </div>


                                            </div>


                                            {{-- DAFTAR TAGIHAN --}}

                                            <div class="tagihan-year-items">


                                                @foreach($daftarTahun as $item)

                                                    <div class="tagihan-list-item">


                                                        {{-- CHECKBOX TAGIHAN --}}

                                                        <div class="tagihan-check">

                                                            <input
                                                                type="checkbox"
                                                                class="tagihan-checkbox"
                                                                value="{{ $item->id }}"
                                                                title="Pilih tagihan"
                                                            >

                                                        </div>


                                                        {{-- INFORMASI TAGIHAN --}}

                                                        <div class="tagihan-list-info">


                                                            {{-- ICON --}}

                                                            <div class="tagihan-category-icon">

                                                                @php

                                                                    $namaKategori =
                                                                        strtolower(
                                                                            $item->kategori?->nama ?? ''
                                                                        );

                                                                @endphp


                                                                @if(str_contains($namaKategori, 'spp'))

                                                                    <i class="bi bi-receipt"></i>


                                                                @elseif(str_contains($namaKategori, 'jas'))

                                                                    <i class="bi bi-person-badge"></i>


                                                                @elseif(
                                                                    str_contains($namaKategori, 'ujian') ||
                                                                    str_contains($namaKategori, 'pts')
                                                                )

                                                                    <i class="bi bi-clipboard-check"></i>


                                                                @else

                                                                    <i class="bi bi-file-text"></i>

                                                                @endif

                                                            </div>


                                                            {{-- DETAIL --}}

                                                            <div class="tagihan-list-detail">


                                                                <span class="tagihan-category">

                                                                    {{ $item->kategori?->nama ?? '-' }}

                                                                </span>


                                                                <span class="tagihan-item-nominal">

                                                                    Rp
                                                                    {{ number_format($item->nominal ?? 0, 0, ',', '.') }}

                                                                </span>


                                                                <small class="tagihan-item-tempo">

                                                                    Jatuh tempo:

                                                                    @if($item->jatuh_tempo)

                                                                        {{ \Carbon\Carbon::parse($item->jatuh_tempo)->format('d-m-Y') }}

                                                                    @else

                                                                        -

                                                                    @endif

                                                                </small>


                                                            </div>

                                                        </div>


                                                        {{-- =================================================
                                                             AKSI TAGIHAN
                                                        ================================================== --}}

                                                        <div class="tagihan-item-actions">


                                                            {{-- DETAIL --}}

                                                            <a
                                                                href="{{ route('admin.tagihan.show', $item->id) }}"
                                                                class="btn-icon btn-detail"
                                                                title="Lihat detail tagihan"
                                                                aria-label="Lihat detail tagihan"
                                                            >

                                                                <i class="bi bi-info-circle"></i>

                                                            </a>


                                                            {{-- EDIT --}}

                                                            <a
                                                                href="{{ route('admin.tagihan.edit', $item->id) }}"
                                                                class="btn-icon btn-edit"
                                                                title="Edit tagihan"
                                                                aria-label="Edit tagihan"
                                                            >

                                                                <i class="bi bi-pencil-square"></i>

                                                            </a>


                                                            {{-- HAPUS SATUAN --}}

                                                            <form
                                                                action="{{ route('admin.tagihan.destroy', $item->id) }}"
                                                                method="POST"
                                                                class="form-hapus"
                                                                onsubmit="return confirm('Yakin ingin menghapus tagihan ini?')"
                                                            >

                                                                @csrf

                                                                @method('DELETE')


                                                                <button
                                                                    type="submit"
                                                                    class="btn-icon btn-hapus"
                                                                    title="Hapus tagihan"
                                                                    aria-label="Hapus tagihan"
                                                                >

                                                                    <i class="bi bi-trash3"></i>

                                                                </button>

                                                            </form>


                                                        </div>


                                                    </div>

                                                @endforeach


                                            </div>

                                        </div>

                                    @endforeach


                                </div>

                            </td>


                            {{-- =================================================
                                 TOTAL TAGIHAN SISWA
                            ================================================== --}}

                            <td>

                                <div class="tagihan-total-wrapper">


                                    <strong class="tagihan-total">

                                        Rp
                                        {{ number_format($totalTagihan, 0, ',', '.') }}

                                    </strong>


                                    <small class="tagihan-total-count">

                                        {{ $daftarTagihan->count() }}
                                        jenis tagihan

                                    </small>


                                </div>

                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="6"
                                class="tagihan-empty"
                            >

                                <i class="bi bi-inbox"></i>


                                <span>

                                    @if(request('search'))

                                        Data tagihan tidak ditemukan.

                                    @else

                                        Belum ada tagihan.

                                    @endif

                                </span>

                            </td>

                        </tr>


                    @endforelse


                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkAll = document.getElementById('checkAllTagihan');

    const checkboxes = document.querySelectorAll(
        '.tagihan-checkbox'
    );

    const jumlahDipilih = document.getElementById(
        'jumlahTagihanDipilih'
    );

    const btnHapus = document.getElementById(
        'btnHapusTagihanTerpilih'
    );


    function updateSelected() {

        const selected = document.querySelectorAll(
            '.tagihan-checkbox:checked'
        );

        const jumlah = selected.length;


        if (jumlahDipilih) {
            jumlahDipilih.textContent =
                jumlah + ' tagihan dipilih';
        }


        if (btnHapus) {
            btnHapus.disabled = jumlah === 0;
        }


        if (!checkAll) {
            return;
        }


        if (jumlah === 0) {

            checkAll.checked = false;
            checkAll.indeterminate = false;

        } else if (jumlah === checkboxes.length) {

            checkAll.checked = true;
            checkAll.indeterminate = false;

        } else {

            checkAll.checked = false;
            checkAll.indeterminate = true;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PASTIKAN AWALNYA TIDAK ADA YANG TERPILIH
    |--------------------------------------------------------------------------
    */

    checkboxes.forEach(function (checkbox) {
        checkbox.checked = false;
    });

    if (checkAll) {
        checkAll.checked = false;
        checkAll.indeterminate = false;
    }


    /*
    |--------------------------------------------------------------------------
    | PILIH SEMUA
    |--------------------------------------------------------------------------
    */

    if (checkAll) {

        checkAll.addEventListener('change', function () {

            checkboxes.forEach(function (checkbox) {

                checkbox.checked =
                    checkAll.checked;

            });

            updateSelected();
        });
    }


    /*
    |--------------------------------------------------------------------------
    | CHECKBOX SATUAN
    |--------------------------------------------------------------------------
    */

    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener(
            'change',
            function () {

                updateSelected();

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | HAPUS TERPILIH
    |--------------------------------------------------------------------------
    */

    if (btnHapus) {

        btnHapus.addEventListener(
            'click',
            function () {

                const selected =
                    document.querySelectorAll(
                        '.tagihan-checkbox:checked'
                    );


                if (selected.length === 0) {
                    return;
                }


                const jumlah =
                    selected.length;


                const konfirmasi = confirm(
                    'Apakah Anda yakin ingin menghapus ' +
                    jumlah +
                    ' tagihan yang dipilih?'
                );


                if (!konfirmasi) {
                    return;
                }


                const form =
                    document.createElement('form');

                form.method = 'POST';

                form.action =
                    '{{ route('admin.tagihan.bulk-destroy') }}';


                const csrf =
                    document.createElement('input');

                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                form.appendChild(csrf);


                selected.forEach(function (checkbox) {

                    const input =
                        document.createElement('input');

                    input.type = 'hidden';

                    input.name =
                        'tagihan_ids[]';

                    input.value =
                        checkbox.value;

                    form.appendChild(input);

                });


                document.body.appendChild(form);

                form.submit();
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ICON
    |--------------------------------------------------------------------------
    */

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL
    |--------------------------------------------------------------------------
    */

    updateSelected();

});
</script>
@endpush