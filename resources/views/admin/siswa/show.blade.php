@extends('layouts.admin')

@section('title', 'Detail Siswa')

@section('page-title', 'Detail Siswa')

@push('styles')
    @vite('resources/css/admin/siswa.css')
@endpush

@section('content')

<div class="siswa-detail-content">

    <div class="siswa-detail-card">

        <h2>
            Detail Siswa
        </h2>


        <div class="siswa-detail-row">

            <div class="siswa-detail-label">
                NIS
            </div>

            <div>
                {{ $siswa->nis }}
            </div>

        </div>


        <div class="siswa-detail-row">

            <div class="siswa-detail-label">
                Nama
            </div>

            <div>
                {{ $siswa->nama }}
            </div>

        </div>


        <div class="siswa-detail-row">

            <div class="siswa-detail-label">
                Jenis Kelamin
            </div>

            <div>

                {{ $siswa->jenis_kelamin === 'L'
                    ? 'Laki-laki'
                    : 'Perempuan' }}

            </div>

        </div>


        <div class="siswa-detail-row">

            <div class="siswa-detail-label">
                Alamat
            </div>

            <div>
                {{ $siswa->alamat ?? '-' }}
            </div>

        </div>


        <div class="siswa-detail-row">

            <div class="siswa-detail-label">
                Kelas
            </div>

            <div>
                {{ $siswa->kelas->nama_kelas ?? '-' }}
            </div>

        </div>


        <div class="siswa-detail-row">

            <div class="siswa-detail-label">
                Orang Tua
            </div>

            <div>
                {{ $siswa->orangTua->nama ?? '-' }}
            </div>

        </div>


        <a
            href="{{ route('admin.siswa.index') }}"
            class="siswa-detail-btn"
        >
            Kembali
        </a>

    </div>

</div>

@endsection