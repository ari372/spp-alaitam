@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('page-title', 'Edit Kategori')

@push('styles')
    @vite('resources/css/admin/kategori.css')
@endpush

@section('content')

<div class="kategori-form-page">

    {{-- HEADER --}}

    <div class="kategori-form-header">

        <div>

            <h2>
                Edit Kategori
            </h2>

            <p>
                Perbarui informasi kategori pembayaran
            </p>

        </div>

        <a
            href="{{ route('admin.kategori.index') }}"
            class="kategori-back-button"
        >
            <i data-lucide="arrow-left"></i>
            Kembali
        </a>

    </div>


    {{-- VALIDATION ERROR --}}

    @if($errors->any())

        <div class="kategori-alert kategori-alert-error">

            <i data-lucide="triangle-alert"></i>

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


    {{-- FORM EDIT --}}

    <div class="kategori-form-card">

        <div class="kategori-form-card-header">

            <div class="kategori-form-icon">

                <i data-lucide="square-pen"></i>

            </div>

            <div>

                <h3>
                    Informasi Kategori
                </h3>

                <p>
                    Perbarui data kategori pembayaran.
                </p>

            </div>

        </div>


        <form
            action="{{ route(
                'admin.kategori.update',
                $kategori->id
            ) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            {{-- NAMA --}}

            <div class="kategori-form-group">

                <label for="nama">

                    Nama Kategori

                    <span>
                        *
                    </span>

                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old('nama', $kategori->nama) }}"
                    placeholder="Contoh: SPP"
                    required
                >

                @error('nama')

                    <small class="kategori-field-error">
                        {{ $message }}
                    </small>

                @enderror

            </div>


            {{-- NOMINAL --}}

            <div class="kategori-form-group">

                <label for="nominal">

                    Nominal

                    <span>
                        *
                    </span>

                </label>

                <div class="kategori-input-rupiah">

                    <span>
                        Rp
                    </span>

                    <input
                        type="number"
                        id="nominal"
                        name="nominal"
                        value="{{ old(
                            'nominal',
                            (int) $kategori->nominal
                        ) }}"
                        placeholder="1350000"
                        min="0"
                        required
                    >

                </div>

                <small class="kategori-form-help">
                    Masukkan nominal pembayaran dalam rupiah.
                </small>

                @error('nominal')

                    <small class="kategori-field-error">
                        {{ $message }}
                    </small>

                @enderror

            </div>


            {{-- KETERANGAN --}}

            <div class="kategori-form-group">

                <label for="keterangan">
                    Keterangan
                </label>

                <textarea
                    id="keterangan"
                    name="keterangan"
                    rows="4"
                    placeholder="Contoh: Pembayaran SPP siswa"
                >{{ old(
                    'keterangan',
                    $kategori->keterangan
                ) }}</textarea>

                @error('keterangan')

                    <small class="kategori-field-error">
                        {{ $message }}
                    </small>

                @enderror

            </div>


            {{-- ACTION --}}

            <div class="kategori-form-actions">

                <a
                    href="{{ route('admin.kategori.index') }}"
                    class="kategori-btn kategori-btn-cancel"
                >
                    <i data-lucide="arrow-left"></i>
                    Batal
                </a>

                <button
                    type="submit"
                    class="kategori-btn kategori-btn-save"
                >
                    <i data-lucide="save"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>


<script src="https://unpkg.com/lucide@latest"></script>

<script>

    document.addEventListener('DOMContentLoaded', function () {

        lucide.createIcons();

    });

</script>

@endsection