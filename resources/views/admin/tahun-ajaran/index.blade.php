@extends('layouts.admin')

@section('title', 'Tahun Ajaran')

@section('content')

@vite('resources/css/admin/tahun-ajaran.css')

<div class="tahun-ajaran-page">

    <div class="page-header">

        <div class="page-header-text">

            <h1>
                Tahun Ajaran
            </h1>

            <p>
                Kelola tahun ajaran yang digunakan dalam sistem pembayaran.
            </p>

        </div>

        <a
            href="{{ route('admin.tahun-ajaran.create') }}"
            class="btn-add"
        >

            <svg
                width="19"
                height="19"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>

            <span>
                Tambah Tahun Ajaran
            </span>

        </a>

    </div>


    {{-- ALERT SUCCESS --}}
    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- ALERT ERROR --}}
    @if(session('error'))

        <div class="alert alert-error">

            {{ session('error') }}

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="alert alert-error">

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- TABLE --}}
    <div class="table-card">

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th class="col-no">
                            No
                        </th>

                        <th>
                            Tahun Ajaran
                        </th>

                        <th>
                            Tanggal Mulai
                        </th>

                        <th>
                            Tanggal Selesai
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="col-action">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($tahunAjaran as $index => $item)

                        <tr>

                            {{-- NO --}}
                            <td class="text-center">

                                {{ $index + 1 }}

                            </td>


                            {{-- TAHUN AJARAN --}}
                            <td>

                                <strong class="tahun-nama">
                                    {{ $item->nama }}
                                </strong>

                            </td>


                            {{-- TANGGAL MULAI --}}
                            <td>

                                @if($item->tanggal_mulai)

                                    {{ $item->tanggal_mulai->format('d-m-Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- TANGGAL SELESAI --}}
                            <td>

                                @if($item->tanggal_selesai)

                                    {{ $item->tanggal_selesai->format('d-m-Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if($item->aktif)

                                    <span class="status status-active">
                                        Aktif
                                    </span>

                                @else

                                    <span class="status status-inactive">
                                        Tidak Aktif
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td>

                                <div class="action-buttons">

                                    {{-- AKTIFKAN --}}
                                    @if(!$item->aktif)

                                        <form
                                            action="{{ route(
                                                'admin.tahun-ajaran.aktifkan',
                                                $item->id
                                            ) }}"
                                            method="POST"
                                        >

                                            @csrf

                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="action-btn action-activate"
                                                title="Aktifkan"
                                                onclick="return confirm('Aktifkan tahun ajaran {{ $item->nama }}?')"
                                            >

                                                <svg
                                                    width="20"
                                                    height="20"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >

                                                    <polyline
                                                        points="20 6 9 17 4 12"
                                                    ></polyline>

                                                </svg>

                                            </button>

                                        </form>

                                    @endif


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route(
                                            'admin.tahun-ajaran.edit',
                                            $item->id
                                        ) }}"
                                        class="action-btn action-edit"
                                        title="Edit"
                                    >

                                        <svg
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <path d="M12 20h9"></path>

                                            <path
                                                d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"
                                            ></path>

                                        </svg>

                                    </a>


                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route(
                                            'admin.tahun-ajaran.destroy',
                                            $item->id
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus tahun ajaran {{ $item->nama }}?')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="action-btn action-delete"
                                            title="Hapus"
                                        >

                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >

                                                <polyline
                                                    points="3 6 5 6 21 6"
                                                ></polyline>

                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"
                                                ></path>

                                                <path
                                                    d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                                ></path>

                                                <line
                                                    x1="10"
                                                    y1="11"
                                                    x2="10"
                                                    y2="17"
                                                ></line>

                                                <line
                                                    x1="14"
                                                    y1="11"
                                                    x2="14"
                                                    y2="17"
                                                ></line>

                                            </svg>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="empty"
                            >
                                Belum ada data tahun ajaran.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection