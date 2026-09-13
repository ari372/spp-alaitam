@extends('layouts.admin')

@section('title', 'Tagihan')
@section('page-title', 'Tagihan')

@push('styles')
    @vite('resources/css/admin/tagihan.css')
@endpush

@section('content')

<div class="tagihan-container">

    {{-- HEADER --}}
    <div class="tagihan-header">

        <div class="tagihan-header-title">

            <h2>Data Tagihan</h2>

            <p>
                Daftar tagihan siswa berdasarkan kelompok siswa
            </p>

        </div>

    </div>


    {{-- TOOLBAR PENCARIAN DAN BUAT TAGIHAN --}}
    <div class="tagihan-toolbar">

        {{-- FORM PENCARIAN --}}
        <form
            action="{{ route('admin.tagihan.index') }}"
            method="GET"
            class="tagihan-search-form"
        >

            <div class="tagihan-search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    class="tagihan-search-input"
                    placeholder="Cari nama siswa, NIS, kategori, atau tahun ajaran..."
                >

            </div>

            <button
                type="submit"
                class="tagihan-search-button"
            >
                <i class="bi bi-search"></i>
                <span>Cari</span>
            </button>

            @if(request('search'))

                <a
                    href="{{ route('admin.tagihan.index') }}"
                    class="tagihan-reset-button"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Reset</span>
                </a>

            @endif

        </form>


        {{-- TOMBOL BUAT TAGIHAN --}}
        <a
            href="{{ route('admin.tagihan.create') }}"
            class="btn-tambah-tagihan"
        >
            <i class="bi bi-plus-lg"></i>
            <span>Buat Tagihan</span>
        </a>

    </div>


    {{-- INFORMASI HASIL PENCARIAN --}}
    @if(request('search'))

        <div class="tagihan-search-result">

            <i class="bi bi-info-circle"></i>

            Menampilkan hasil pencarian untuk:

            <strong>
                "{{ request('search') }}"
            </strong>

        </div>

    @endif


    {{-- SUCCESS ALERT --}}
    @if(session('success'))

        <div class="alert alert-success">

            <i class="bi bi-check-circle"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR ALERT --}}
    @if(session('error'))

        <div class="alert alert-error">

            <i class="bi bi-exclamation-triangle"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="alert alert-error">

            <i class="bi bi-exclamation-triangle"></i>

            <ul>
                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach
            </ul>

        </div>

    @endif


    {{-- TABLE CARD --}}
    <div class="tagihan-table-card">

        <div class="tagihan-table-wrapper">

            <table class="tagihan-table">

                <thead>

                    <tr>
                        <th class="kolom-no">No</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Daftar Tagihan</th>
                        <th>Total Tagihan</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($tagihan as $siswaId => $daftarTagihan)

                        @php

                            $tagihanPertama = $daftarTagihan->first();

                            $siswa = $tagihanPertama->siswa;

                            $totalTagihan = $daftarTagihan->sum('nominal');

                        @endphp

                        <tr>

                            {{-- NOMOR --}}
                            <td class="no">
                                {{ $loop->iteration }}
                            </td>


                            {{-- DATA SISWA --}}
                            <td>

                                <div class="tagihan-siswa-info">

                                    <strong>
                                        {{ $siswa?->nama ?? '-' }}
                                    </strong>

                                    @if($siswa?->nis)

                                        <small>
                                            NIS: {{ $siswa->nis }}
                                        </small>

                                    @endif

                                </div>

                            </td>


                            {{-- KELAS --}}
                            <td>
                                {{ $siswa?->kelas?->nama_kelas ?? '-' }}
                            </td>


                            {{-- DAFTAR SEMUA TAGIHAN --}}
                            <td>

                                <div class="tagihan-list">

                                    @foreach($daftarTagihan as $item)

                                        <div class="tagihan-list-item">

                                            {{-- INFORMASI TAGIHAN --}}
                                            <div class="tagihan-list-info">

                                                {{-- KATEGORI --}}
                                                <span class="tagihan-category">

                                                    {{ $item->kategori?->nama ?? '-' }}

                                                </span>


                                                {{-- TAHUN AJARAN --}}
                                                <small class="tagihan-item-tahun">

                                                    Tahun ajaran:

                                                    {{ $item->tahunAjaran?->nama ?? '-' }}

                                                </small>


                                                {{-- NOMINAL --}}
                                                <span class="tagihan-item-nominal">

                                                    Rp
                                                    {{ number_format($item->nominal ?? 0, 0, ',', '.') }}

                                                </span>


                                                {{-- JATUH TEMPO --}}
                                                <small class="tagihan-item-tempo">

                                                    Jatuh tempo:

                                                    @if($item->jatuh_tempo)

                                                        {{ \Carbon\Carbon::parse($item->jatuh_tempo)->format('d-m-Y') }}

                                                    @else

                                                        -

                                                    @endif

                                                </small>

                                            </div>


                                            {{-- AKSI TAGIHAN --}}
                                            <div class="tagihan-item-actions">

                                                {{-- DETAIL --}}
                                                <a
                                                    href="{{ route('admin.tagihan.show', $item->id) }}"
                                                    class="btn-icon btn-detail"
                                                    title="Lihat detail tagihan"
                                                    aria-label="Lihat detail tagihan"
                                                >
                                                    <i class="bi bi-info-circle"></i>
                                                </a>


                                                {{-- EDIT --}}
                                                <a
                                                    href="{{ route('admin.tagihan.edit', $item->id) }}"
                                                    class="btn-icon btn-edit"
                                                    title="Edit tagihan"
                                                    aria-label="Edit tagihan"
                                                >
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>


                                                {{-- HAPUS --}}
                                                <form
                                                    action="{{ route('admin.tagihan.destroy', $item->id) }}"
                                                    method="POST"
                                                    class="form-hapus"
                                                    onsubmit="return confirm('Yakin ingin menghapus tagihan ini?')"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn-icon btn-hapus"
                                                        title="Hapus tagihan"
                                                        aria-label="Hapus tagihan"
                                                    >
                                                        <i class="bi bi-trash3"></i>
                                                    </button>

                                                </form>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </td>


                            {{-- TOTAL TAGIHAN --}}
                            <td>

                                <div class="tagihan-total-wrapper">

                                    <strong class="tagihan-total">

                                        Rp
                                        {{ number_format($totalTagihan, 0, ',', '.') }}

                                    </strong>

                                    <small class="tagihan-total-count">

                                        {{ $daftarTagihan->count() }}
                                        jenis tagihan

                                    </small>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="tagihan-empty"
                            >

                                <i class="bi bi-inbox"></i>

                                <span>

                                    @if(request('search'))

                                        Data tagihan tidak ditemukan.

                                    @else

                                        Belum ada tagihan.

                                    @endif

                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection