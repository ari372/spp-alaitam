@extends('layouts.admin')

@section('title', 'Tambah Siswa')

@section('page-title', 'Tambah Siswa')

@push('styles')
    @vite('resources/css/admin/siswa.css')
@endpush

@section('content')

<div class="siswa-create-content">

    <div class="siswa-create-card">

        {{-- HEADER --}}
        <div class="siswa-create-header">

            <div>
                <h2>
                    Tambah Data Siswa
                </h2>

                <p>
                    Tambahkan data siswa secara manual melalui formulir berikut.
                </p>
            </div>

            <div class="siswa-create-header-actions">

                <a
                    href="{{ route('admin.siswa.import') }}"
                    class="siswa-create-btn siswa-create-btn-import"
                >
                    <i data-lucide="file-spreadsheet"></i>
                    Import Excel
                </a>

                <a
                    href="{{ route('admin.siswa.index') }}"
                    class="siswa-create-btn siswa-create-btn-kembali"
                >
                    <i data-lucide="arrow-left"></i>
                    Kembali
                </a>

            </div>

        </div>

        {{-- ERROR VALIDASI --}}
        @if ($errors->any())

            <div class="siswa-create-error">

                <div class="siswa-create-error-title">
                    <i data-lucide="alert-circle"></i>
                    Terdapat kesalahan
                </div>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>

            </div>

        @endif

        {{-- FORM TAMBAH SISWA --}}
        <form
            action="{{ route('admin.siswa.store') }}"
            method="POST"
        >

            @csrf

            {{-- NIS --}}
            <div class="siswa-create-form-group">

                <label for="nis">
                    NIS
                </label>

                <input
                    type="text"
                    id="nis"
                    name="nis"
                    value="{{ old('nis') }}"
                    placeholder="Masukkan NIS"
                    required
                >

                @error('nis')
                    <small class="siswa-create-field-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            {{-- NAMA SISWA --}}
            <div class="siswa-create-form-group">

                <label for="nama">
                    Nama Siswa
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old('nama') }}"
                    placeholder="Masukkan nama siswa"
                    required
                >

                @error('nama')
                    <small class="siswa-create-field-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            {{-- JENIS KELAMIN --}}
            <div class="siswa-create-form-group">

                <label for="jenis_kelamin">
                    Jenis Kelamin
                </label>

                <select
                    id="jenis_kelamin"
                    name="jenis_kelamin"
                    required
                >

                    <option value="">
                        -- Pilih Jenis Kelamin --
                    </option>

                    <option
                        value="L"
                        {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}
                    >
                        Laki-laki
                    </option>

                    <option
                        value="P"
                        {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}
                    >
                        Perempuan
                    </option>

                </select>

                @error('jenis_kelamin')
                    <small class="siswa-create-field-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            {{-- ALAMAT --}}
            <div class="siswa-create-form-group">

                <label for="alamat">
                    Alamat
                </label>

                <textarea
                    id="alamat"
                    name="alamat"
                    placeholder="Masukkan alamat siswa"
                >{{ old('alamat') }}</textarea>

                @error('alamat')
                    <small class="siswa-create-field-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            {{-- KELAS --}}
            <div class="siswa-create-form-group">

                <label for="kelas_id">
                    Kelas
                </label>

                <select
                    id="kelas_id"
                    name="kelas_id"
                    required
                >

                    <option value="">
                        -- Pilih Kelas --
                    </option>

                    @foreach ($kelas as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ old('kelas_id') == $item->id ? 'selected' : '' }}
                        >
                            {{ $item->nama_kelas }}
                        </option>

                    @endforeach

                </select>

                @error('kelas_id')
                    <small class="siswa-create-field-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            {{-- ORANG TUA --}}
            <div class="siswa-create-form-group">

                <label for="orang_tua_id">
                    Orang Tua / Wali
                </label>

                <select
                    id="orang_tua_id"
                    name="orang_tua_id"
                    required
                >

                    <option value="">
                        -- Pilih Orang Tua --
                    </option>

                    @foreach ($orangTua as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ old('orang_tua_id') == $item->id ? 'selected' : '' }}
                        >
                            {{ $item->nama }}
                        </option>

                    @endforeach

                </select>

                @error('orang_tua_id')
                    <small class="siswa-create-field-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            {{-- BUTTON --}}
            <div class="siswa-create-actions">

                <button
                    type="submit"
                    class="siswa-create-btn siswa-create-btn-simpan"
                >
                    <i data-lucide="save"></i>
                    Simpan Data
                </button>

                <a
                    href="{{ route('admin.siswa.index') }}"
                    class="siswa-create-btn siswa-create-btn-kembali"
                >
                    <i data-lucide="x"></i>
                    Batal
                </a>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
@endpush