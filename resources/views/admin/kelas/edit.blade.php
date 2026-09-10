@extends('layouts.admin')

@section('title', 'Edit Kelas')

@section('page-title', 'Edit Kelas')

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
            Edit Kelas
        </h2>

        <p style="
            color:#777;
            margin-bottom:25px;
        ">
            Ubah data kelas
        </p>


        {{-- ERROR --}}

        @if($errors->any())

            <div style="
                background:#f8d7da;
                color:#842029;
                padding:15px;
                border-radius:8px;
                margin-bottom:20px;
            ">

                <ul style="margin:0;">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- FORM --}}

        <form
            method="POST"
            action="{{ route(
                'admin.kelas.update',
                $kela->id
            ) }}"
        >

            @csrf

            @method('PUT')


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
                    value="{{ old(
                        'nama_kelas',
                        $kela->nama_kelas
                    ) }}"
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
                    Update Kelas
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