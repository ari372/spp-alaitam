@extends('layouts.admin')

@section('title', 'Koreksi Pembayaran')

@section('page-title', 'Koreksi Pembayaran')

@section('content')

@vite('resources/css/admin/pembayaran.css')

<div class="koreksi-container">

    {{-- HEADER --}}
    <div class="koreksi-header">

        <div class="koreksi-header-content">

            <div class="koreksi-header-icon">
                <i data-lucide="pencil"></i>
            </div>

            <div>
                <h2>
                    Koreksi Pembayaran
                </h2>

                <p>
                    Periksa detail pembayaran dan sesuaikan nominal
                    berdasarkan bukti pembayaran.
                </p>
            </div>

        </div>

        <a
            href="{{ route('admin.pembayaran.index') }}"
            class="koreksi-header-back"
        >
            <i data-lucide="arrow-left"></i>
            Kembali
        </a>

    </div>


    {{-- ALERT ERROR --}}
    @if(session('error'))

        <div class="pembayaran-alert pembayaran-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- ALERT SUCCESS --}}
    @if(session('success'))

        <div class="pembayaran-alert pembayaran-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="pembayaran-alert pembayaran-alert-error">

            <i data-lucide="triangle-alert"></i>

            <div>

                <strong>
                    Terdapat kesalahan:
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>

            </div>

        </div>

    @endif


    {{-- INFORMASI + BUKTI --}}
    <div class="koreksi-grid">


        {{-- INFORMASI PEMBAYARAN --}}
        <div class="koreksi-card">

            <div class="koreksi-card-header">

                <div class="koreksi-card-title">

                    <div class="koreksi-icon">
                        <i data-lucide="clipboard-list"></i>
                    </div>

                    <div>
                        <h3>
                            Informasi Pembayaran
                        </h3>

                        <p>
                            Detail pembayaran dari orang tua
                        </p>
                    </div>

                </div>

            </div>


            <div class="koreksi-detail-list">


                {{-- SISWA --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        <i data-lucide="user-round"></i>
                        Nama Siswa
                    </span>

                    <strong class="koreksi-value">
                        {{ $tagihan->siswa->nama ?? '-' }}
                    </strong>

                </div>


                {{-- NIS --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        <i data-lucide="badge"></i>
                        NIS
                    </span>

                    <strong class="koreksi-value">
                        {{ $tagihan->siswa->nis ?? '-' }}
                    </strong>

                </div>


                {{-- TAHUN AJARAN --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        <i data-lucide="calendar-days"></i>
                        Tahun Ajaran
                    </span>

                    <strong class="koreksi-value">
                        {{ $tagihan->tahunAjaran->nama ?? '-' }}
                    </strong>

                </div>


                {{-- KATEGORI --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        <i data-lucide="tag"></i>
                        Kategori
                    </span>

                    <strong class="koreksi-value koreksi-green">
                        {{ $tagihan->kategori->nama ?? '-' }}
                    </strong>

                </div>


                {{-- TOTAL TAGIHAN --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        <i data-lucide="receipt"></i>
                        Total Tagihan
                    </span>

                    <strong class="koreksi-value koreksi-nominal">

                        Rp {{ number_format(
                            $tagihan->nominal,
                            0,
                            ',',
                            '.'
                        ) }}

                    </strong>

                </div>


                {{-- SUDAH DIBAYAR --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        <i data-lucide="circle-check"></i>
                        Sudah Dibayar
                    </span>

                    <strong class="koreksi-value">

                        Rp {{ number_format(
                            $sudahDibayar,
                            0,
                            ',',
                            '.'
                        ) }}

                    </strong>

                </div>


                {{-- SISA TAGIHAN --}}
                <div class="koreksi-detail-item koreksi-sisa">

                    <span class="koreksi-label">
                        <i data-lucide="wallet"></i>
                        Sisa Tagihan
                    </span>

                    <strong class="koreksi-value koreksi-nominal">

                        Rp {{ number_format(
                            $sisaTagihan,
                            0,
                            ',',
                            '.'
                        ) }}

                    </strong>

                </div>


                {{-- METODE --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        <i data-lucide="wallet-cards"></i>
                        Metode Pembayaran
                    </span>

                    <strong class="koreksi-value">

                        <span class="metode-badge">
                            {{ strtoupper($pembayaran->metode ?? '-') }}
                        </span>

                    </strong>

                </div>


                {{-- WAKTU --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        <i data-lucide="clock"></i>
                        Waktu Pengiriman
                    </span>

                    <strong class="koreksi-value">

                        {{ $pembayaran->tanggal_kirim
                            ? $pembayaran->tanggal_kirim->format('d-m-Y H:i')
                            : '-'
                        }}

                    </strong>

                </div>


            </div>

        </div>


        {{-- BUKTI PEMBAYARAN --}}
        <div class="koreksi-card">

            <div class="koreksi-card-header">

                <div class="koreksi-card-title">

                    <div class="koreksi-icon">
                        <i data-lucide="image"></i>
                    </div>

                    <div>
                        <h3>
                            Bukti Pembayaran
                        </h3>

                        <p>
                            Periksa bukti yang dikirim orang tua
                        </p>
                    </div>

                </div>

            </div>


            <div class="koreksi-bukti">

                @if($pembayaran->bukti_pembayaran)

                    <div class="koreksi-bukti-image-wrapper">

                        <img
                            src="{{ asset(
                                'storage/' .
                                $pembayaran->bukti_pembayaran
                            ) }}"
                            alt="Bukti pembayaran"
                            class="koreksi-bukti-image"
                        >

                    </div>


                    <a
                        href="{{ asset(
                            'storage/' .
                            $pembayaran->bukti_pembayaran
                        ) }}"
                        target="_blank"
                        class="koreksi-btn-bukti"
                    >
                        <i data-lucide="maximize-2"></i>
                        Lihat Bukti Lebih Besar
                    </a>

                @else

                    <div class="koreksi-bukti-empty">

                        <div class="koreksi-bukti-empty-icon">
                            <i data-lucide="image-off"></i>
                        </div>

                        <strong>
                            Bukti pembayaran tidak tersedia
                        </strong>

                        <span>
                            Tidak ada file bukti pembayaran.
                        </span>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- FORM KOREKSI --}}
    <div class="koreksi-form-card">

        <div class="koreksi-form-header">

            <div class="koreksi-card-title">

                <div class="koreksi-icon">
                    <i data-lucide="pencil-line"></i>
                </div>

                <div>
                    <h3>
                        Koreksi Data Pembayaran
                    </h3>

                    <p>
                        Masukkan nominal sesuai dengan bukti pembayaran.
                    </p>
                </div>

            </div>

        </div>


        <form
            action="{{ route(
                'admin.pembayaran.update',
                $pembayaran->id
            ) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            {{-- NOMINAL --}}
            <div class="koreksi-form-group">

                <label
                    for="nominal"
                    class="koreksi-form-label"
                >
                    Nominal Pembayaran

                    <span class="koreksi-required">
                        *
                    </span>
                </label>


                <div class="koreksi-nominal-wrapper">

                    <span class="koreksi-rupiah">
                        Rp
                    </span>

                    <input
                        type="number"
                        name="nominal"
                        id="nominal"
                        class="koreksi-nominal-input"
                        value="{{ old(
                            'nominal',
                            $pembayaran->nominal
                        ) }}"
                        min="1"
                        max="{{ $sisaTagihan }}"
                        placeholder="Masukkan nominal pembayaran"
                        required
                    >

                </div>


                <small class="koreksi-help">

                    Maksimal nominal yang dapat dicatat:

                    <strong>
                        Rp {{ number_format(
                            $sisaTagihan,
                            0,
                            ',',
                            '.'
                        ) }}
                    </strong>

                </small>

            </div>


            {{-- METODE --}}
            <div class="koreksi-form-group">

                <label
                    for="metode"
                    class="koreksi-form-label"
                >
                    Metode Pembayaran

                    <span class="koreksi-required">
                        *
                    </span>
                </label>


                <select
                    name="metode"
                    id="metode"
                    class="koreksi-select"
                    required
                >

                    <option value="">
                        Pilih metode pembayaran
                    </option>


                    <option
                        value="transfer"
                        {{ old(
                            'metode',
                            $pembayaran->metode
                        ) === 'transfer'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Transfer Bank
                    </option>


                    <option
                        value="qris"
                        {{ old(
                            'metode',
                            $pembayaran->metode
                        ) === 'qris'
                            ? 'selected'
                            : ''
                        }}
                    >
                        QRIS
                    </option>

                </select>

            </div>


            {{-- INFORMASI --}}
            <div class="koreksi-info">

                <div class="koreksi-info-icon">
                    <i data-lucide="info"></i>
                </div>

                <div>

                    <strong>
                        Periksa nominal pada bukti pembayaran
                    </strong>

                    <p>
                        Pastikan nominal yang dimasukkan sesuai dengan
                        jumlah yang terlihat pada bukti pembayaran.
                        Setelah dikoreksi, pembayaran tetap berstatus
                        <strong>Menunggu Persetujuan</strong>
                        sampai admin menyetujuinya.
                    </p>

                </div>

            </div>


            {{-- ACTION --}}
            <div class="koreksi-actions">

                <a
                    href="{{ route('admin.pembayaran.index') }}"
                    class="koreksi-btn koreksi-btn-back"
                >
                    <i data-lucide="arrow-left"></i>
                    Kembali
                </a>


                <button
                    type="submit"
                    class="koreksi-btn koreksi-btn-save"
                >
                    <i data-lucide="save"></i>
                    Simpan Koreksi
                </button>

            </div>

        </form>

    </div>

</div>


{{-- LUCIDE --}}
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>

@endsection