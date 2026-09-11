@extends('layouts.admin')

@section('title', 'Tagihan')

@section('page-title', 'Tagihan')

@section('content')

<div class="tagihan-container">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="tagihan-header">

        <div>

            <h2>
                Data Tagihan
            </h2>

            <p>
                Daftar tagihan siswa
            </p>

        </div>


        <a
            href="{{ route('admin.tagihan.create') }}"
            class="btn-tambah-tagihan"
        >
            + Buat Tagihan
        </a>

    </div>


    {{-- =====================================================
         SUCCESS
    ====================================================== --}}

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- =====================================================
         ERROR
    ====================================================== --}}

    @if(session('error'))

        <div class="alert alert-error">

            {{ session('error') }}

        </div>

    @endif


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <div class="tagihan-table-card">

        <div class="tagihan-table-wrapper">

            <table class="tagihan-table">

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Siswa
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th>
                            Tahun Ajaran
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Nominal
                        </th>

                        <th>
                            Jatuh Tempo
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($tagihan as $index => $item)

                        <tr>

                            {{-- =================================================
                                 NO
                            ================================================== --}}

                            <td class="no">

                                {{ $index + 1 }}

                            </td>


                            {{-- =================================================
                                 SISWA
                            ================================================== --}}

                            <td>

                                {{ $item->siswa?->nama ?? '-' }}

                            </td>


                            {{-- =================================================
                                 KELAS
                            ================================================== --}}

                            <td>

                                {{ $item->siswa?->kelas?->nama_kelas ?? '-' }}

                            </td>


                            {{-- =================================================
                                 TAHUN AJARAN
                            ================================================== --}}

                            <td>

                                {{ $item->tahunAjaran?->nama ?? '-' }}

                            </td>


                            {{-- =================================================
                                 KATEGORI
                            ================================================== --}}

                            <td>

                                {{ $item->kategori?->nama ?? '-' }}

                            </td>


                            {{-- =================================================
                                 NOMINAL
                            ================================================== --}}

                            <td class="tagihan-nominal">

                                Rp
                                {{ number_format(
                                    $item->nominal ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- =================================================
                                 JATUH TEMPO
                            ================================================== --}}

                            <td>

                                @if($item->jatuh_tempo)

                                    {{ $item->jatuh_tempo->format('d-m-Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- =================================================
                                 AKSI
                            ================================================== --}}

                            <td class="tagihan-aksi">


                                {{-- DETAIL --}}

                                <a
                                    href="{{ route(
                                        'admin.tagihan.show',
                                        $item->id
                                    ) }}"
                                    class="btn-aksi btn-detail"
                                >

                                    Detail

                                </a>


                                {{-- EDIT --}}

                                <a
                                    href="{{ route(
                                        'admin.tagihan.edit',
                                        $item->id
                                    ) }}"
                                    class="btn-aksi btn-edit"
                                >

                                    Edit

                                </a>


                                {{-- HAPUS --}}

                                <form
                                    action="{{ route(
                                        'admin.tagihan.destroy',
                                        $item->id
                                    ) }}"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Yakin ingin menghapus tagihan ini?')"
                                >

                                    @csrf

                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="btn-aksi btn-hapus"
                                    >

                                        Hapus

                                    </button>

                                </form>

                            </td>

                        </tr>


                    @empty


                        {{-- =================================================
                             DATA KOSONG
                        ================================================== --}}

                        <tr>

                            <td
                                colspan="8"
                                class="tagihan-empty"
                            >

                                Belum ada tagihan.

                            </td>

                        </tr>


                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection