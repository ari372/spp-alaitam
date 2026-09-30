@extends('layouts.admin')

@section('title', 'Pengaturan Pembayaran')

@section('page-title', 'Pengaturan Pembayaran')

@push('styles')
    @vite('resources/css/admin/pembayaran.css')
@endpush

@section('content')

<div class="pengaturan-pembayaran-page">

    {{-- HEADER --}}
    <div class="pengaturan-header">

        <div class="pengaturan-header-icon">
            <i data-lucide="calendar-clock"></i>
        </div>

        <div class="pengaturan-header-text">
            <h2>Pengaturan Pembayaran</h2>

            <p>
                Atur tanggal jatuh tempo dan pengingat pembayaran
                bulanan orang tua.
            </p>
        </div>

    </div>


    {{-- ALERT SUCCESS --}}
    @if(session('success'))

        <div class="pengaturan-alert pengaturan-alert-success">

            <i data-lucide="check-circle"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ALERT ERROR --}}
    @if($errors->any())

        <div class="pengaturan-alert pengaturan-alert-error">

            <i data-lucide="alert-circle"></i>

            <div>

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        </div>

    @endif


    {{-- CARD PENGATURAN --}}
    <div class="pengaturan-card">

        {{-- CARD HEADER --}}
        <div class="pengaturan-card-header">

            <div>

                <h3>
                    Aturan Jatuh Tempo
                </h3>

                <p>
                    Pengaturan ini digunakan sebagai acuan
                    pembayaran SPP setiap bulan.
                </p>

            </div>

            <div class="pengaturan-status">

                @if($pengaturan->aktif)

                    <span class="status-aktif">
                        <span class="status-dot"></span>
                        Aktif
                    </span>

                @else

                    <span class="status-nonaktif">
                        <span class="status-dot"></span>
                        Tidak Aktif
                    </span>

                @endif

            </div>

        </div>


        {{-- FORM --}}
        <form
            action="{{ route('admin.pengaturan-pembayaran.update') }}"
            method="POST"
            class="pengaturan-form"
        >

            @csrf
            @method('PUT')


            {{-- GRID --}}
            <div class="pengaturan-form-grid">

                {{-- TANGGAL JATUH TEMPO --}}
                <div class="pengaturan-field">

                    <label for="tanggal_jatuh_tempo">
                        Tanggal Jatuh Tempo
                    </label>

                    <div class="pengaturan-input-group">

                        <input
                            type="number"
                            id="tanggal_jatuh_tempo"
                            name="tanggal_jatuh_tempo"
                            min="1"
                            max="31"
                            value="{{ old('tanggal_jatuh_tempo', $pengaturan->tanggal_jatuh_tempo) }}"
                            required
                        >

                        <span>
                            setiap bulan
                        </span>

                    </div>

                    <small>
                        Contoh: tanggal 5 berarti pembayaran
                        jatuh tempo setiap tanggal 5.
                    </small>

                    @error('tanggal_jatuh_tempo')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- PENGINGAT --}}
                <div class="pengaturan-field">

                    <label for="hari_pengingat">
                        Pengingat Pembayaran
                    </label>

                    <div class="pengaturan-input-group">

                        <input
                            type="number"
                            id="hari_pengingat"
                            name="hari_pengingat"
                            min="0"
                            max="30"
                            value="{{ old('hari_pengingat', $pengaturan->hari_pengingat) }}"
                            required
                        >

                        <span>
                            hari sebelumnya
                        </span>

                    </div>

                    <small>
                        Contoh: 3 berarti pengingat diberikan
                        3 hari sebelum jatuh tempo.
                    </small>

                    @error('hari_pengingat')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>

            </div>


            {{-- STATUS AKTIF --}}
            <div class="pengaturan-toggle">

                <div class="pengaturan-toggle-text">

                    <strong>
                        Aktifkan pengaturan pembayaran
                    </strong>

                    <span>
                        Sistem akan menggunakan tanggal ini
                        sebagai acuan jatuh tempo.
                    </span>

                </div>


                <label class="switch">

                    <input
                        type="checkbox"
                        name="aktif"
                        value="1"
                        {{ $pengaturan->aktif ? 'checked' : '' }}
                    >

                    <span class="slider"></span>

                </label>

            </div>


            {{-- INFO --}}
            <div class="pengaturan-info">

                <div class="pengaturan-info-icon">
                    <i data-lucide="info"></i>
                </div>

                <div class="pengaturan-info-content">

                    <strong>
                        Contoh pengaturan
                    </strong>

                    <p>
                        Jika tanggal jatuh tempo diatur
                        <strong>5</strong> dan pengingat
                        <strong>3 hari</strong>, maka sistem akan
                        menggunakan:
                    </p>

                    <div class="pengaturan-example">

                        <div class="example-item">

                            <span>
                                Tanggal jatuh tempo
                            </span>

                            <strong>
                                Setiap tanggal 5
                            </strong>

                        </div>

                        <div class="example-item">

                            <span>
                                Mulai pengingat
                            </span>

                            <strong>
                                Tanggal 2
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="pengaturan-form-footer">

                <button
                    type="submit"
                    class="btn-simpan-pengaturan"
                >

                    <i data-lucide="save"></i>

                    <span>
                        Simpan Pengaturan
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

});
</script>

@endpush