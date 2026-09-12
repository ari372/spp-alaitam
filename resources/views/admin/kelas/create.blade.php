@extends('layouts.admin')

@section('title', 'Tambah Kelas')

@section('page-title', 'Tambah Kelas')

@push('styles')
    @vite('resources/css/admin/kelas.css')
@endpush

@section('content')

<div class="kelas-form-container">

    <div class="kelas-form-card">

        <h2>
            Tambah Kelas
        </h2>

        <p class="kelas-form-description">
            Tambahkan data kelas baru
        </p>


        {{-- ERROR VALIDASI --}}

        @if($errors->any())

            <div class="kelas-error">

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- FORM TAMBAH KELAS --}}

        <form
            method="POST"
            action="{{ route('admin.kelas.store') }}"
        >

            @csrf


            {{-- NAMA KELAS --}}

            <div class="kelas-form-group">

                <label for="nama_kelas">
                    Nama Kelas
                </label>

                <input
                    type="text"
                    id="nama_kelas"
                    name="nama_kelas"
                    value="{{ old('nama_kelas') }}"
                    placeholder="Contoh: VII A"
                    required
                >

            </div>


            {{-- BUTTON --}}

            <div class="kelas-form-actions">

                <button
                    type="submit"
                    class="kelas-btn-simpan"
                >
                    Simpan Kelas
                </button>


                <a
                    href="{{ route('admin.kelas.index') }}"
                    class="kelas-btn-kembali"
                >
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

@endsection