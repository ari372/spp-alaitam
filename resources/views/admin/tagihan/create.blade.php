@extends('layouts.admin')

@section('title', 'Buat Tagihan')

@section('page-title', 'Buat Tagihan')

@push('styles')
    @vite('resources/css/admin/tagihan.css')
@endpush

@section('content')

<div class="tagihan-detail-container">

    <div class="tagihan-detail-card">

        {{-- HEADER --}}
        <div class="tagihan-detail-header">

            <div>
                <h2 class="tagihan-detail-title">
                    <i class="bi bi-file-earmark-plus"></i>
                    Buat Tagihan
                </h2>

                <p class="tagihan-detail-subtitle">
                    Buat tagihan untuk seluruh siswa sekaligus.
                </p>
            </div>

            <span class="tagihan-form-badge">
                <i class="bi bi-people"></i>
                Semua Siswa
            </span>

        </div>


        {{-- ERROR DARI CONTROLLER --}}
        @if(session('error'))
            <div class="tagihan-alert tagihan-alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
            </div>
        @endif


        {{-- ERROR VALIDASI --}}
        @if($errors->any())
            <div class="tagihan-alert tagihan-alert-error">

                <div class="tagihan-error-title">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Terjadi kesalahan
                </div>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- INFORMASI --}}
        <div class="tagihan-info">

            <div class="tagihan-info-title">
                <i class="bi bi-info-circle-fill"></i>
                Informasi
            </div>

            <p>
                Tagihan akan dibuat secara otomatis untuk
                <strong>seluruh siswa</strong> yang terdaftar.
                Jika memilih <strong>Semua Kategori</strong>,
                semua kategori tagihan akan dibuat otomatis.
            </p>

        </div>


        {{-- FORM --}}
        <form
            method="POST"
            action="{{ route('admin.tagihan.store') }}"
        >

            @csrf

            <div class="tagihan-form-grid">

                {{-- TAHUN AJARAN --}}
                <div class="tagihan-form-group">

                    <label for="tahun_ajaran_id">
                        <i class="bi bi-calendar3"></i>
                        Tahun Ajaran
                    </label>

                    <select
                        name="tahun_ajaran_id"
                        id="tahun_ajaran_id"
                        required
                        class="tagihan-form-control"
                    >

                        <option value="">
                            -- Pilih Tahun Ajaran --
                        </option>

                        @foreach($tahunAjaran as $tahun)

                            <option
                                value="{{ $tahun->id }}"
                                {{ old('tahun_ajaran_id') == $tahun->id ? 'selected' : '' }}
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

                    <label for="kategori">

                        <i class="bi bi-tags"></i>
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

                        {{-- PILIHAN SEMUA KATEGORI --}}
                        <option
                            value="semua"
                            {{ old('kategori_tagihan_id') === 'semua' ? 'selected' : '' }}
                        >
                            Semua Kategori
                        </option>

                        @foreach($kategori as $item)

                            <option
                                value="{{ $item->id }}"
                                data-nominal="{{ $item->nominal }}"
                                {{ old('kategori_tagihan_id') == $item->id ? 'selected' : '' }}
                            >
                                {{ $item->nama }}
                                -
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </option>

                        @endforeach

                    </select>

                    <small class="tagihan-form-help">
                        Pilih satu kategori atau pilih Semua Kategori.
                    </small>

                </div>


                {{-- NOMINAL --}}
                <div class="tagihan-form-group">

                    <label for="nominal">

                        <i class="bi bi-cash-stack"></i>
                        Nominal Tagihan

                    </label>

                    <input
                        type="text"
                        id="nominal"
                        readonly
                        placeholder="Otomatis dari kategori"
                        class="tagihan-form-control tagihan-readonly"
                    >

                    <small class="tagihan-form-help">
                        Jika memilih Semua Kategori, nominal setiap kategori
                        mengikuti nominal yang tersimpan di database.
                    </small>

                </div>


                {{-- JUMLAH SISWA --}}
                <div class="tagihan-form-group">

                    <label for="target">

                        <i class="bi bi-people"></i>
                        Target Tagihan

                    </label>

                    <input
                        type="text"
                        id="target"
                        value="{{ \App\Models\Siswa::count() }} Siswa"
                        readonly
                        class="tagihan-form-control tagihan-target"
                    >

                </div>


                {{-- JATUH TEMPO --}}
                <div class="tagihan-form-group">

                    <label for="jatuh_tempo">

                        <i class="bi bi-calendar-event"></i>
                        Jatuh Tempo

                    </label>

                    <input
                        type="date"
                        name="jatuh_tempo"
                        id="jatuh_tempo"
                        value="{{ old('jatuh_tempo') }}"
                        class="tagihan-form-control"
                    >

                </div>

            </div>


            {{-- BUTTON --}}
            <div class="tagihan-detail-actions">

                <a
                    href="{{ route('admin.tagihan.index') }}"
                    class="tagihan-detail-btn tagihan-detail-btn-kembali"
                >
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>

                <button
                    type="submit"
                    class="tagihan-detail-btn tagihan-detail-btn-edit"
                >
                    <i class="bi bi-check-circle"></i>
                    Buat Tagihan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>
    const kategoriSelect = document.getElementById('kategori');
    const nominalInput = document.getElementById('nominal');

    if (kategoriSelect && nominalInput) {

        function tampilkanNominal() {
            const option =
                kategoriSelect.options[kategoriSelect.selectedIndex];

            if (!option || !option.value) {
                nominalInput.value = '';
                return;
            }

            if (option.value === 'semua') {
                nominalInput.value =
                    'Mengikuti nominal semua kategori';

                return;
            }

            const nominal = Number(option.dataset.nominal || 0);

            nominalInput.value =
                'Rp ' + nominal.toLocaleString('id-ID');
        }

        kategoriSelect.addEventListener(
            'change',
            tampilkanNominal
        );

        tampilkanNominal();
    }
</script>

@endpush