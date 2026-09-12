@extends('layouts.orangtua')

@section('title', 'Pembayaran Tagihan')

@section('content')

@vite('resources/css/orang-tua/pembayaran.css')

<div class="payment-page">

    {{-- HEADER --}}

    <div class="page-header">

        <h1>
            Pembayaran Tagihan
        </h1>

        <p>
            Silakan lakukan pembayaran dan upload bukti pembayaran.
        </p>

    </div>


    {{-- ERROR --}}

    @if(session('error'))

        <div class="alert alert-error">
            {{ session('error') }}
        </div>

    @endif


    {{-- SUCCESS --}}

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- VALIDATION ERROR --}}

    @if($errors->any())

        <div class="alert alert-error">

            <ul style="margin-left: 18px;">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- DETAIL + PEMBAYARAN --}}

    <div class="payment-grid">


        {{-- DETAIL TAGIHAN --}}

        <div class="card">

            <h2 class="card-title">
                Detail Tagihan
            </h2>

            <div class="detail-list">

                <div class="detail-item">

                    <span class="detail-label">
                        Nama Siswa
                    </span>

                    <span class="detail-value">
                        {{ $tagihan->siswa->nama ?? '-' }}
                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        NIS
                    </span>

                    <span class="detail-value">
                        {{ $tagihan->siswa->nis ?? '-' }}
                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Tahun Ajaran
                    </span>

                    <span class="detail-value">
                        {{ $tagihan->tahunAjaran->nama ?? '-' }}
                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Kategori
                    </span>

                    <span class="detail-value green">
                        {{ $tagihan->kategori->nama ?? '-' }}
                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Total Tagihan
                    </span>

                    <span class="detail-value nominal">
                        Rp {{ number_format(
                            $tagihan->nominal,
                            0,
                            ',',
                            '.'
                        ) }}
                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Sudah Dibayar
                    </span>

                    <span class="detail-value">
                        Rp {{ number_format(
                            $totalDibayar,
                            0,
                            ',',
                            '.'
                        ) }}
                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Sisa Tagihan
                    </span>

                    <span class="detail-value nominal">
                        Rp {{ number_format(
                            $sisaTagihan,
                            0,
                            ',',
                            '.'
                        ) }}
                    </span>

                </div>

            </div>

        </div>


        {{-- DETAIL REKENING --}}

        <div class="card">

            <h2 class="card-title">
                Detail Pembayaran
            </h2>

            <div class="payment-method-info">

                <h3>
                    Transfer Bank
                </h3>

                <div class="bank-box">

                    <div class="bank-name">
                        BRI
                    </div>

                    <div class="account-number">
                        1234567890
                    </div>

                    <div class="account-owner">
                        a.n. SMP Plus Al-I'tam
                    </div>

                </div>


                <div class="bank-box">

                    <div class="bank-name">
                        BCA
                    </div>

                    <div class="account-number">
                        1234567890
                    </div>

                    <div class="account-owner">
                        a.n. SMP Plus Al-I'tam
                    </div>

                </div>

            </div>


            <div class="qris-box">

                <div class="qris-title">
                    Pembayaran QRIS
                </div>

                @if(file_exists(public_path('images/qris.png')))

                    <img
                        src="{{ asset('images/qris.png') }}"
                        alt="QRIS SMP Plus Al-I'tam"
                    >

                @else

                    <div style="
                        padding:35px 15px;
                        color:#777;
                        border:1px dashed #ccc;
                        border-radius:8px;
                        margin-bottom:10px;
                    ">

                        QRIS sekolah belum tersedia

                    </div>

                @endif

                <div class="qris-description">
                    Silakan scan QRIS sekolah untuk melakukan pembayaran.
                </div>

            </div>

        </div>

    </div>


    {{-- FORM PEMBAYARAN --}}

    <div class="form-card">

        <h2 class="card-title">
            Form Pembayaran
        </h2>


        <form
            action="{{ route(
                'orangtua.pembayaran.store',
                ['tagihan' => $tagihan->id]
            ) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf


            {{-- NOMINAL --}}

            <div class="form-group">

                <label
                    for="nominal"
                    class="form-label"
                >

                    Nominal Pembayaran

                    <span class="required">*</span>

                </label>


                <div class="nominal-wrapper">

                    <span>
                        Rp
                    </span>

                    <input
                        type="number"
                        name="nominal"
                        id="nominal"
                        class="form-control nominal-input"
                        value="{{ old('nominal', $sisaTagihan) }}"
                        min="1"
                        max="{{ $sisaTagihan }}"
                        required
                    >

                </div>


                <small class="form-help">

                    Maksimal pembayaran:

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

            <div class="form-group">

                <label class="form-label">

                    Metode Pembayaran

                    <span class="required">*</span>

                </label>


                <div class="method-options">

                    <div class="method-option">

                        <input
                            type="radio"
                            name="metode"
                            id="transfer"
                            value="transfer"
                            {{ old('metode') === 'transfer' ? 'checked' : '' }}
                            required
                        >

                        <label for="transfer">

                            <span class="method-icon">
                                🏦
                            </span>

                            Transfer Bank

                        </label>

                    </div>


                    <div class="method-option">

                        <input
                            type="radio"
                            name="metode"
                            id="qris"
                            value="qris"
                            {{ old('metode') === 'qris' ? 'checked' : '' }}
                        >

                        <label for="qris">

                            <span class="method-icon">
                                ▦
                            </span>

                            QRIS

                        </label>

                    </div>

                </div>

            </div>


            {{-- BUKTI PEMBAYARAN --}}

            <div class="form-group">

                <label
                    for="bukti_pembayaran"
                    class="form-label"
                >

                    Bukti Pembayaran

                    <span class="required">*</span>

                </label>


                <div class="file-box">

                    <input
                        type="file"
                        name="bukti_pembayaran"
                        id="bukti_pembayaran"
                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                        required
                    >

                    <small class="form-help">

                        Format JPG, JPEG, PNG.
                        Maksimal 2 MB.

                    </small>

                </div>

            </div>


            {{-- ACTION --}}

            <div class="action-buttons">

                <a
                    href="{{ route('orangtua.dashboard') }}"
                    class="btn btn-back"
                >
                    ← Kembali
                </a>


                <button
                    type="submit"
                    class="btn btn-submit"
                >
                    Kirim Pembayaran
                </button>

            </div>

        </form>

    </div>

</div>

@endsection