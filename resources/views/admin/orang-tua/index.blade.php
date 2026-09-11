@extends('layouts.admin')

@section('title', 'Data Orang Tua')

@section('page-title', 'Data Orang Tua')

@section('content')

<div class="orang-tua-page">

    {{-- HEADER --}}

    <div class="orang-tua-header">

        <div>

            <h2>
                Data Orang Tua
            </h2>

            <p>
                Daftar data orang tua siswa
            </p>

        </div>

        <a
            href="{{ route('admin.orang-tua.create') }}"
            class="btn-tambah-orang-tua"
        >
            + Tambah Orang Tua
        </a>

    </div>


    {{-- SUCCESS --}}

    @if(session('success'))

        <div class="orang-tua-alert orang-tua-alert-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- ERROR --}}

    @if(session('error'))

        <div class="orang-tua-alert orang-tua-alert-error">

            {{ session('error') }}

        </div>

    @endif


    {{-- TABLE --}}

    <div class="orang-tua-card">

        <div class="orang-tua-table-wrapper">

            <table class="orang-tua-table">

                <thead>

                    <tr>

                        <th>
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

                        <th class="text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($orangTua as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->nama }}
                            </td>

                            <td>
    {{ $item->user?->email ?? '-' }}
</td>

                            <td>
                                {{ $item->no_hp ?? '-' }}
                            </td>

                            <td class="text-center">

                                <span class="jumlah-anak">
                                    {{ $item->siswa_count }}
                                </span>

                            </td>

                            <td class="text-center">

                                <div class="orang-tua-actions">

                                    {{-- DETAIL --}}

                                    <a
                                        href="{{ route(
                                            'admin.orang-tua.show',
                                            $item->id
                                        ) }}"
                                        class="btn-orang-tua btn-detail-orang-tua"
                                    >
                                        Detail
                                    </a>


                                    {{-- EDIT --}}

                                    <a
                                        href="{{ route(
                                            'admin.orang-tua.edit',
                                            $item->id
                                        ) }}"
                                        class="btn-orang-tua btn-edit-orang-tua"
                                    >
                                        Edit
                                    </a>


                                    {{-- HAPUS --}}

                                    <form
                                        action="{{ route(
                                            'admin.orang-tua.destroy',
                                            $item->id
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus orang tua ini?')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn-orang-tua btn-hapus-orang-tua"
                                        >
                                            Hapus
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
                                Belum ada data orang tua.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
