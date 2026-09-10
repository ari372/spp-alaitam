@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('page-title', 'Data Siswa')

@section('content')

<div class="siswa-container">

    <div class="siswa-header">

        <div>
            <h2>Data Siswa</h2>

            <p>
                Daftar data siswa SMP Plus Al-I'tam
            </p>
        </div>

        <a
            href="{{ route('admin.siswa.create') }}"
            class="btn-tambah"
        >
            + Tambah Siswa
        </a>

    </div>


    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert error">
            {{ session('error') }}
        </div>

    @endif


    <div class="siswa-table-card">

        <div class="table-wrapper">

            <table class="siswa-table">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>NIS</th>

                        <th>Nama</th>

                        <th>Jenis Kelamin</th>

                        <th>Kelas</th>

                        <th>Orang Tua</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($siswa as $item)

                        <tr>

                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>


                            <td>
                                {{ $item->nis }}
                            </td>


                            <td>
                                {{ $item->nama }}
                            </td>


                            <td>

                                @if($item->jenis_kelamin === 'L')

                                    Laki-laki

                                @else

                                    Perempuan

                                @endif

                            </td>


                            <td>
                                {{ $item->kelas->nama_kelas ?? '-' }}
                            </td>


                            <td>
                                {{ $item->orangTua->nama ?? '-' }}
                            </td>


                            <td>

                                <div class="aksi">

                                    <a
                                        href="{{ route(
                                            'admin.siswa.show',
                                            $item->id
                                        ) }}"
                                        class="btn-detail"
                                    >
                                        Detail
                                    </a>


                                    <a
                                        href="{{ route(
                                            'admin.siswa.edit',
                                            $item->id
                                        ) }}"
                                        class="btn-edit"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        action="{{ route(
                                            'admin.siswa.destroy',
                                            $item->id
                                        ) }}"
                                        method="POST"
                                        onsubmit="
                                            return confirm(
                                                'Yakin ingin menghapus siswa ini?'
                                            )
                                        "
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn-hapus"
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
                                colspan="7"
                                class="empty"
                            >
                                Belum ada data siswa.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection