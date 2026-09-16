@extends('layouts.admin')

@section('title', 'Koreksi Pembayaran')

@section('page-title', 'Koreksi Pembayaran')

@section('content')

@vite('resources/css/admin/pembayaran.css')

<div class="koreksi-container">

    {{-- HEADER --}}
    <div class="koreksi-header">

        <div>
            <h2>
                Koreksi Pembayaran
            </h2>

            <p>
                Periksa bukti pembayaran dan tentukan nominal pembayaran
                sebelum pembayaran disetujui.
            </p>
        </div>

    </div>


    {{-- ERROR SESSION --}}
    @if(session('error'))

        <div class="pembayaran-alert pembayaran-alert-error">
            {{ session('error') }}
        </div>

    @endif


    {{-- SUCCESS SESSION --}}
    @if(session('success'))

        <div class="pembayaran-alert pembayaran-alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="pembayaran-alert pembayaran-alert-error">

            <ul style="margin: 0 0 0 18px;">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================
         INFORMASI + BUKTI
    ========================================== --}}

    <div class="koreksi-grid">


        {{-- INFORMASI PEMBAYARAN --}}
        <div class="koreksi-card">

            <div class="koreksi-card-header">

                <div class="koreksi-icon">
                    💳
                </div>

                <div>

                    <h3>
                        Informasi Pembayaran
                    </h3>

                    <p>
                        Data pembayaran dari orang tua
                    </p>

                </div>

            </div>


            <div class="koreksi-detail-list">


                {{-- SISWA --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        Nama Siswa
                    </span>

                    <strong class="koreksi-value">
                        {{ $tagihan->siswa->nama ?? '-' }}
                    </strong>

                </div>


                {{-- NIS --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        NIS
                    </span>

                    <strong class="koreksi-value">
                        {{ $tagihan->siswa->nis ?? '-' }}
                    </strong>

                </div>


                {{-- TAHUN AJARAN --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        Tahun Ajaran
                    </span>

                    <strong class="koreksi-value">
                        {{ $tagihan->tahunAjaran->nama ?? '-' }}
                    </strong>

                </div>


                {{-- KATEGORI --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
                        Kategori
                    </span>

                    <strong class="koreksi-value koreksi-green">
                        {{ $tagihan->kategori->nama ?? '-' }}
                    </strong>

                </div>


                {{-- TOTAL TAGIHAN --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
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
                        Metode Pembayaran
                    </span>

                    <strong class="koreksi-value">

                        {{ strtoupper(
                            $pembayaran->metode ?? '-'
                        ) }}

                    </strong>

                </div>


                {{-- TANGGAL --}}
                <div class="koreksi-detail-item">

                    <span class="koreksi-label">
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

                <div class="koreksi-icon">
                    🧾
                </div>

                <div>

                    <h3>
                        Bukti Pembayaran
                    </h3>

                    <p>
                        Periksa nominal pada bukti pembayaran
                    </p>

                </div>

            </div>


            <div class="koreksi-bukti">

                @if($pembayaran->bukti_pembayaran)

                    <img
                        src="{{ asset(
                            'storage/' .
                            $pembayaran->bukti_pembayaran
                        ) }}"
                        alt="Bukti pembayaran"
                        class="koreksi-bukti-image"
                    >

                    <a
                        href="{{ asset(
                            'storage/' .
                            $pembayaran->bukti_pembayaran
                        ) }}"
                        target="_blank"
                        class="koreksi-btn-bukti"
                    >
                        🔍 Lihat Bukti Lebih Besar
                    </a>

                @else

                    <div class="koreksi-bukti-empty">

                        <div>
                            📄
                        </div>

                        <p>
                            Bukti pembayaran tidak tersedia.
                        </p>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================
         FORM KOREKSI
    ========================================== --}}

    <div class="koreksi-form-card">


        {{-- FORM HEADER --}}
        <div class="koreksi-form-header">

            <div class="koreksi-icon">
                ✏️
            </div>

            <div>

                <h3>
                    Koreksi Data Pembayaran
                </h3>

                <p>
                    Masukkan nominal sesuai dengan bukti pembayaran
                    yang telah diperiksa.
                </p>

            </div>

        </div>


        {{-- FORM --}}
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


            {{-- METODE PEMBAYARAN --}}
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
                    ℹ
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


                {{-- KEMBALI --}}
                <a
                    href="{{ route('admin.pembayaran.index') }}"
                    class="koreksi-btn koreksi-btn-back"
                >
                    ← Kembali
                </a>


                {{-- SIMPAN --}}
                <button
                    type="submit"
                    class="koreksi-btn koreksi-btn-save"
                >
                    ✓ Simpan Koreksi
                </button>

            </div>

        </form>

    </div>

</div>

@endsection