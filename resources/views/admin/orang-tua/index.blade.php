@extends('layouts.admin')

@section('title', 'Data Orang Tua')

@section('page-title', 'Data Orang Tua')

@push('styles')
    @vite('resources/css/admin/orang-tua.css')
@endpush

@section('content')

<div class="orang-tua-page">

    {{-- HEADER --}}
    <div class="orang-tua-header">

        <div class="orang-tua-header-content">

            <div class="orang-tua-header-icon">
                <i data-lucide="users"></i>
            </div>

            <div>
                <h2>Data Orang Tua</h2>

                <p>
                    Daftar data orang tua siswa SMP Plus Al-I'tam
                </p>
            </div>

        </div>


        {{-- SEARCH DAN TAMBAH --}}
        <div class="orang-tua-header-actions">

            {{-- FORM PENCARIAN --}}
            <form
                action="{{ route('admin.orang-tua.index') }}"
                method="GET"
                class="orang-tua-search-form"
            >

                <div class="orang-tua-search-input-wrapper">

                    <i data-lucide="search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, email, HP..."
                        aria-label="Cari data orang tua"
                    >

                    @if(request('search'))

                        <a
                            href="{{ route('admin.orang-tua.index') }}"
                            class="orang-tua-search-clear"
                            title="Hapus pencarian"
                            aria-label="Hapus pencarian"
                        >
                            <i data-lucide="x"></i>
                        </a>

                    @endif

                </div>


                <button
                    type="submit"
                    class="orang-tua-search-button"
                >

                    <i data-lucide="search"></i>

                    <span>Cari</span>

                </button>

            </form>


            {{-- TOMBOL TAMBAH --}}
            <a
                href="{{ route('admin.orang-tua.create') }}"
                class="btn-tambah-orang-tua"
            >

                <i data-lucide="user-plus"></i>

                <span>
                    Tambah Orang Tua
                </span>

            </a>

        </div>

    </div>


    {{-- INFORMASI HASIL PENCARIAN --}}
    @if(request('search'))

        <div class="orang-tua-search-result">

            <i data-lucide="search"></i>

            <span>

                Hasil pencarian untuk:

                <strong>
                    "{{ request('search') }}"
                </strong>

            </span>


            <a
                href="{{ route('admin.orang-tua.index') }}"
                class="orang-tua-search-reset"
            >

                <i data-lucide="rotate-ccw"></i>

                Reset

            </a>

        </div>

    @endif


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="orang-tua-alert orang-tua-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR --}}
    @if(session('error'))

        <div class="orang-tua-alert orang-tua-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="orang-tua-alert orang-tua-alert-error">

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


    {{-- TABLE CARD --}}
    <div class="orang-tua-card">

        {{-- TABLE HEADER --}}
        <div class="orang-tua-table-header">

            <div>

                <h3>
                    Daftar Orang Tua
                </h3>

                <p>
                    Data orang tua yang terdaftar dalam sistem
                </p>

            </div>


            <div class="orang-tua-total">

                <i data-lucide="users-round"></i>

                <span>
                    {{ $orangTua->count() }} Orang Tua
                </span>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="orang-tua-table-wrapper">

            <table class="orang-tua-table">

                <thead>

                    <tr>

                        <th class="col-no">
                            No
                        </th>

                        <th>
                            Nama
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            No HP
                        </th>

                        <th class="text-center">
                            Jumlah Anak
                        </th>

                        <th class="col-aksi">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($orangTua as $item)

                        <tr>

                            {{-- NO --}}
                            <td class="text-center">

                                {{ $loop->iteration }}

                            </td>


                            {{-- NAMA --}}
                            <td>

                                <div class="orang-tua-name">

                                    <div class="orang-tua-avatar">

                                        {{ strtoupper(
                                            substr($item->nama ?? 'O', 0, 1)
                                        ) }}

                                    </div>

                                    <span>
                                        {{ $item->nama }}
                                    </span>

                                </div>

                            </td>


                            {{-- EMAIL --}}
                            <td>

                                <span class="orang-tua-email">

                                    <i data-lucide="mail"></i>

                                    {{ $item->user?->email ?? '-' }}

                                </span>

                            </td>


                            {{-- NO HP --}}
                            <td>

                                <span class="orang-tua-phone">

                                    <i data-lucide="phone"></i>

                                    {{ $item->no_hp ?? '-' }}

                                </span>

                            </td>


                            {{-- JUMLAH ANAK --}}
                            <td class="text-center">

                                <span class="jumlah-anak">

                                    <i data-lucide="users-round"></i>

                                    {{ $item->siswa_count }}

                                    Anak

                                </span>

                            </td>


                            {{-- AKSI --}}
                            <td>

                                <div class="orang-tua-actions">


                                    {{-- DETAIL --}}
                                    <a
                                        href="{{ route(
                                            'admin.orang-tua.show',
                                            $item->id
                                        ) }}"
                                        class="orang-tua-action-btn orang-tua-detail"
                                        data-tooltip="Lihat detail orang tua"
                                        aria-label="Lihat detail orang tua"
                                    >

                                        <i data-lucide="info"></i>

                                    </a>


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route(
                                            'admin.orang-tua.edit',
                                            $item->id
                                        ) }}"
                                        class="orang-tua-action-btn orang-tua-edit"
                                        data-tooltip="Edit data orang tua"
                                        aria-label="Edit data orang tua"
                                    >

                                        <i data-lucide="square-pen"></i>

                                    </a>


                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route(
                                            'admin.orang-tua.destroy',
                                            $item->id
                                        ) }}"
                                        method="POST"
                                        class="orang-tua-action-form"
                                        onsubmit="return confirm('Yakin ingin menghapus orang tua ini?')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="orang-tua-action-btn orang-tua-delete"
                                            data-tooltip="Hapus data orang tua"
                                            aria-label="Hapus data orang tua"
                                        >

                                            <i data-lucide="trash-2"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="orang-tua-empty"
                            >

                                <div class="orang-tua-empty-content">

                                    <div class="orang-tua-empty-icon">

                                        <i data-lucide="users-round"></i>

                                    </div>


                                    @if(request('search'))

                                        <strong>
                                            Data orang tua tidak ditemukan
                                        </strong>

                                        <span>
                                            Tidak ada data yang sesuai dengan pencarian Anda.
                                        </span>

                                        <a
                                            href="{{ route('admin.orang-tua.index') }}"
                                            class="orang-tua-empty-button"
                                        >

                                            <i data-lucide="rotate-ccw"></i>

                                            Reset Pencarian

                                        </a>

                                    @else

                                        <strong>
                                            Belum ada data orang tua
                                        </strong>

                                        <span>
                                            Silakan tambahkan data orang tua terlebih dahulu.
                                        </span>

                                        <a
                                            href="{{ route('admin.orang-tua.create') }}"
                                            class="orang-tua-empty-button"
                                        >

                                            <i data-lucide="user-plus"></i>

                                            Tambah Orang Tua

                                        </a>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- LUCIDE --}}
@push('scripts')

    <script src="https://unpkg.com/lucide@latest"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            if (typeof lucide !== 'undefined') {

                lucide.createIcons();

            }

        });

    </script>

@endpush

@endsection