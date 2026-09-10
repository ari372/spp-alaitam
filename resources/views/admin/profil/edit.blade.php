@extends('layouts.admin')

@section('title', 'Edit Profil')

@section('page-title', 'Edit Profil')

@section('content')

@vite('resources/css/admin/profil.css')

<div class="profil-container">

    <div class="profil-header">

        <h2>
            Edit Profil
        </h2>

        <p>
            Perbarui informasi akun administrator
        </p>

    </div>


    @if($errors->any())

        <div class="alert alert-error">
            {{ $errors->first() }}
        </div>

    @endif


    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    <div class="profil-card">

        <form
            action="{{ route('admin.profil.update') }}"
            method="POST"
        >

            @csrf
            @method('PUT')


            {{-- NAMA --}}

            <div class="form-group">

                <label for="name">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', Auth::user()->name) }}"
                    placeholder="Masukkan nama lengkap"
                    required
                >

            </div>


            {{-- EMAIL --}}

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', Auth::user()->email) }}"
                    placeholder="Masukkan email"
                    required
                >

            </div>


            {{-- PASSWORD --}}

            <div class="form-group">

                <label for="password">
                    Password Baru
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                >

                <small class="form-help">
                    Isi password hanya jika ingin mengganti password.
                </small>

            </div>


            {{-- KONFIRMASI PASSWORD --}}

            <div class="form-group">

                <label for="password_confirmation">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Ulangi password baru"
                >

            </div>


            {{-- BUTTON --}}

            <div class="form-actions">

                <a
                    href="{{ route('admin.profil') }}"
                    class="btn-batal"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn-simpan"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection