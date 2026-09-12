@extends('layouts.admin')

@section('title', 'Buat Tagihan')

@section('page-title', 'Buat Tagihan')

@section('content')

<link rel="stylesheet" href="{{ asset('css/tagihan.css') }}">

<div class="tagihan-create-container">

    <div class="tagihan-create-card">

        <h2 class="tagihan-create-title">
            Buat Tagihan
        </h2>

        <p class="tagihan-create-subtitle">
            Buat tagihan untuk seluruh siswa sekaligus
        </p>


        {{-- ERROR DARI CONTROLLER --}}

        @if(session('error'))

            <div class="tagihan-alert tagihan-alert-error">

                {{ session('error') }}

            </div>

        @endif


        {{-- ERROR VALIDASI --}}

        @if($errors->any())

            <div class="tagihan-alert tagihan-alert-error">

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- INFO --}}

        <div class="tagihan-info">

            <strong>
                Informasi
            </strong>

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

            <div class="tagihan-form-group">

                <label>
                    Tahun Ajaran
                </label>

                <select
                    name="tahun_ajaran_id"
                    required
                    class="tagihan-form-control"
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

            <div class="tagihan-form-group">

                <label>
                    Kategori Pembayaran
                </label>

                <select
                    name="kategori_tagihan_id"
                    id="kategori"
                    required
                    class="tagihan-form-control"
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

            <div class="tagihan-form-group">

                <label>
                    Nominal Tagihan
                </label>

                <input
                    type="text"
                    id="nominal"
                    readonly
                    placeholder="Otomatis dari kategori"
                    class="tagihan-form-control tagihan-readonly"
                >

            </div>


            {{-- JUMLAH SISWA --}}

            <div class="tagihan-form-group">

                <label>
                    Target Tagihan
                </label>

                <input
                    type="text"
                    value="{{ \App\Models\Siswa::count() }} Siswa"
                    readonly
                    class="tagihan-form-control tagihan-target"
                >

            </div>


            {{-- JATUH TEMPO --}}

            <div class="tagihan-form-group">

                <label>
                    Jatuh Tempo
                </label>

                <input
                    type="date"
                    name="jatuh_tempo"
                    value="{{ old('jatuh_tempo') }}"
                    class="tagihan-form-control"
                >

            </div>


            {{-- BUTTON --}}

            <div class="tagihan-button-area">

                <button
                    type="submit"
                    class="tagihan-btn tagihan-btn-simpan"
                >
                    Buat Tagihan Semua Siswa
                </button>


                <a
                    href="{{ route('admin.tagihan.index') }}"
                    class="tagihan-btn tagihan-btn-kembali"
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