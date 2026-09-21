@extends('layouts.admin')

@section('title', 'Detail Orang Tua')

@section('page-title', 'Detail Orang Tua')

@push('styles')
    @vite('resources/css/admin/orang-tua.css')
@endpush

@section('content')

<div class="orang-tua-detail-page">

    {{-- HEADER --}}
    <div class="orang-tua-detail-header">

        <div class="orang-tua-detail-header-content">

            <div class="orang-tua-detail-header-icon">
                <i data-lucide="user-round"></i>
            </div>

            <div>
                <h1>Detail Orang Tua</h1>
                <p>Informasi lengkap data orang tua dan anak siswa.</p>
            </div>

        </div>

        <a href="{{ route('admin.orang-tua.index') }}"
           class="orang-tua-detail-back">

            <i data-lucide="arrow-left"></i>
            <span>Kembali</span>

        </a>

    </div>


    {{-- INFORMASI ORANG TUA --}}
    <div class="orang-tua-detail-grid">

        <div class="orang-tua-detail-card">

            <div class="orang-tua-detail-card-header">

                <div class="orang-tua-detail-card-icon">
                    <i data-lucide="user-round"></i>
                </div>

                <div>
                    <h2>Informasi Orang Tua</h2>
                    <p>Data pribadi orang tua siswa.</p>
                </div>

            </div>


            <div class="orang-tua-detail-profile">

                <div class="orang-tua-detail-avatar">
                    {{ strtoupper(substr($orangTua->nama, 0, 1)) }}
                </div>

                <div>
                    <h3>{{ $orangTua->nama }}</h3>
                    <span>Orang Tua Siswa</span>
                </div>

            </div>


            <div class="orang-tua-detail-list">

                <div class="orang-tua-detail-item">

                    <div class="orang-tua-detail-item-label">
                        <i data-lucide="user"></i>
                        <span>Nama</span>
                    </div>

                    <strong>
                        {{ $orangTua->nama }}
                    </strong>

                </div>


                <div class="orang-tua-detail-item">

                    <div class="orang-tua-detail-item-label">
                        <i data-lucide="mail"></i>
                        <span>Email</span>
                    </div>

                    <strong>
    {{ $orangTua->user->email ?? '-' }}
</strong>

                </div>


                <div class="orang-tua-detail-item">

                    <div class="orang-tua-detail-item-label">
                        <i data-lucide="phone"></i>
                        <span>No HP</span>
                    </div>

                    <strong>
                        {{ $orangTua->no_hp ?? '-' }}
                    </strong>

                </div>


                <div class="orang-tua-detail-item">

                    <div class="orang-tua-detail-item-label">
                        <i data-lucide="map-pin"></i>
                        <span>Alamat</span>
                    </div>

                    <strong>
                        {{ $orangTua->alamat ?? '-' }}
                    </strong>

                </div>

            </div>


            <div class="orang-tua-detail-card-actions">

                <a href="{{ route('admin.orang-tua.edit', $orangTua->id) }}"
                   class="orang-tua-detail-btn orang-tua-detail-btn-edit">

                    <i data-lucide="square-pen"></i>
                    <span>Edit Data</span>

                </a>

                <a href="{{ route('admin.orang-tua.index') }}"
                   class="orang-tua-detail-btn orang-tua-detail-btn-back">

                    <i data-lucide="arrow-left"></i>
                    <span>Kembali</span>

                </a>

            </div>

        </div>


        {{-- DATA ANAK --}}
        <div class="orang-tua-detail-card">

            <div class="orang-tua-detail-card-header">

                <div class="orang-tua-detail-card-icon">
                    <i data-lucide="users"></i>
                </div>

                <div>
                    <h2>Anak / Siswa</h2>
                    <p>Daftar siswa yang terhubung dengan orang tua.</p>
                </div>

            </div>


            <div class="orang-tua-detail-total">

                <i data-lucide="users"></i>

                <span>
                    {{ $orangTua->siswa->count() }} Siswa
                </span>

            </div>


            @if($orangTua->siswa->count() > 0)

                <div class="orang-tua-detail-table-wrapper">

                    <table class="orang-tua-detail-table">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($orangTua->siswa as $index => $siswa)

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $siswa->nis }}
                                        </strong>
                                    </td>

                                    <td>

                                        <div class="orang-tua-detail-siswa">

                                            <div class="orang-tua-detail-siswa-avatar">
                                                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                            </div>

                                            <span>
                                                {{ $siswa->nama }}
                                            </span>

                                        </div>

                                    </td>

                                    <td>

                                        <span class="orang-tua-detail-kelas">

                                            <i data-lucide="school"></i>

                                            {{ $siswa->kelas->nama_kelas
                                                ?? $siswa->kelas->nama
                                                ?? '-' }}

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="orang-tua-detail-empty">

                    <div class="orang-tua-detail-empty-icon">
                        <i data-lucide="users"></i>
                    </div>

                    <h3>Belum Ada Data Siswa</h3>

                    <p>
                        Orang tua ini belum memiliki data siswa
                        yang terhubung.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush

@endsection