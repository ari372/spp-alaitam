@extends('layouts.orangtua')

@section('title', 'Pembayaran Tagihan')

@section('content')

<style>
    .payment-page {
        max-width: 1100px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        color: #0f5132;
        font-size: 34px;
        margin-bottom: 8px;
    }

    .page-header p {
        color: #777;
        font-size: 16px;
    }

    .alert {
        padding: 15px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .alert-error {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f1aeb5;
    }

    .alert-success {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #a3cfbb;
    }

    .payment-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 25px;
    }

    .card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .06);
    }

    .card-title {
        color: #0f5132;
        font-size: 21px;
        font-weight: 700;
        margin-bottom: 22px;
    }

    .detail-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #eee;
        gap: 20px;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #777;
        font-size: 14px;
    }

    .detail-value {
        color: #333;
        font-size: 15px;
        font-weight: 600;
        text-align: right;
    }

    .detail-value.green {
        color: #0f5132;
    }

    .detail-value.nominal {
        font-size: 20px;
        color: #0f5132;
    }

    .payment-method-info {
        background: #f5f7f6;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 5px solid #0f5132;
    }

    .payment-method-info h3 {
        color: #0f5132;
        font-size: 17px;
        margin-bottom: 15px;
    }

    .bank-box {
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 12px;
    }

    .bank-box:last-child {
        margin-bottom: 0;
    }

    .bank-name {
        color: #0f5132;
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 6px;
    }

    .account-number {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #222;
        margin-bottom: 5px;
    }

    .account-owner {
        color: #666;
        font-size: 13px;
    }

    .qris-box {
        text-align: center;
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        margin-top: 12px;
    }

    .qris-box img {
        width: 190px;
        max-width: 100%;
        height: auto;
        margin-bottom: 10px;
    }

    .qris-title {
        color: #0f5132;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .qris-description {
        color: #777;
        font-size: 13px;
    }

    .form-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .06);
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        transition: .2s;
        background: white;
    }

    .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, .12);
    }

    .form-help {
        display: block;
        color: #777;
        font-size: 12px;
        margin-top: 7px;
    }

    .nominal-wrapper {
        position: relative;
    }

    .nominal-wrapper span {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #555;
        font-weight: 600;
        pointer-events: none;
    }

    .nominal-input {
        padding-left: 45px;
    }

    .method-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .method-option {
        position: relative;
    }

    .method-option input {
        position: absolute;
        opacity: 0;
    }

    .method-option label {
        display: block;
        padding: 16px;
        border: 2px solid #ddd;
        border-radius: 10px;
        cursor: pointer;
        transition: .2s;
        text-align: center;
        font-weight: 700;
        color: #555;
    }

    .method-option label:hover {
        border-color: #198754;
    }

    .method-option input:checked + label {
        border-color: #198754;
        background: #eaf6ef;
        color: #0f5132;
    }

    .method-icon {
        display: block;
        font-size: 24px;
        margin-bottom: 6px;
    }

    .file-box {
        border: 2px dashed #ccc;
        border-radius: 10px;
        padding: 20px;
        background: #fafafa;
    }

    .file-box input {
        width: 100%;
    }

    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-top: 25px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 22px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: .2s;
    }

    .btn-back {
        background: #6c757d;
        color: white;
    }

    .btn-back:hover {
        background: #5c636a;
        color: white;
    }

    .btn-submit {
        background: #198754;
        color: white;
    }

    .btn-submit:hover {
        background: #157347;
    }

    .required {
        color: #dc3545;
    }

    .blocked-info {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffecb5;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    @media (max-width: 800px) {

        .payment-grid {
            grid-template-columns: 1fr;
        }

        .method-options {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
        }

    }
</style>


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

                {{-- Ganti file ini dengan QRIS sekolah --}}

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