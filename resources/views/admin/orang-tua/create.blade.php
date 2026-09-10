@extends('layouts.admin')

@section('title', 'Tambah Orang Tua')

@section('content')

<style>

    .page-title {
        margin-bottom: 25px;
    }

    .page-title h1 {
        color: #0f5132;
        font-size: 30px;
        margin-bottom: 5px;
    }

    .page-title p {
        color: #777;
    }

    .card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 800px;
        box-shadow: 0 5px 20px rgba(0,0,0,.06);
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
    }

    input,
    textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-sizing: border-box;
        font-size: 14px;
    }

    textarea {
        min-height: 100px;
        resize: vertical;
    }

    input:focus,
    textarea:focus {
        outline: none;
        border-color: #0f5132;
    }

    .error {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .info {
        background: #e9f5ee;
        color: #0f5132;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .button-area {
        margin-top: 25px;
        display: flex;
        gap: 10px;
    }

    .btn {
        display: inline-block;
        padding: 11px 18px;
        border-radius: 8px;
        border: none;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
    }

    .btn-simpan {
        background: #198754;
        color: white;
    }

    .btn-simpan:hover {
        background: #157347;
    }

    .btn-kembali {
        background: #6c757d;
        color: white;
    }

</style>


<div class="page-title">

    <h1>
        Tambah Orang Tua
    </h1>

    <p>
        Buat data orang tua sekaligus akun untuk login.
    </p>

</div>


<div class="card">

    <div class="info">

        <strong>
            Informasi
        </strong>

        <br>

        Email dan password yang diisi di bawah
        akan digunakan orang tua untuk login ke sistem.

    </div>


    @if($errors->any())

        <div class="error" style="margin-bottom:20px;">

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        action="{{ route('admin.orang-tua.store') }}"
        method="POST"
    >

        @csrf


        <!-- NAMA -->

        <div class="form-group">

            <label>
                Nama Orang Tua
            </label>

            <input
                type="text"
                name="nama"
                value="{{ old('nama') }}"
                placeholder="Masukkan nama orang tua"
                required
            >

            @error('nama')

                <div class="error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <!-- EMAIL -->

        <div class="form-group">

            <label>
                Email Login
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="contoh@email.com"
                required
            >

            @error('email')

                <div class="error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <!-- PASSWORD -->

        <div class="form-group">

            <label>
                Password Login
            </label>

            <input
                type="password"
                name="password"
                placeholder="Minimal 6 karakter"
                required
            >

            @error('password')

                <div class="error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <!-- NO HP -->

        <div class="form-group">

            <label>
                No. HP
            </label>

            <input
                type="text"
                name="no_hp"
                value="{{ old('no_hp') }}"
                placeholder="08xxxxxxxxxx"
            >

            @error('no_hp')

                <div class="error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <!-- ALAMAT -->

        <div class="form-group">

            <label>
                Alamat
            </label>

            <textarea
                name="alamat"
                placeholder="Masukkan alamat orang tua"
            >{{ old('alamat') }}</textarea>

            @error('alamat')

                <div class="error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <!-- BUTTON -->

        <div class="button-area">

            <button
                type="submit"
                class="btn btn-simpan"
            >
                Simpan Orang Tua
            </button>


            <a
                href="{{ route('admin.orang-tua.index') }}"
                class="btn btn-kembali"
            >
                Kembali
            </a>

        </div>

    </form>

</div>

@endsection