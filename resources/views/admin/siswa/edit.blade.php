@extends('layouts.admin')

@section('title', 'Edit Siswa')

@section('page-title', 'Edit Siswa')

@push('styles')
    @vite('resources/css/admin/siswa.css')
@endpush

@section('content')

<div class="siswa-edit-content">

    <div class="siswa-edit-card">

        <h2>
            Edit Data Siswa
        </h2>


        {{-- ERROR --}}

        @if ($errors->any())

            <div class="siswa-edit-error">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- FORM --}}

        <form
            action="{{ route(
                'admin.siswa.update',
                $siswa->id
            ) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            {{-- NIS --}}

            <div class="siswa-edit-form-group">

                <label>
                    NIS
                </label>

                <input
                    type="text"
                    name="nis"
                    value="{{ old(
                        'nis',
                        $siswa->nis
                    ) }}"
                    required
                >

            </div>


            {{-- NAMA --}}

            <div class="siswa-edit-form-group">

                <label>
                    Nama Siswa
                </label>

                <input
                    type="text"
                    name="nama"
                    value="{{ old(
                        'nama',
                        $siswa->nama
                    ) }}"
                    required
                >

            </div>


            {{-- JENIS KELAMIN --}}

            <div class="siswa-edit-form-group">

                <label>
                    Jenis Kelamin
                </label>

                <select
                    name="jenis_kelamin"
                    required
                >

                    <option
                        value="L"
                        {{ old(
                            'jenis_kelamin',
                            $siswa->jenis_kelamin
                        ) == 'L'
                            ? 'selected'
                            : '' }}
                    >
                        Laki-laki
                    </option>

                    <option
                        value="P"
                        {{ old(
                            'jenis_kelamin',
                            $siswa->jenis_kelamin
                        ) == 'P'
                            ? 'selected'
                            : '' }}
                    >
                        Perempuan
                    </option>

                </select>

            </div>


            {{-- ALAMAT --}}

            <div class="siswa-edit-form-group">

                <label>
                    Alamat
                </label>

                <textarea
                    name="alamat"
                >{{ old(
                    'alamat',
                    $siswa->alamat
                ) }}</textarea>

            </div>


            {{-- KELAS --}}

            <div class="siswa-edit-form-group">

                <label>
                    Kelas
                </label>

                <select
                    name="kelas_id"
                    required
                >

                    @foreach ($kelas as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ old(
                                'kelas_id',
                                $siswa->kelas_id
                            ) == $item->id
                                ? 'selected'
                                : '' }}
                        >
                            {{ $item->nama_kelas }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ORANG TUA --}}

            <div class="siswa-edit-form-group">

                <label>
                    Orang Tua / Wali
                </label>

                <select
                    name="orang_tua_id"
                    required
                >

                    @foreach ($orangTua as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ old(
                                'orang_tua_id',
                                $siswa->orang_tua_id
                            ) == $item->id
                                ? 'selected'
                                : '' }}
                        >
                            {{ $item->nama }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- BUTTON --}}

            <div class="siswa-edit-actions">

                <button
                    type="submit"
                    class="siswa-edit-btn siswa-edit-btn-simpan"
                >
                    Update
                </button>

                <a
                    href="{{ route('admin.siswa.index') }}"
                    class="siswa-edit-btn siswa-edit-btn-kembali"
                >
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

@endsection