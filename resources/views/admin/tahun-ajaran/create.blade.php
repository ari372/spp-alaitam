@extends('layouts.admin')

@section('title', 'Tambah Tahun Ajaran')

@section('content')

@vite('resources/css/admin/tahun-ajaran.css')

<div class="tahun-ajaran-page">

    {{-- HEADER --}}
    <div class="page-header">

        <div class="page-header-text">

            <h1>Tambah Tahun Ajaran</h1>

            <p>
                Tambahkan tahun ajaran baru ke dalam sistem.
            </p>

        </div>

        <a
            href="{{ route('admin.tahun-ajaran.index') }}"
            class="btn btn-secondary"
        >
            ← Kembali
        </a>

    </div>


    {{-- ERROR VALIDASI --}}
    @if($errors->any())

        <div class="alert alert-error">

            <strong>
                Terjadi kesalahan:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- FORM --}}
    <div class="form-card">

        <form
            action="{{ route('admin.tahun-ajaran.store') }}"
            method="POST"
        >

            @csrf


            {{-- TAHUN AJARAN --}}
            <div class="form-group">

                <label for="nama">
                    Tahun Ajaran
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old('nama') }}"
                    placeholder="Contoh: 2026/2027"
                    required
                >

                <small>
                    Masukkan tahun ajaran dengan format seperti 2026/2027.
                </small>

            </div>


            {{-- TANGGAL MULAI --}}
            <div class="form-group">

                <label for="tanggal_mulai">
                    Tanggal Mulai
                </label>

                <input
                    type="date"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ old('tanggal_mulai') }}"
                >

            </div>


            {{-- TANGGAL SELESAI --}}
            <div class="form-group">

                <label for="tanggal_selesai">
                    Tanggal Selesai
                </label>

                <input
                    type="date"
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="{{ old('tanggal_selesai') }}"
                >

            </div>


            {{-- TOMBOL --}}
            <div class="form-actions">

                <a
                    href="{{ route('admin.tahun-ajaran.index') }}"
                    class="btn btn-secondary"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Tahun Ajaran
                </button>

            </div>

        </form>

    </div>

</div>

@endsection