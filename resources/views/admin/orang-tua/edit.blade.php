@extends('layouts.admin')

@section('title', 'Edit Orang Tua')

@section('page-title', 'Edit Orang Tua')

@push('styles')
    @vite('resources/css/admin/orang-tua.css')
@endpush

@section('content')

<div class="orang-tua-edit-page">

    {{-- HEADER --}}
    <div class="orang-tua-edit-header">

        <div class="orang-tua-edit-header-content">

            <div class="orang-tua-edit-header-icon">
                <i data-lucide="user-pen"></i>
            </div>

            <div>
                <h1>Edit Orang Tua</h1>
                <p>Perbarui informasi data orang tua siswa.</p>
            </div>

        </div>

        <a href="{{ route('admin.orang-tua.index') }}"
           class="orang-tua-edit-back">

            <i data-lucide="arrow-left"></i>
            <span>Kembali</span>

        </a>

    </div>


    {{-- FORM CARD --}}
    <div class="orang-tua-edit-card">

        <div class="orang-tua-edit-card-header">

            <div class="orang-tua-edit-card-icon">
                <i data-lucide="user-round-pen"></i>
            </div>

            <div>
                <h2>Form Edit Orang Tua</h2>
                <p>
                    Ubah data sesuai informasi yang diperlukan.
                </p>
            </div>

        </div>


        <div class="orang-tua-edit-info">

            <i data-lucide="info"></i>

            <span>
                Kosongkan password jika tidak ingin mengubah
                password login orang tua.
            </span>

        </div>


        @if($errors->any())

            <div class="orang-tua-edit-error">

                <div class="orang-tua-edit-error-title">

                    <i data-lucide="alert-circle"></i>

                    <strong>
                        Terdapat kesalahan pada formulir
                    </strong>

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


        <form
            action="{{ route('admin.orang-tua.update', $orangTua->id) }}"
            method="POST"
            class="orang-tua-edit-form"
        >

            @csrf

            @method('PUT')


            {{-- NAMA --}}

            <div class="orang-tua-edit-form-group">

                <label for="nama">
                    Nama Orang Tua
                    <span>*</span>
                </label>

                <div class="orang-tua-edit-input-wrapper">

                    <i data-lucide="user"></i>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama', $orangTua->nama) }}"
                        placeholder="Masukkan nama orang tua"
                        required
                    >

                </div>

            </div>


            {{-- EMAIL --}}

            <div class="orang-tua-edit-form-group">

                <label for="email">
                    Email Login
                    <span>*</span>
                </label>

                <div class="orang-tua-edit-input-wrapper">

                    <i data-lucide="mail"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
value="{{ old('email', $orangTua->user?->email) }}"
                        placeholder="Masukkan email login"
                        required
                    >

                </div>

            </div>


            {{-- PASSWORD --}}

            <div class="orang-tua-edit-form-group">

                <label for="password">
                    Password Baru
                </label>

                <div class="orang-tua-edit-input-wrapper">

                    <i data-lucide="lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Kosongkan jika tidak diubah"
                    >

                </div>

                <small>
                    Password tidak perlu diisi jika tidak ingin diubah.
                </small>

            </div>


            {{-- KONFIRMASI PASSWORD --}}

            <div class="orang-tua-edit-form-group">

                <label for="password_confirmation">
                    Konfirmasi Password Baru
                </label>

                <div class="orang-tua-edit-input-wrapper">

                    <i data-lucide="lock-keyhole"></i>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password baru"
                    >

                </div>

            </div>


            {{-- NO HP --}}

            <div class="orang-tua-edit-form-group">

                <label for="no_hp">
                    No HP
                </label>

                <div class="orang-tua-edit-input-wrapper">

                    <i data-lucide="phone"></i>

                    <input
                        type="text"
                        id="no_hp"
                        name="no_hp"
                        value="{{ old('no_hp', $orangTua->no_hp) }}"
                        placeholder="Masukkan nomor HP"
                    >

                </div>

            </div>


            {{-- ALAMAT --}}

            <div class="orang-tua-edit-form-group">

                <label for="alamat">
                    Alamat
                </label>

                <div class="orang-tua-edit-textarea-wrapper">

                    <i data-lucide="map-pin"></i>

                    <textarea
                        id="alamat"
                        name="alamat"
                        placeholder="Masukkan alamat orang tua"
                    >{{ old('alamat', $orangTua->alamat) }}</textarea>

                </div>

            </div>


            {{-- BUTTON --}}

            <div class="orang-tua-edit-actions">

                <a
                    href="{{ route('admin.orang-tua.index') }}"
                    class="orang-tua-edit-btn orang-tua-edit-btn-kembali"
                >

                    <i data-lucide="arrow-left"></i>
                    <span>Batal</span>

                </a>


                <button
                    type="submit"
                    class="orang-tua-edit-btn orang-tua-edit-btn-simpan"
                >

                    <i data-lucide="save"></i>
                    <span>Simpan Perubahan</span>

                </button>

            </div>

        </form>

    </div>

</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush

@endsection
