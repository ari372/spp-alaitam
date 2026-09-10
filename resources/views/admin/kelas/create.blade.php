@extends('layouts.admin')

@section('title', 'Tambah Kelas')

@section('page-title', 'Tambah Kelas')

@section('content')

<div style="max-width:700px;">

    <div style="
        background:white;
        padding:30px;
        border-radius:15px;
        box-shadow:0 4px 15px rgba(0,0,0,.08);
    ">

        <h2 style="
            color:#0f5132;
            margin-bottom:8px;
        ">
            Tambah Kelas
        </h2>

        <p style="
            color:#777;
            margin-bottom:25px;
        ">
            Tambahkan data kelas baru
        </p>


        {{-- ERROR VALIDASI --}}

        @if($errors->any())

            <div style="
                background:#f8d7da;
                color:#842029;
                padding:15px;
                border-radius:8px;
                margin-bottom:20px;
            ">

                <ul style="
                    margin:0;
                    padding-left:20px;
                ">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- FORM TAMBAH KELAS --}}

        <form
            method="POST"
            action="{{ route('admin.kelas.store') }}"
        >

            @csrf


            {{-- NAMA KELAS --}}

            <div style="margin-bottom:25px;">

                <label style="
                    display:block;
                    margin-bottom:7px;
                    font-weight:bold;
                ">
                    Nama Kelas
                </label>

                <input
                    type="text"
                    name="nama_kelas"
                    value="{{ old('nama_kelas') }}"
                    placeholder="Contoh: VII A"
                    required
                    style="
                        width:100%;
                        padding:12px;
                        border:1px solid #ddd;
                        border-radius:7px;
                        box-sizing:border-box;
                    "
                >

            </div>


            {{-- BUTTON --}}

            <div style="
                display:flex;
                gap:10px;
            ">

                <button
                    type="submit"
                    style="
                        background:#0f5132;
                        color:white;
                        border:none;
                        padding:12px 22px;
                        border-radius:7px;
                        cursor:pointer;
                    "
                >
                    Simpan Kelas
                </button>


                <a
                    href="{{ route('admin.kelas.index') }}"
                    style="
                        background:#6c757d;
                        color:white;
                        text-decoration:none;
                        padding:12px 22px;
                        border-radius:7px;
                    "
                >
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

@endsection