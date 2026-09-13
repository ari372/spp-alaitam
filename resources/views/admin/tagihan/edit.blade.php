@extends('layouts.admin')

@section('title', 'Edit Tagihan')

@section('page-title', 'Edit Tagihan')

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
                    <i class="bi bi-pencil-square"></i>
                    Edit Tagihan
                </h2>

                <p class="tagihan-detail-subtitle">
                    Ubah data tagihan siswa dengan benar.
                </p>
            </div>

            <span class="tagihan-form-badge">
                <i class="bi bi-file-earmark-text"></i>
                Data Tagihan
            </span>

        </div>

        {{-- ERROR VALIDASI --}}
        @if ($errors->any())
            <div class="tagihan-error">

                <div class="tagihan-error-title">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Terjadi kesalahan
                </div>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        {{-- FORM --}}
        <form
            method="POST"
            action="{{ route('admin.tagihan.update', $tagihan->id) }}"
        >
            @csrf
            @method('PUT')

            <div class="tagihan-form-grid">

                {{-- SISWA --}}
                <div class="tagihan-form-group">

                    <label for="siswa">
                        <i class="bi bi-person"></i>
                        Siswa
                    </label>

                    <input
                        type="text"
                        id="siswa"
                        value="{{ $tagihan->siswa?->nama ?? '-' }}"
                        readonly
                        class="tagihan-form-control tagihan-readonly"
                    >

                </div>

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
                            Pilih Tahun Ajaran
                        </option>

                        @foreach ($tahunAjaran as $tahun)
                            <option
                                value="{{ $tahun->id }}"
                                {{ old('tahun_ajaran_id', $tagihan->tahun_ajaran_id) == $tahun->id ? 'selected' : '' }}
                            >
                                {{ $tahun->nama }}

                                @if ($tahun->aktif)
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
                            Pilih Kategori Pembayaran
                        </option>

                        @foreach ($kategori as $item)
                            <option
                                value="{{ $item->id }}"
                                data-nominal="{{ $item->nominal }}"
                                {{ old('kategori_tagihan_id', $tagihan->kategori_tagihan_id) == $item->id ? 'selected' : '' }}
                            >
                                {{ $item->nama }} -
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>

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
                        class="tagihan-form-control tagihan-readonly"
                        placeholder="Nominal otomatis berdasarkan kategori"
                    >

                    <small class="tagihan-form-help">
                        Nominal mengikuti kategori pembayaran yang dipilih.
                    </small>

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
                        value="{{ old(
                            'jatuh_tempo',
                            $tagihan->jatuh_tempo
                                ? $tagihan->jatuh_tempo->format('Y-m-d')
                                : ''
                        ) }}"
                        class="tagihan-form-control"
                    >

                </div>

            </div>

            {{-- TOMBOL --}}
            <div class="tagihan-form-actions">

                <a
                    href="{{ route('admin.tagihan.index') }}"
                    class="tagihan-btn tagihan-btn-kembali"
                >
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>

                <button
                    type="submit"
                    class="tagihan-btn tagihan-btn-update"
                >
                    <i class="bi bi-check-circle"></i>
                    Update Tagihan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
    const kategori = document.getElementById('kategori');
    const nominal = document.getElementById('nominal');

    if (kategori && nominal) {
        function tampilkanNominal() {
            const option = kategori.options[kategori.selectedIndex];

            if (!option || !option.dataset.nominal) {
                nominal.value = '';
                return;
            }

            const angka = Number(option.dataset.nominal);

            nominal.value = 'Rp ' + angka.toLocaleString('id-ID');
        }

        kategori.addEventListener('change', tampilkanNominal);

        tampilkanNominal();
    }
</script>
@endpush