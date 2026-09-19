@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('page-title', 'Data Kelas')

@push('styles')
    @vite('resources/css/admin/kelas.css')
@endpush

@section('content')

<div class="kelas-page">

    {{-- HEADER --}}
    <div class="kelas-header">

        <div class="kelas-header-content">

            <div class="kelas-header-icon">
                <i data-lucide="school"></i>
            </div>

            <div>
                <h2>
                    Data Kelas
                </h2>

                <p>
                    Daftar kelas siswa SMP Plus Al-I'tam
                </p>
            </div>

        </div>


        <a
            href="{{ route('admin.kelas.create') }}"
            class="btn-tambah-kelas"
        >

            <i data-lucide="plus"></i>

            Tambah Kelas

        </a>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="kelas-alert kelas-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR --}}
    @if(session('error'))

        <div class="kelas-alert kelas-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="kelas-alert kelas-alert-error">

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


    {{-- CARD --}}
    <div class="kelas-card">

        {{-- CARD HEADER --}}
        <div class="kelas-card-header">

            <div>

                <h3>
                    Daftar Kelas
                </h3>

                <p>
                    Data kelas yang tersedia dalam sistem
                </p>

            </div>


            <div class="kelas-total">

                <i data-lucide="school"></i>

                <span>
                    {{ $kelas->count() }} Kelas
                </span>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="kelas-table-wrapper">

            <table class="kelas-table">

                <thead>

                    <tr>

                        <th class="col-no">
                            No
                        </th>

                        <th>
                            Nama Kelas
                        </th>

                        <th class="col-jumlah">
                            Jumlah Siswa
                        </th>

                        <th class="col-aksi">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($kelas as $index => $item)

                        <tr>

                            {{-- NO --}}
                            <td class="text-center">

                                {{ $index + 1 }}

                            </td>


                            {{-- NAMA KELAS --}}
                            <td>

                                <div class="kelas-name">

                                    <div class="kelas-avatar">

                                        <i data-lucide="school"></i>

                                    </div>

                                    <span>
                                        {{ $item->nama_kelas }}
                                    </span>

                                </div>

                            </td>


                            {{-- JUMLAH SISWA --}}
                            <td class="text-center">

                                <span class="kelas-jumlah">

                                    <i data-lucide="users-round"></i>

                                    {{ $item->siswa()->count() }}

                                    Siswa

                                </span>

                            </td>


                            {{-- AKSI --}}
                            <td>

                                <div class="kelas-actions">

                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route(
                                            'admin.kelas.edit',
                                            $item->id
                                        ) }}"
                                        class="kelas-action-btn kelas-edit"
                                        data-tooltip="Edit data kelas"
                                        aria-label="Edit data kelas"
                                    >

                                        <i data-lucide="square-pen"></i>

                                    </a>


                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route(
                                            'admin.kelas.destroy',
                                            $item->id
                                        ) }}"
                                        method="POST"
                                        class="kelas-action-form"
                                        onsubmit="
                                            return confirm(
                                                'Yakin ingin menghapus kelas ini?'
                                            )
                                        "
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="kelas-action-btn kelas-delete"
                                            data-tooltip="Hapus data kelas"
                                            aria-label="Hapus data kelas"
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
                                colspan="4"
                                class="kelas-empty"
                            >

                                <div class="kelas-empty-content">

                                    <div class="kelas-empty-icon">

                                        <i data-lucide="school"></i>

                                    </div>

                                    <strong>
                                        Belum ada data kelas
                                    </strong>

                                    <span>
                                        Silakan tambahkan data kelas terlebih dahulu.
                                    </span>

                                    <a
                                        href="{{ route('admin.kelas.create') }}"
                                        class="kelas-empty-button"
                                    >

                                        <i data-lucide="plus"></i>

                                        Tambah Kelas

                                    </a>

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
<script src="https://unpkg.com/lucide@latest"></script>

<script>

    document.addEventListener('DOMContentLoaded', function () {

        lucide.createIcons();

    });

</script>

@endsection