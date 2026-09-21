
@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('page-title', 'Data Siswa')

@push('styles')
    @vite('resources/css/admin/siswa.css')
@endpush

@section('content')

<div class="siswa-container">

    {{-- HEADER --}}
    <div class="siswa-header">

        <div class="siswa-header-content">

            <div class="siswa-header-icon">
                <i data-lucide="users"></i>
            </div>

            <div>
                <h2>Data Siswa</h2>

                <p>
                    Daftar data siswa SMP Plus Al-I'tam
                </p>
            </div>

        </div>

        {{-- SEARCH DAN TOMBOL TAMBAH --}}
        <div class="siswa-header-actions">

            {{-- FORM PENCARIAN --}}
            <form
                action="{{ route('admin.siswa.index') }}"
                method="GET"
                class="siswa-search-form"
            >

                <div class="siswa-search-input-wrapper">

                    <i data-lucide="search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, NIS, kelas..."
                        aria-label="Cari data siswa"
                    >

                    @if(request('search'))

                        <a
                            href="{{ route('admin.siswa.index') }}"
                            class="siswa-search-clear"
                            title="Reset pencarian"
                            aria-label="Reset pencarian"
                        >
                            <i data-lucide="x"></i>
                        </a>

                    @endif

                </div>

                <button
                    type="submit"
                    class="siswa-search-button"
                >
                    <i data-lucide="search"></i>
                    <span>Cari</span>
                </button>

            </form>


            {{-- TOMBOL TAMBAH SISWA --}}
            <a
                href="{{ route('admin.siswa.create') }}"
                class="btn-tambah"
            >
                <i data-lucide="user-plus"></i>
                <span>Tambah Siswa</span>
            </a>

        </div>

    </div>


    {{-- INFORMASI HASIL PENCARIAN --}}
    @if(request('search'))

        <div class="siswa-search-result">

            <div class="siswa-search-result-content">

                <i data-lucide="search"></i>

                <span>
                    Hasil pencarian untuk:
                    <strong>"{{ request('search') }}"</strong>
                </span>

            </div>

            <a
                href="{{ route('admin.siswa.index') }}"
                class="siswa-search-reset"
            >
                <i data-lucide="rotate-ccw"></i>
                Reset
            </a>

        </div>

    @endif


    {{-- ALERT SUCCESS --}}
    @if(session('success'))

        <div class="alert success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ALERT ERROR --}}
    @if(session('error'))

        <div class="alert error">

            <i data-lucide="circle-alert"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="alert error">

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
    <div class="siswa-table-card">

        {{-- TABLE HEADER --}}
        <div class="siswa-table-header">

            <div>

                <h3>
                    Daftar Siswa
                </h3>

                <p>
                    Data siswa yang terdaftar dalam sistem
                </p>

            </div>

            <div class="siswa-total">

                <i data-lucide="users"></i>

                <span>
                    {{ $siswa->count() }} Siswa
                </span>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="table-wrapper">

            <table class="siswa-table">

                <thead>

                    <tr>

                        <th class="col-no">
                            No
                        </th>

                        <th>
                            NIS
                        </th>

                        <th>
                            Nama
                        </th>

                        <th>
                            Jenis Kelamin
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th>
                            Orang Tua
                        </th>

                        <th class="col-aksi">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($siswa as $item)

                        <tr>

                            {{-- NOMOR --}}
                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>


                            {{-- NIS --}}
                            <td>

                                <span class="siswa-nis">
                                    {{ $item->nis }}
                                </span>

                            </td>


                            {{-- NAMA SISWA --}}
                            <td>

                                <div class="siswa-name">

                                    <div class="siswa-avatar">

                                        {{ strtoupper(substr($item->nama, 0, 1)) }}

                                    </div>

                                    <span>
                                        {{ $item->nama }}
                                    </span>

                                </div>

                            </td>


                            {{-- JENIS KELAMIN --}}
                            <td>

                                @if($item->jenis_kelamin === 'L')

                                    <span class="gender-badge gender-laki">

                                        <i data-lucide="user-round"></i>

                                        Laki-laki

                                    </span>

                                @else

                                    <span class="gender-badge gender-perempuan">

                                        <i data-lucide="user-round"></i>

                                        Perempuan

                                    </span>

                                @endif

                            </td>


                            {{-- KELAS --}}
                            <td>

                                <span class="kelas-badge">

                                    <i data-lucide="school"></i>

                                    {{ $item->kelas->nama_kelas ?? '-' }}

                                </span>

                            </td>


                            {{-- ORANG TUA --}}
                            <td>

                                <span class="orangtua-text">

                                    <i data-lucide="user-round"></i>

                                    {{ $item->orangTua->nama ?? '-' }}

                                </span>

                            </td>


                            {{-- AKSI --}}
                            <td>

                                <div class="aksi">

                                    {{-- DETAIL --}}
                                    <a
                                        href="{{ route('admin.siswa.show', $item->id) }}"
                                        class="aksi-btn aksi-detail"
                                        data-tooltip="Lihat detail siswa"
                                        aria-label="Lihat detail siswa"
                                    >

                                        <i data-lucide="info"></i>

                                    </a>


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('admin.siswa.edit', $item->id) }}"
                                        class="aksi-btn aksi-edit"
                                        data-tooltip="Edit data siswa"
                                        aria-label="Edit data siswa"
                                    >

                                        <i data-lucide="square-pen"></i>

                                    </a>


                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route('admin.siswa.destroy', $item->id) }}"
                                        method="POST"
                                        class="aksi-form"
                                        onsubmit="return confirm('Yakin ingin menghapus siswa ini?')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="aksi-btn aksi-hapus"
                                            data-tooltip="Hapus data siswa"
                                            aria-label="Hapus data siswa"
                                        >

                                            <i data-lucide="trash-2"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        {{-- DATA KOSONG --}}
                        <tr>

                            <td
                                colspan="7"
                                class="empty"
                            >

                                <div class="empty-content">

                                    <div class="empty-icon">

                                        <i data-lucide="users-round"></i>

                                    </div>


                                    @if(request('search'))

                                        <strong>
                                            Data siswa tidak ditemukan
                                        </strong>

                                        <span>
                                            Tidak ada siswa yang sesuai dengan pencarian Anda.
                                        </span>

                                        <a
                                            href="{{ route('admin.siswa.index') }}"
                                            class="empty-button"
                                        >

                                            <i data-lucide="rotate-ccw"></i>

                                            Reset Pencarian

                                        </a>

                                    @else

                                        <strong>
                                            Belum ada data siswa
                                        </strong>

                                        <span>
                                            Silakan tambahkan data siswa terlebih dahulu.
                                        </span>

                                        <a
                                            href="{{ route('admin.siswa.create') }}"
                                            class="empty-button"
                                        >

                                            <i data-lucide="user-plus"></i>

                                            Tambah Siswa

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


{{-- LUCIDE ICON --}}
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