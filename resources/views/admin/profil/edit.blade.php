@extends('layouts.admin')

@section('title', 'Edit Profil')

@section('page-title', 'Edit Profil')

@push('styles')
    @vite('resources/css/admin/profil.css')
@endpush

@section('content')

<div class="profil-page">

    {{-- HEADER --}}
    <div class="profil-header">

        <div>
            <h2>
                Edit Profil
            </h2>

            <p>
                Perbarui informasi akun administrator sistem pembayaran SPP
            </p>
        </div>

        <a
            href="{{ route('admin.profil') }}"
            class="profil-header-back"
        >
            <i data-lucide="arrow-left"></i>
            Kembali
        </a>

    </div>


    {{-- ERROR VALIDASI --}}
    @if($errors->any())

        <div class="profil-alert profil-alert-error">

            <i data-lucide="circle-alert"></i>

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


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="profil-alert profil-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- FORM CARD --}}
    <div class="profil-edit-card">

        {{-- CARD HEADER --}}
        <div class="profil-edit-header">

            <div class="profil-section-icon">

                <i data-lucide="user-cog"></i>

            </div>

            <div>

                <h3>
                    Pengaturan Profil
                </h3>

                <p>
                    Ubah informasi akun administrator
                </p>

            </div>

        </div>


        <form
            action="{{ route('admin.profil.update') }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            {{-- NAMA --}}
            <div class="profil-form-group">

                <label
                    for="name"
                    class="profil-form-label"
                >
                    Nama Lengkap
                    <span class="profil-required">*</span>
                </label>

                <div class="profil-input-wrapper">

                    <i data-lucide="user"></i>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', Auth::user()->name) }}"
                        placeholder="Masukkan nama lengkap"
                        required
                    >

                </div>

            </div>


            {{-- EMAIL --}}
            <div class="profil-form-group">

                <label
                    for="email"
                    class="profil-form-label"
                >
                    Email
                    <span class="profil-required">*</span>
                </label>

                <div class="profil-input-wrapper">

                    <i data-lucide="mail"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', Auth::user()->email) }}"
                        placeholder="Masukkan email"
                        required
                    >

                </div>

            </div>


            {{-- PASSWORD --}}
            <div class="profil-form-group">

                <label
                    for="password"
                    class="profil-form-label"
                >
                    Password Baru
                </label>

                <div class="profil-input-wrapper">

                    <i data-lucide="lock-keyhole"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password baru"
                    >

                    <button
                        type="button"
                        class="profil-password-toggle"
                        data-target="password"
                        aria-label="Tampilkan password"
                    >
                        <i data-lucide="eye"></i>
                    </button>

                </div>

                <small class="profil-form-help">
                    Kosongkan jika tidak ingin mengubah password.
                </small>

            </div>


            {{-- KONFIRMASI PASSWORD --}}
            <div class="profil-form-group">

                <label
                    for="password_confirmation"
                    class="profil-form-label"
                >
                    Konfirmasi Password Baru
                </label>

                <div class="profil-input-wrapper">

                    <i data-lucide="lock-keyhole"></i>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password baru"
                    >

                    <button
                        type="button"
                        class="profil-password-toggle"
                        data-target="password_confirmation"
                        aria-label="Tampilkan password"
                    >
                        <i data-lucide="eye"></i>
                    </button>

                </div>

            </div>


            {{-- INFO --}}
            <div class="profil-edit-info">

                <div class="profil-edit-info-icon">

                    <i data-lucide="info"></i>

                </div>

                <div>

                    <strong>
                        Informasi perubahan
                    </strong>

                    <p>
                        Nama dan email akan langsung diperbarui.
                        Password hanya berubah jika kamu mengisinya.
                    </p>

                </div>

            </div>


            {{-- ACTION --}}
            <div class="profil-edit-actions">

                <a
                    href="{{ route('admin.profil') }}"
                    class="profil-btn profil-btn-cancel"
                >
                    <i data-lucide="x"></i>
                    Batal
                </a>

                <button
                    type="submit"
                    class="profil-btn profil-btn-save"
                >
                    <i data-lucide="save"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        if (typeof lucide !== 'undefined') {

            lucide.createIcons();

        }

        document.querySelectorAll('.profil-password-toggle').forEach(function (button) {

            button.addEventListener('click', function () {

                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);

                if (!input) {
                    return;
                }

                const icon = this.querySelector('[data-lucide]');

                if (input.type === 'password') {

                    input.type = 'text';

                    this.setAttribute(
                        'aria-label',
                        'Sembunyikan password'
                    );

                    if (icon) {
                        icon.setAttribute('data-lucide', 'eye-off');
                    }

                } else {

                    input.type = 'password';

                    this.setAttribute(
                        'aria-label',
                        'Tampilkan password'
                    );

                    if (icon) {
                        icon.setAttribute('data-lucide', 'eye');
                    }

                }

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

            });

        });

    });
</script>

@endsection