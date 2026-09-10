@extends('layouts.admin')

@section('title', 'Buat Tagihan')

@section('page-title', 'Buat Tagihan')

@section('content')

<div style="max-width:800px;">

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
            Buat Tagihan
        </h2>


        <p style="
            color:#777;
            margin-bottom:25px;
        ">
            Buat tagihan untuk seluruh siswa sekaligus
        </p>


        {{-- ERROR DARI CONTROLLER --}}

        @if(session('error'))

            <div style="
                background:#f8d7da;
                color:#842029;
                padding:15px;
                border-radius:8px;
                margin-bottom:20px;
            ">

                {{ session('error') }}

            </div>

        @endif


        {{-- ERROR VALIDASI --}}

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


        {{-- INFO --}}

        <div style="
            background:#e9f5ee;
            color:#0f5132;
            padding:15px;
            border-radius:8px;
            margin-bottom:25px;
            line-height:1.6;
        ">

            <strong>Informasi</strong>

            <br>

            Tagihan akan dibuat secara otomatis untuk
            <strong>seluruh siswa</strong> yang terdaftar.

            <br>

            Admin tidak perlu membuat tagihan satu per satu.

        </div>


        <form
            method="POST"
            action="{{ route('admin.tagihan.store') }}"
        >

            @csrf


            {{-- TAHUN AJARAN --}}

            <div style="margin-bottom:20px;">

                <label>
                    Tahun Ajaran
                </label>

                <select
                    name="tahun_ajaran_id"
                    required
                    style="
                        width:100%;
                        padding:12px;
                        margin-top:7px;
                        border:1px solid #ddd;
                        border-radius:7px;
                    "
                >

                    <option value="">
                        -- Pilih Tahun Ajaran --
                    </option>

                    @foreach($tahunAjaran as $tahun)

                        <option
                            value="{{ $tahun->id }}"
                            {{ old('tahun_ajaran_id') == $tahun->id
                                ? 'selected'
                                : '' }}
                        >

                            {{ $tahun->nama }}

                            @if($tahun->aktif)

                                (Aktif)

                            @endif

                        </option>

                    @endforeach

                </select>

            </div>



            {{-- KATEGORI --}}

            <div style="margin-bottom:20px;">

                <label>
                    Kategori Pembayaran
                </label>

                <select
                    name="kategori_tagihan_id"
                    id="kategori"
                    required
                    style="
                        width:100%;
                        padding:12px;
                        margin-top:7px;
                        border:1px solid #ddd;
                        border-radius:7px;
                    "
                >

                    <option value="">
                        -- Pilih Kategori --
                    </option>

                    @foreach($kategori as $item)

                        <option
                            value="{{ $item->id }}"
                            data-nominal="{{ $item->nominal }}"
                            {{ old('kategori_tagihan_id') == $item->id
                                ? 'selected'
                                : '' }}
                        >

                            {{ $item->nama }}

                            -
                            
                            Rp
                            {{ number_format(
                                $item->nominal,
                                0,
                                ',',
                                '.'
                            ) }}

                        </option>

                    @endforeach

                </select>

            </div>



            {{-- NOMINAL --}}

            <div style="margin-bottom:20px;">

                <label>
                    Nominal Tagihan
                </label>

                <input
                    type="text"
                    id="nominal"
                    readonly
                    placeholder="Otomatis dari kategori"
                    style="
                        width:100%;
                        padding:12px;
                        margin-top:7px;
                        border:1px solid #ddd;
                        border-radius:7px;
                        background:#f5f5f5;
                    "
                >

            </div>



            {{-- JUMLAH SISWA --}}

            <div style="margin-bottom:20px;">

                <label>
                    Target Tagihan
                </label>

                <input
                    type="text"
                    value="{{ \App\Models\Siswa::count() }} Siswa"
                    readonly
                    style="
                        width:100%;
                        padding:12px;
                        margin-top:7px;
                        border:1px solid #ddd;
                        border-radius:7px;
                        background:#f5f5f5;
                        font-weight:bold;
                    "
                >

            </div>



            {{-- JATUH TEMPO --}}

            <div style="margin-bottom:25px;">

                <label>
                    Jatuh Tempo
                </label>

                <input
                    type="date"
                    name="jatuh_tempo"
                    value="{{ old('jatuh_tempo') }}"
                    style="
                        width:100%;
                        padding:12px;
                        margin-top:7px;
                        border:1px solid #ddd;
                        border-radius:7px;
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
                    Buat Tagihan Semua Siswa
                </button>


                <a
                    href="{{ route('admin.tagihan.index') }}"
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



<script>

const kategori =
    document.getElementById('kategori');

const nominal =
    document.getElementById('nominal');


function tampilkanNominal()
{
    const option =
        kategori.options[kategori.selectedIndex];


    if (!option || !option.dataset.nominal) {

        nominal.value = '';

        return;
    }


    const angka =
        Number(option.dataset.nominal);


    nominal.value =
        'Rp ' +
        angka.toLocaleString('id-ID');
}


kategori.addEventListener(
    'change',
    tampilkanNominal
);


tampilkanNominal();

</script>

@endsection