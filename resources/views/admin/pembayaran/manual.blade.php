@extends('layouts.admin')

@section('title', 'Pembayaran Manual')

@section('page-title', 'Pembayaran Manual')

@push('styles')
    @vite('resources/css/admin/pembayaran.css')
@endpush

@section('content')

<div class="pembayaran-container">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="pembayaran-header pembayaran-manual-header">

        <div class="pembayaran-header-content">

            <div class="pembayaran-header-icon">
                <i data-lucide="hand-coins"></i>
            </div>

            <div>
                <h2>
                    Pembayaran Manual
                </h2>

                <p>
                    Catat pembayaran tunai yang diterima langsung oleh admin.
                </p>
            </div>

        </div>

        <a
            href="{{ route('admin.pembayaran.index') }}"
            class="btn-kembali"
        >
            <i data-lucide="arrow-left"></i>
            Kembali
        </a>

    </div>


    {{-- =====================================================
         ALERT SUCCESS
    ====================================================== --}}

    @if(session('success'))

        <div class="pembayaran-alert pembayaran-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
         ALERT ERROR
    ====================================================== --}}

    @if(session('error'))

        <div class="pembayaran-alert pembayaran-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
         VALIDATION ERROR
    ====================================================== --}}

    @if($errors->any())

        <div class="pembayaran-alert pembayaran-alert-error">

            <i data-lucide="triangle-alert"></i>

            <div>

                <strong>
                    Terdapat kesalahan:
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =====================================================
         FORM CARD
    ====================================================== --}}

    <div class="pembayaran-card manual-card">


        {{-- =================================================
             CARD HEADER
        ================================================== --}}

        <div class="manual-title">

            <div class="manual-icon">
                <i data-lucide="banknote"></i>
            </div>

            <div>

                <h3>
                    Pembayaran Cash
                </h3>

                <p>
                    Masukkan data pembayaran yang diterima secara langsung.
                </p>

            </div>

        </div>


        {{-- =================================================
             FORM
        ================================================== --}}

        <form
            action="{{ route('admin.pembayaran.manual.store') }}"
            method="POST"
        >

            @csrf


            {{-- =================================================
                 SISWA / TAGIHAN
            ================================================== --}}

            <div class="form-group">

                <label for="tagihan_id">

                    <i data-lucide="user-round"></i>

                    Siswa / Tagihan

                    <span class="required-mark">
                        *
                    </span>

                </label>

                <select
                    name="tagihan_id"
                    id="tagihan_id"
                    required
                >

                    <option value="">
                        -- Pilih Siswa dan Tagihan --
                    </option>


                    @foreach($tagihan as $item)

                        @php

                            $sudahDibayar = $item->pembayaran
                                ->where('status', 'dibayar')
                                ->sum('nominal');

                            $sisa = $item->nominal - $sudahDibayar;

                        @endphp


                        @if($sisa > 0)

                            <option
                                value="{{ $item->id }}"
                                {{ old('tagihan_id') == $item->id ? 'selected' : '' }}
                            >

                                {{ $item->siswa->nama ?? '-' }}

                                -

                                {{ $item->kategori->nama ?? '-' }}

                                -

                                {{ $item->tahunAjaran->nama ?? '-' }}

                                -

                                Sisa Rp
                                {{ number_format($sisa, 0, ',', '.') }}

                            </option>

                        @endif

                    @endforeach

                </select>

                <small class="form-help">
                    Pilih siswa dan tagihan yang akan menerima pembayaran.
                </small>

            </div>


            {{-- =================================================
                 NOMINAL
            ================================================== --}}

            <div class="form-group">

                <label for="nominal">

                    <i data-lucide="wallet"></i>

                    Nominal Pembayaran

                    <span class="required-mark">
                        *
                    </span>

                </label>

                <div class="manual-nominal-wrapper">

                    <span class="manual-rupiah">
                        Rp
                    </span>

                    <input
                        type="number"
                        name="nominal"
                        id="nominal"
                        value="{{ old('nominal') }}"
                        min="1"
                        placeholder="Masukkan nominal pembayaran"
                        required
                    >

                </div>

                <small class="form-help">
                    Masukkan nominal pembayaran yang diterima dari orang tua.
                </small>

            </div>


            {{-- =================================================
                 METODE PEMBAYARAN
            ================================================== --}}

            <div class="form-group">

                <label for="metode">

                    <i data-lucide="credit-card"></i>

                    Metode Pembayaran

                </label>

                <div class="manual-method-wrapper">

                    <input
                        type="text"
                        id="metode"
                        value="Cash"
                        readonly
                        class="input-readonly"
                    >

                    <span class="manual-method-icon">
                        <i data-lucide="banknote"></i>
                    </span>

                </div>

                <small class="form-help">
                    Pembayaran manual dicatat sebagai pembayaran cash.
                </small>

            </div>


            {{-- =================================================
                 TANGGAL PEMBAYARAN
            ================================================== --}}

            <div class="form-group">

                <label for="tanggal_kirim">

                    <i data-lucide="calendar-days"></i>

                    Tanggal Pembayaran

                    <span class="required-mark">
                        *
                    </span>

                </label>

                <input
                    type="date"
                    name="tanggal_kirim"
                    id="tanggal_kirim"
                    value="{{ old('tanggal_kirim', date('Y-m-d')) }}"
                    required
                >

                <small class="form-help">
                    Tentukan tanggal pembayaran diterima oleh admin.
                </small>

            </div>


            {{-- =================================================
                 INFORMASI
            ================================================== --}}

            <div class="manual-info">

                <div class="manual-info-icon">

                    <i data-lucide="info"></i>

                </div>

                <div>

                    <strong>
                        Informasi Pembayaran Manual
                    </strong>

                    <p>
                        Pembayaran cash akan langsung dicatat sebagai
                        <strong>dibayar</strong>
                        dan tidak memerlukan proses persetujuan admin.
                    </p>

                </div>

            </div>


            {{-- =================================================
                 ACTION
            ================================================== --}}

            <div class="form-actions">

                <a
                    href="{{ route('admin.pembayaran.index') }}"
                    class="btn-batal"
                >

                    <i data-lucide="x"></i>

                    Batal

                </a>


                <button
                    type="submit"
                    class="btn-simpan"
                >

                    <i data-lucide="save"></i>

                    Simpan Pembayaran

                </button>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     LUCIDE
========================================================= --}}

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

    });
</script>

@endpush

@endsection