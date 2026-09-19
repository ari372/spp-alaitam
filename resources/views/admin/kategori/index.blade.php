@extends('layouts.admin')

@section('title', 'Kategori Pembayaran')

@section('page-title', 'Kategori Pembayaran')

@push('styles')
    @vite('resources/css/admin/kategori.css')
@endpush

@section('content')

<div class="kategori-page">

    {{-- HEADER --}}

    <div class="kategori-header">

        <div>

            <h2>
                Kategori Pembayaran
            </h2>

            <p>
                Mengatur jenis pembayaran siswa
            </p>

        </div>

        <a
            href="{{ route('admin.kategori.create') }}"
            class="btn-tambah-kategori"
        >
            <i data-lucide="plus"></i>
            Tambah Kategori
        </a>

    </div>


    {{-- SUCCESS --}}

    @if(session('success'))

        <div class="kategori-alert kategori-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR --}}

    @if(session('error'))

        <div class="kategori-alert kategori-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


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


    {{-- DATA KATEGORI --}}

    <div class="kategori-card">

        <div class="kategori-card-header">

            <div class="kategori-card-title">

                <div class="kategori-card-icon">

                    <i data-lucide="tags"></i>

                </div>

                <div>

                    <h3>
                        Daftar Kategori
                    </h3>

                    <p>
                        Daftar jenis pembayaran yang tersedia
                    </p>

                </div>

            </div>

        </div>


        <div class="kategori-table-wrapper">

            <table class="kategori-table">

                <thead>

                    <tr>

                        <th class="col-no">
                            No
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Nominal
                        </th>

                        <th>
                            Keterangan
                        </th>

                        <th class="col-aksi">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($kategori as $index => $item)

                        <tr>

                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>


                            <td>

                                <strong class="kategori-nama">
                                    {{ $item->nama }}
                                </strong>

                            </td>


                            <td>

                                <strong class="kategori-nominal">

                                    Rp
                                    {{ number_format(
                                        $item->nominal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </td>


                            <td>

                                <span class="kategori-keterangan">

                                    {{ $item->keterangan ?? '-' }}

                                </span>

                            </td>


                            <td>

                                <div class="kategori-actions">

                                    {{-- EDIT --}}

                                    <a
                                        href="{{ route(
                                            'admin.kategori.edit',
                                            $item->id
                                        ) }}"
                                        class="kategori-action kategori-action-edit"
                                        title="Edit kategori"
                                    >
                                        <i data-lucide="square-pen"></i>
                                    </a>


                                    {{-- HAPUS --}}

                                    <form
                                        action="{{ route(
                                            'admin.kategori.destroy',
                                            $item->id
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="kategori-action kategori-action-delete"
                                            title="Hapus kategori"
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
                                colspan="5"
                                class="kategori-empty"
                            >

                                <div class="kategori-empty-icon">

                                    <i data-lucide="tags"></i>

                                </div>

                                <strong>
                                    Belum ada kategori pembayaran
                                </strong>

                                <span>
                                    Silakan tambahkan kategori pembayaran baru.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<script src="https://unpkg.com/lucide@latest"></script>

<script>

    document.addEventListener('DOMContentLoaded', function () {

        lucide.createIcons();

    });

</script>

@endsection