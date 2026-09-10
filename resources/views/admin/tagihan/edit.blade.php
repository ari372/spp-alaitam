@extends('layouts.admin')

@section('title', 'Edit Tagihan')

@section('page-title', 'Edit Tagihan')

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
            Edit Tagihan
        </h2>

        <p style="
            color:#777;
            margin-bottom:25px;
        ">
            Ubah data tagihan siswa.
        </p>


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


        <form
            method="POST"
            action="{{ route(
                'admin.tagihan.update',
                $tagihan->id
            ) }}"
        >

            @csrf

            @method('PUT')


            {{-- SISWA --}}

            <div style="margin-bottom:20px;">

                <label>
                    Siswa
                </label>

                <input
                    type="text"
                    value="{{ $tagihan->siswa->nama ?? '-' }}"
                    readonly
                    style="
                        width:100%;
                        padding:12px;
                        margin-top:7px;
                        border:1px solid #ddd;
                        border-radius:7px;
                        background:#f5f5f5;
                        box-sizing:border-box;
                    "
                >

            </div>


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

                    @foreach($tahunAjaran as $tahun)

                        <option
                            value="{{ $tahun->id }}"
                            {{ old(
                                'tahun_ajaran_id',
                                $tagihan->tahun_ajaran_id
                            ) == $tahun->id
                                ? 'selected'
                                : ''
                            }}
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

                    @foreach($kategori as $item)

                        <option
                            value="{{ $item->id }}"
                            data-nominal="{{ $item->nominal }}"

                            {{ old(
                                'kategori_tagihan_id',
                                $tagihan->kategori_tagihan_id
                            ) == $item->id
                                ? 'selected'
                                : ''
                            }}
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
                    style="
                        width:100%;
                        padding:12px;
                        margin-top:7px;
                        border:1px solid #ddd;
                        border-radius:7px;
                        background:#f5f5f5;
                        box-sizing:border-box;
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
                    value="{{ old(
                        'jatuh_tempo',
                        $tagihan->jatuh_tempo
                            ? $tagihan->jatuh_tempo->format('Y-m-d')
                            : ''
                    ) }}"
                    style="
                        width:100%;
                        padding:12px;
                        margin-top:7px;
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
                    Update Tagihan
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