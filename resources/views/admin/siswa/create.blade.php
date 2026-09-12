@extends('layouts.admin')

@section('title', 'Tambah Siswa')

@section('page-title', 'Tambah Siswa')

@push('styles')
    @vite('resources/css/admin/siswa.css')
@endpush

@section('content')

<div class="siswa-create-content">

    <div class="siswa-create-card">

        <h2>
            Tambah Data Siswa
        </h2>


        @if ($errors->any())

            <div class="siswa-create-error">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            action="{{ route('admin.siswa.store') }}"
            method="POST"
        >

            @csrf


            {{-- NIS --}}

            <div class="siswa-create-form-group">

                <label>
                    NIS
                </label>

                <input
                    type="text"
                    name="nis"
                    value="{{ old('nis') }}"
                    placeholder="Masukkan NIS"
                    required
                >

            </div>


            {{-- NAMA --}}

            <div class="siswa-create-form-group">

                <label>
                    Nama Siswa
                </label>

                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama') }}"
                    placeholder="Masukkan nama siswa"
                    required
                >

            </div>


            {{-- JENIS KELAMIN --}}

            <div class="siswa-create-form-group">

                <label>
                    Jenis Kelamin
                </label>

                <select
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

            </div>


            {{-- ALAMAT --}}

            <div class="siswa-create-form-group">

                <label>
                    Alamat
                </label>

                <textarea
                    name="alamat"
                    placeholder="Masukkan alamat siswa"
                >{{ old('alamat') }}</textarea>

            </div>


            {{-- KELAS --}}

            <div class="siswa-create-form-group">

                <label>
                    Kelas
                </label>

                <select
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

            </div>


            {{-- ORANG TUA --}}

            <div class="siswa-create-form-group">

                <label>
                    Orang Tua / Wali
                </label>

                <select
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

            </div>


            {{-- BUTTON --}}

            <div class="siswa-create-actions">

                <button
                    type="submit"
                    class="siswa-create-btn siswa-create-btn-simpan"
                >
                    Simpan
                </button>

                <a
                    href="{{ route('admin.siswa.index') }}"
                    class="siswa-create-btn siswa-create-btn-kembali"
                >
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

@endsection