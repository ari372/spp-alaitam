@extends('layouts.admin')

@section('title', 'Edit Kelas')

@section('page-title', 'Edit Kelas')

@push('styles')
    @vite('resources/css/admin/kelas.css')
@endpush

@section('content')

<div class="kelas-form-container">

    <div class="kelas-form-card">

        <h2>
            Edit Kelas
        </h2>

        <p class="kelas-form-description">
            Ubah data kelas
        </p>


        {{-- ERROR --}}

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


        {{-- FORM --}}

        <form
            method="POST"
            action="{{ route(
                'admin.kelas.update',
                $kela->id
            ) }}"
        >

            @csrf

            @method('PUT')


            {{-- NAMA KELAS --}}

            <div class="kelas-form-group">

                <label for="nama_kelas">
                    Nama Kelas
                </label>

                <input
                    type="text"
                    id="nama_kelas"
                    name="nama_kelas"
                    value="{{ old(
                        'nama_kelas',
                        $kela->nama_kelas
                    ) }}"
                    required
                >

            </div>


            {{-- BUTTON --}}

            <div class="kelas-form-actions">

                <button
                    type="submit"
                    class="kelas-btn-simpan"
                >
                    Update Kelas
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