@extends('layouts.admin')

@section('title', 'Kategori Pembayaran')

@section('page-title', 'Kategori Pembayaran')

@section('content')

<div class="kategori-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="kategori-header">

        <div>
            <h2>
                Kategori Pembayaran
            </h2>

            <p>
                Mengatur jenis pembayaran siswa
            </p>
        </div>

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
         FORM TAMBAH KATEGORI
    ====================================================== --}}

    <div class="kategori-card">

        <h3>
            Tambah Kategori
        </h3>


        <form
            method="POST"
            action="{{ route('admin.kategori.store') }}"
        >

            @csrf


            <div class="kategori-form-grid">


                {{-- NAMA --}}

                <div class="form-group">

                    <label for="nama">
                        Nama Kategori
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Contoh: SPP"
                        value="{{ old('nama') }}"
                        required
                    >

                </div>


                {{-- NOMINAL --}}

                <div class="form-group">

                    <label for="nominal">
                        Nominal
                    </label>

                    <input
                        type="number"
                        id="nominal"
                        name="nominal"
                        placeholder="1350000"
                        min="0"
                        value="{{ old('nominal') }}"
                        required
                    >

                </div>


                {{-- KETERANGAN --}}

                <div class="form-group">

                    <label for="keterangan">
                        Keterangan
                    </label>

                    <input
                        type="text"
                        id="keterangan"
                        name="keterangan"
                        placeholder="Keterangan"
                        value="{{ old('keterangan') }}"
                    >

                </div>

            </div>


            <button
                type="submit"
                class="btn-tambah-kategori"
            >
                + Tambah Kategori
            </button>

        </form>

    </div>


    {{-- =====================================================
         DATA KATEGORI
    ====================================================== --}}

    <div class="kategori-card">

        <h3>
            Daftar Kategori
        </h3>


        <div class="table-wrapper">

            <table class="kategori-table">

                <thead>

                    <tr>

                        <th>
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

                        <th>
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
                            {{ $item->nama }}
                        </td>


                        <td class="nominal">

                            Rp
                            {{ number_format(
                                $item->nominal,
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>


                        <td>

                            {{ $item->keterangan ?? '-' }}

                        </td>


                        <td>

                            <form
                                method="POST"
                                action="{{
                                    route(
                                        'admin.kategori.destroy',
                                        $item->id
                                    )
                                }}"
                                onsubmit="return confirm('Hapus kategori ini?')"
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

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="empty-data"
                        >

                            Belum ada kategori pembayaran.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection