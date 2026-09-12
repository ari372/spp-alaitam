@extends('layouts.admin')

@section('title', 'Detail Orang Tua')

@section('page-title', 'Detail Orang Tua')

@push('styles')
    @vite('resources/css/admin/orang-tua.css')
@endpush

@section('content')

<div class="orang-tua-detail-content">

    <div class="orang-tua-detail-card">

        <h2>
            Detail Orang Tua
        </h2>


        <div class="orang-tua-detail-row">

            <div class="orang-tua-detail-label">
                Nama
            </div>

            <div>
                {{ $orangTua->nama }}
            </div>

        </div>


        <div class="orang-tua-detail-row">

            <div class="orang-tua-detail-label">
                Email
            </div>

            <div>
                {{ $orangTua->email ?? '-' }}
            </div>

        </div>


        <div class="orang-tua-detail-row">

            <div class="orang-tua-detail-label">
                No HP
            </div>

            <div>
                {{ $orangTua->no_hp ?? '-' }}
            </div>

        </div>


        <div class="orang-tua-detail-row">

            <div class="orang-tua-detail-label">
                Alamat
            </div>

            <div>
                {{ $orangTua->alamat ?? '-' }}
            </div>

        </div>

    </div>


    <div class="orang-tua-detail-card">

        <h3>
            Anak / Siswa
        </h3>


        @if($orangTua->siswa->count() > 0)

            <div class="orang-tua-detail-table-wrapper">

                <table class="orang-tua-detail-table">

                    <thead>

                        <tr>

                            <th>
                                NIS
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Kelas
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($orangTua->siswa as $siswa)

                            <tr>

                                <td>
                                    {{ $siswa->nis }}
                                </td>

                                <td>
                                    {{ $siswa->nama }}
                                </td>

                                <td>
                                    {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <p class="orang-tua-detail-empty">
                Orang tua ini belum memiliki
                data siswa.
            </p>

        @endif

    </div>


    <a
        href="{{ route('admin.orang-tua.index') }}"
        class="orang-tua-detail-btn"
    >
        Kembali
    </a>

</div>

@endsection