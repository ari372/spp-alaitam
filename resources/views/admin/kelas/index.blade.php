@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('page-title', 'Data Kelas')

@push('styles')
    @vite('resources/css/admin/kelas.css')
@endpush

@section('content')

<div class="kelas-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="kelas-header">

        <div>

            <h2>
                Data Kelas
            </h2>

            <p>
                Daftar kelas siswa
            </p>

        </div>


        <a
            href="{{ route('admin.kelas.create') }}"
            class="btn-tambah-kelas"
        >
            + Tambah Kelas
        </a>

    </div>


    {{-- =====================================================
         PESAN BERHASIL
    ====================================================== --}}

    @if(session('success'))

        <div class="alert-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- =====================================================
         DATA KELAS
    ====================================================== --}}

    <div class="kelas-card">

        <div class="kelas-table-wrapper">

            <table class="kelas-table">

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Nama Kelas
                        </th>

                        <th>
                            Jumlah Siswa
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($kelas as $index => $item)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>
                                {{ $item->nama_kelas }}
                            </td>


                            <td>
                                {{ $item->siswa()->count() }}
                            </td>


                            <td>

                                <div class="kelas-actions">

                                    {{-- EDIT --}}

                                    <a
                                        href="{{ route(
                                            'admin.kelas.edit',
                                            $item->id
                                        ) }}"
                                        class="btn-edit-kelas"
                                    >
                                        Edit
                                    </a>


                                    {{-- HAPUS --}}

                                    <form
                                        action="{{ route(
                                            'admin.kelas.destroy',
                                            $item->id
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm(
                                            'Yakin ingin menghapus kelas ini?'
                                        )"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn-hapus-kelas"
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
                                colspan="4"
                                class="kelas-empty"
                            >
                                Belum ada data kelas.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection