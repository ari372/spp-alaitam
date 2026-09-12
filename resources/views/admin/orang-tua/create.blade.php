@extends('layouts.admin')

@section('title', 'Tambah Orang Tua')

@section('content')

@push('styles')
    @vite('resources/css/admin/orang-tua.css')
@endpush

<div class="page-title">

    <h1>
        Tambah Orang Tua
    </h1>

    <p>
        Buat data orang tua sekaligus akun untuk login.
    </p>

</div>


<div class="orang-tua-card">

    <div class="orang-tua-info">

        <strong>
            Informasi
        </strong>

        <br>

        Email dan password yang diisi di bawah
        akan digunakan orang tua untuk login ke sistem.

    </div>


    @if($errors->any())

        <div class="orang-tua-error orang-tua-error-list">

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


        {{-- NAMA --}}

        <div class="orang-tua-form-group">

            <label for="nama">
                Nama Orang Tua
            </label>

            <input
                type="text"
                id="nama"
                name="nama"
                value="{{ old('nama') }}"
                placeholder="Masukkan nama orang tua"
                required
            >

            @error('nama')

                <div class="orang-tua-error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- EMAIL --}}

        <div class="orang-tua-form-group">

            <label for="email">
                Email Login
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="contoh@email.com"
                required
            >

            @error('email')

                <div class="orang-tua-error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- PASSWORD --}}

        <div class="orang-tua-form-group">

            <label for="password">
                Password Login
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Minimal 6 karakter"
                required
            >

            @error('password')

                <div class="orang-tua-error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- KONFIRMASI PASSWORD --}}

        <div class="orang-tua-form-group">

            <label for="password_confirmation">
                Konfirmasi Password
            </label>

            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Ulangi password"
                required
            >

            @error('password_confirmation')

                <div class="orang-tua-error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- NO HP --}}

        <div class="orang-tua-form-group">

            <label for="no_hp">
                No. HP
            </label>

            <input
                type="text"
                id="no_hp"
                name="no_hp"
                value="{{ old('no_hp') }}"
                placeholder="08xxxxxxxxxx"
            >

            @error('no_hp')

                <div class="orang-tua-error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- ALAMAT --}}

        <div class="orang-tua-form-group">

            <label for="alamat">
                Alamat
            </label>

            <textarea
                id="alamat"
                name="alamat"
                placeholder="Masukkan alamat orang tua"
            >{{ old('alamat') }}</textarea>

            @error('alamat')

                <div class="orang-tua-error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- BUTTON --}}

        <div class="orang-tua-button-area">

            <button
                type="submit"
                class="orang-tua-btn orang-tua-btn-simpan"
            >
                Simpan Orang Tua
            </button>


            <a
                href="{{ route('admin.orang-tua.index') }}"
                class="orang-tua-btn orang-tua-btn-kembali"
            >
                Kembali
            </a>

        </div>

    </form>

</div>

@endsection