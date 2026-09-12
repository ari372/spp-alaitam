@extends('layouts.admin')

@section('title', 'Edit Orang Tua')

@section('page-title', 'Edit Orang Tua')

@push('styles')
    @vite('resources/css/admin/orang-tua.css')
@endpush

@section('content')

<div class="orang-tua-edit-content">

    <div class="orang-tua-edit-card">

        <h2>
            Edit Orang Tua
        </h2>


        <div class="orang-tua-edit-info">

            Kosongkan password jika tidak ingin
            mengubah password login.

        </div>


        @if($errors->any())

            <div class="orang-tua-edit-error">

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
            action="{{ route(
                'admin.orang-tua.update',
                $orangTua->id
            ) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            {{-- NAMA --}}

            <div class="orang-tua-edit-form-group">

                <label for="nama">
                    Nama Orang Tua
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old(
                        'nama',
                        $orangTua->nama
                    ) }}"
                    required
                >

            </div>


            {{-- EMAIL --}}

            <div class="orang-tua-edit-form-group">

                <label for="email">
                    Email Login
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old(
                        'email',
                        $orangTua->email
                    ) }}"
                    required
                >

            </div>


            {{-- PASSWORD --}}

            <div class="orang-tua-edit-form-group">

                <label for="password">
                    Password Baru
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Kosongkan jika tidak diubah"
                >

            </div>


            {{-- KONFIRMASI PASSWORD --}}

            <div class="orang-tua-edit-form-group">

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


            {{-- NO HP --}}

            <div class="orang-tua-edit-form-group">

                <label for="no_hp">
                    No HP
                </label>

                <input
                    type="text"
                    id="no_hp"
                    name="no_hp"
                    value="{{ old(
                        'no_hp',
                        $orangTua->no_hp
                    ) }}"
                >

            </div>


            {{-- ALAMAT --}}

            <div class="orang-tua-edit-form-group">

                <label for="alamat">
                    Alamat
                </label>

                <textarea
                    id="alamat"
                    name="alamat"
                >{{ old(
                    'alamat',
                    $orangTua->alamat
                ) }}</textarea>

            </div>


            {{-- BUTTON --}}

            <div class="orang-tua-edit-actions">

                <button
                    type="submit"
                    class="orang-tua-edit-btn orang-tua-edit-btn-simpan"
                >
                    Update
                </button>


                <a
                    href="{{ route('admin.orang-tua.index') }}"
                    class="orang-tua-edit-btn orang-tua-edit-btn-kembali"
                >
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

@endsection