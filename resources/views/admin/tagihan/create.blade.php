@extends('layouts.admin')

@section('title', 'Buat Tagihan')

@section('page-title', 'Buat Tagihan')

@push('styles')

    @vite('resources/css/admin/tagihan.css')

@endpush


@section('content')

<div class="tagihan-detail-container">

    <div class="tagihan-detail-card">

        {{-- HEADER --}}
        <div class="tagihan-detail-header">

            <div>

                <h2 class="tagihan-detail-title">

                    <i class="bi bi-file-earmark-plus"></i>

                    Buat Tagihan

                </h2>

                <p class="tagihan-detail-subtitle">

                    Buat tagihan untuk seluruh siswa sekaligus.

                </p>

            </div>


            <span class="tagihan-form-badge">

                <i class="bi bi-people"></i>

                Semua Siswa

            </span>

        </div>


        {{-- ERROR DARI CONTROLLER --}}
        @if(session('error'))

            <div class="tagihan-alert tagihan-alert-error">

                <i class="bi bi-exclamation-triangle-fill"></i>

                {{ session('error') }}

            </div>

        @endif


        {{-- ERROR VALIDASI --}}
        @if($errors->any())

            <div class="tagihan-alert tagihan-alert-error">

                <div class="tagihan-error-title">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    Terjadi kesalahan

                </div>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- INFORMASI --}}
        <div class="tagihan-info">

            <div class="tagihan-info-title">

                <i class="bi bi-info-circle-fill"></i>

                Informasi

            </div>

            <p>

                Tagihan akan dibuat secara otomatis untuk
                <strong>seluruh siswa</strong> yang terdaftar.

                Untuk kategori <strong>SPP</strong>,
                tagihan dibuat setiap bulan sesuai periode
                tahun ajaran.

                Jika memilih
                <strong>Semua Kategori</strong>,
                semua kategori tagihan akan dibuat sesuai
                aturan masing-masing kategori.

            </p>

        </div>


        {{-- FORM --}}
        <form
            method="POST"
            action="{{ route('admin.tagihan.store') }}"
        >

            @csrf


            <div class="tagihan-form-grid">


                {{-- TAHUN AJARAN --}}
                <div class="tagihan-form-group">

                    <label for="tahun_ajaran_id">

                        <i class="bi bi-calendar3"></i>

                        Tahun Ajaran

                    </label>


                    <select
                        name="tahun_ajaran_id"
                        id="tahun_ajaran_id"
                        required
                        class="tagihan-form-control"
                    >

                        <option value="">

                            -- Pilih Tahun Ajaran --

                        </option>


                        @foreach($tahunAjaran as $tahun)

                            <option
                                value="{{ $tahun->id }}"
                                data-mulai="{{ $tahun->tanggal_mulai }}"
                                data-selesai="{{ $tahun->tanggal_selesai }}"
                                {{ old('tahun_ajaran_id') == $tahun->id ? 'selected' : '' }}
                            >

                                {{ $tahun->nama }}

                                @if($tahun->aktif)

                                    (Aktif)

                                @endif

                            </option>

                        @endforeach

                    </select>


                    <small class="tagihan-form-help">

                        Pilih tahun ajaran yang akan digunakan
                        untuk tagihan.

                    </small>

                </div>


                {{-- KATEGORI --}}
                <div class="tagihan-form-group">

                    <label for="kategori">

                        <i class="bi bi-tags"></i>

                        Kategori Pembayaran

                    </label>


                    <select
                        name="kategori_tagihan_id"
                        id="kategori"
                        required
                        class="tagihan-form-control"
                    >

                        <option value="">

                            -- Pilih Kategori --

                        </option>


                        {{-- SEMUA KATEGORI --}}
                        <option
                            value="semua"
                            {{ old('kategori_tagihan_id') === 'semua' ? 'selected' : '' }}
                        >

                            Semua Kategori

                        </option>


                        @foreach($kategori as $item)

                            <option
                                value="{{ $item->id }}"
                                data-nama="{{ strtolower(trim($item->nama)) }}"
                                data-nominal="{{ $item->nominal }}"
                                {{ old('kategori_tagihan_id') == $item->id ? 'selected' : '' }}
                            >

                                {{ $item->nama }}

                                -

                                Rp
                                {{ number_format($item->nominal, 0, ',', '.') }}

                            </option>

                        @endforeach

                    </select>


                    <small class="tagihan-form-help">

                        Pilih satu kategori atau pilih
                        Semua Kategori.

                    </small>

                </div>


                {{-- NOMINAL --}}
                <div class="tagihan-form-group">

                    <label for="nominal">

                        <i class="bi bi-cash-stack"></i>

                        Nominal Tagihan

                    </label>


                    <input
                        type="text"
                        id="nominal"
                        readonly
                        placeholder="Otomatis dari kategori"
                        class="tagihan-form-control tagihan-readonly"
                    >


                    <small class="tagihan-form-help">

                        Nominal mengikuti kategori
                        yang dipilih.

                    </small>

                </div>


                {{-- TARGET SISWA --}}
                <div class="tagihan-form-group">

                    <label for="target">

                        <i class="bi bi-people"></i>

                        Target Tagihan

                    </label>


                    <input
                        type="text"
                        id="target"
                        value="{{ \App\Models\Siswa::count() }} Siswa"
                        readonly
                        class="tagihan-form-control tagihan-target"
                    >


                    <small class="tagihan-form-help">

                        Tagihan akan dibuat untuk
                        seluruh siswa yang terdaftar.

                    </small>

                </div>


                {{-- PERIODE --}}
                <div class="tagihan-form-group">

                    <label for="periode">

                        <i class="bi bi-calendar-range"></i>

                        Periode

                    </label>


                    <input
                        type="text"
                        id="periode"
                        readonly
                        placeholder="Pilih tahun ajaran terlebih dahulu"
                        class="tagihan-form-control tagihan-readonly"
                    >


                    <small class="tagihan-form-help">

                        Untuk SPP, periode mengikuti
                        tahun ajaran dari bulan mulai
                        sampai bulan selesai.

                    </small>

                </div>


                {{-- JATUH TEMPO --}}
                <div class="tagihan-form-group">

                    <label for="jatuh_tempo_info">

                        <i class="bi bi-calendar-event"></i>

                        Jatuh Tempo

                    </label>


                    @php

                        $pengaturanPembayaran =
                            \App\Models\PengaturanPembayaran::first();

                        $tanggalJatuhTempo =
                            $pengaturanPembayaran?->tanggal_jatuh_tempo ?? 5;

                        $aktifPengaturan =
                            $pengaturanPembayaran?->aktif ?? true;

                    @endphp


                    @if($aktifPengaturan)

                        <input
                            type="text"
                            id="jatuh_tempo_info"
                            readonly
                            value="Tanggal {{ $tanggalJatuhTempo }} setiap bulan"
                            class="tagihan-form-control tagihan-readonly"
                        >


                        <small class="tagihan-form-help">

                            Tanggal jatuh tempo mengikuti
                            pengaturan pembayaran.

                            <strong>
                                Tanggal {{ $tanggalJatuhTempo }}
                                setiap bulan.
                            </strong>

                        </small>

                    @else

                        <input
                            type="text"
                            id="jatuh_tempo_info"
                            readonly
                            value="Pengaturan jatuh tempo tidak aktif"
                            class="tagihan-form-control tagihan-readonly"
                        >


                        <small class="tagihan-form-help">

                            Pengaturan jatuh tempo sedang
                            tidak aktif.

                        </small>

                    @endif

                </div>


                {{-- POLA TAGIHAN --}}
                <div class="tagihan-form-group">

                    <label for="pola_tagihan">

                        <i class="bi bi-repeat"></i>

                        Pola Tagihan

                    </label>


                    <input
                        type="text"
                        id="pola_tagihan"
                        readonly
                        value="Pilih kategori terlebih dahulu"
                        class="tagihan-form-control tagihan-readonly"
                    >


                    <small class="tagihan-form-help">

                        SPP dibuat bulanan.
                        Kategori lainnya dibuat satu kali.

                    </small>

                </div>


            </div>


            {{-- BUTTON --}}
            <div class="tagihan-detail-actions">

                <a
                    href="{{ route('admin.tagihan.index') }}"
                    class="tagihan-detail-btn tagihan-detail-btn-kembali"
                >

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>


                <button
                    type="submit"
                    class="tagihan-detail-btn tagihan-detail-btn-edit"
                >

                    <i class="bi bi-check-circle"></i>

                    Buat Tagihan

                </button>

            </div>


        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const tahunAjaranSelect =
        document.getElementById('tahun_ajaran_id');

    const kategoriSelect =
        document.getElementById('kategori');

    const nominalInput =
        document.getElementById('nominal');

    const periodeInput =
        document.getElementById('periode');

    const polaTagihanInput =
        document.getElementById('pola_tagihan');


    /*
    |--------------------------------------------------------------------------
    | NAMA BULAN
    |--------------------------------------------------------------------------
    */

    const namaBulan = [
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
    ];


    /*
    |--------------------------------------------------------------------------
    | FORMAT TANGGAL
    |--------------------------------------------------------------------------
    */

    function parseTanggal(tanggal) {

        if (!tanggal) {
            return null;
        }

        const bagian =
            tanggal.split('-');

        if (bagian.length !== 3) {
            return null;
        }

        return {
            tahun: Number(bagian[0]),
            bulan: Number(bagian[1]),
            hari: Number(bagian[2])
        };
    }


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN NOMINAL
    |--------------------------------------------------------------------------
    */

    function tampilkanNominal() {

        if (
            !kategoriSelect ||
            !nominalInput
        ) {
            return;
        }


        const option =
            kategoriSelect.options[
                kategoriSelect.selectedIndex
            ];


        if (
            !option ||
            !option.value
        ) {

            nominalInput.value = '';

            return;
        }


        if (
            option.value === 'semua'
        ) {

            nominalInput.value =
                'Mengikuti nominal semua kategori';

            return;
        }


        const nominal =
            Number(
                option.dataset.nominal || 0
            );


        nominalInput.value =
            'Rp ' +
            nominal.toLocaleString('id-ID');
    }


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN PERIODE
    |--------------------------------------------------------------------------
    */

    function tampilkanPeriode() {

        if (
            !tahunAjaranSelect ||
            !periodeInput
        ) {
            return;
        }


        const option =
            tahunAjaranSelect.options[
                tahunAjaranSelect.selectedIndex
            ];


        if (
            !option ||
            !option.value
        ) {

            periodeInput.value =
                'Pilih tahun ajaran terlebih dahulu';

            return;
        }


        const tanggalMulai =
            parseTanggal(
                option.dataset.mulai
            );


        const tanggalSelesai =
            parseTanggal(
                option.dataset.selesai
            );


        if (
            !tanggalMulai ||
            !tanggalSelesai
        ) {

            periodeInput.value =
                'Periode tahun ajaran belum tersedia';

            return;
        }


        periodeInput.value =
            namaBulan[tanggalMulai.bulan - 1] +
            ' ' +
            tanggalMulai.tahun +
            ' - ' +
            namaBulan[tanggalSelesai.bulan - 1] +
            ' ' +
            tanggalSelesai.tahun;
    }


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN POLA TAGIHAN
    |--------------------------------------------------------------------------
    */

    function tampilkanPolaTagihan() {

        if (
            !kategoriSelect ||
            !polaTagihanInput
        ) {
            return;
        }


        const option =
            kategoriSelect.options[
                kategoriSelect.selectedIndex
            ];


        if (
            !option ||
            !option.value
        ) {

            polaTagihanInput.value =
                'Pilih kategori terlebih dahulu';

            return;
        }


        if (
            option.value === 'semua'
        ) {

            polaTagihanInput.value =
                'SPP bulanan, kategori lainnya satu kali';

            return;
        }


        const namaKategori =
            (
                option.dataset.nama || ''
            ).trim().toLowerCase();


        if (
            namaKategori === 'spp'
        ) {

            polaTagihanInput.value =
                'Bulanan — 12 tagihan per siswa';

        } else {

            polaTagihanInput.value =
                'Satu kali — 1 tagihan per siswa';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EVENT KATEGORI
    |--------------------------------------------------------------------------
    */

    if (kategoriSelect) {

        kategoriSelect.addEventListener(
            'change',
            function () {

                tampilkanNominal();

                tampilkanPolaTagihan();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT TAHUN AJARAN
    |--------------------------------------------------------------------------
    */

    if (tahunAjaranSelect) {

        tahunAjaranSelect.addEventListener(
            'change',
            function () {

                tampilkanPeriode();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    */

    tampilkanNominal();

    tampilkanPeriode();

    tampilkanPolaTagihan();


    /*
    |--------------------------------------------------------------------------
    | LUCIDE
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