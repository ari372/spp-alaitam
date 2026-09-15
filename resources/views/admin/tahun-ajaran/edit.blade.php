@extends('layouts.admin')

@section('title', 'Edit Tahun Ajaran')

@section('content')

@vite('resources/css/admin/tahun-ajaran.css')

<div class="tahun-ajaran-page">

    {{-- HEADER --}}
    <div class="page-header">

        <div class="page-header-text">

            <h1>
                Edit Tahun Ajaran
            </h1>

            <p>
                Ubah data tahun ajaran yang digunakan dalam sistem pembayaran.
            </p>

        </div>

    </div>


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="alert alert-error">

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
            action="{{ route('admin.tahun-ajaran.update', $tahunAjaran->id) }}"
            method="POST"
        >

            @csrf
            @method('PUT')


            {{-- TAHUN AJARAN --}}
            <div class="form-group">

                <label for="nama">
                    Tahun Ajaran
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old('nama', $tahunAjaran->nama) }}"
                    placeholder="Contoh: 2027/2028"
                    required
                >

                <small>
                    Masukkan tahun ajaran dengan format 2027/2028.
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
                    value="{{ old(
                        'tanggal_mulai',
                        $tahunAjaran->tanggal_mulai
                            ? $tahunAjaran->tanggal_mulai->format('Y-m-d')
                            : ''
                    ) }}"
                    required
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
                    value="{{ old(
                        'tanggal_selesai',
                        $tahunAjaran->tanggal_selesai
                            ? $tahunAjaran->tanggal_selesai->format('Y-m-d')
                            : ''
                    ) }}"
                    required
                >

            </div>


            {{-- ACTION --}}
            <div class="form-actions">

                <a
                    href="{{ route('admin.tahun-ajaran.index') }}"
                    class="btn btn-secondary"
                >
                    Kembali
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection