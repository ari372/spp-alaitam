@extends('layouts.admin')

@section('title', 'Edit Tagihan')

@section('page-title', 'Edit Tagihan')

@section('content')

<div class="tagihan-form-container">

    <div class="tagihan-form-card">

        <h2 class="tagihan-form-title">
            Edit Tagihan
        </h2>

        <p class="tagihan-form-description">
            Ubah data tagihan siswa.
        </p>


        {{-- ERROR VALIDASI --}}

        @if($errors->any())

            <div class="tagihan-error">

                <ul>

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
                'admin.tagihan.update',
                $tagihan->id
            ) }}"
        >

            @csrf

            @method('PUT')


            {{-- SISWA --}}

            <div class="tagihan-form-group">

                <label>
                    Siswa
                </label>

                <input
                    type="text"
                    value="{{ $tagihan->siswa->nama ?? '-' }}"
                    readonly
                    class="tagihan-form-control tagihan-readonly"
                >

            </div>


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

            <div class="tagihan-form-group">

                <label>
                    Nominal Tagihan
                </label>

                <input
                    type="text"
                    id="nominal"
                    readonly
                    class="tagihan-form-control tagihan-readonly"
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
                    value="{{ old(
                        'jatuh_tempo',
                        $tagihan->jatuh_tempo
                            ? $tagihan->jatuh_tempo->format('Y-m-d')
                            : ''
                    ) }}"
                    class="tagihan-form-control"
                >

            </div>


            {{-- BUTTON --}}

            <div class="tagihan-form-actions">

                <button
                    type="submit"
                    class="tagihan-btn tagihan-btn-update"
                >
                    Update Tagihan
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