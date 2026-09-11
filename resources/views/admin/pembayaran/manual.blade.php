@extends('layouts.admin')

@section('title', 'Pembayaran Manual')

@section('page-title', 'Pembayaran Manual')

@section('content')

@vite('resources/css/admin/pembayaran.css')

<div class="pembayaran-container">

    <div class="pembayaran-header pembayaran-manual-header">

        <div>
            <h2>
                Pembayaran Manual
            </h2>

            <p>
                Catat pembayaran tunai yang diterima langsung oleh admin.
            </p>
        </div>

        <a
            href="{{ route('admin.pembayaran.index') }}"
            class="btn-kembali"
        >
            Kembali
        </a>

    </div>


    @if(session('success'))

        <div class="pembayaran-alert pembayaran-alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="pembayaran-alert pembayaran-alert-error">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="pembayaran-alert pembayaran-alert-error">

            {{ $errors->first() }}

        </div>

    @endif


    <div class="pembayaran-card">

        <div class="manual-title">

            <div class="manual-icon">
                💵
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


        <form
            action="{{ route('admin.pembayaran.manual.store') }}"
            method="POST"
        >

            @csrf


            <div class="form-group">

                <label for="tagihan_id">
                    Siswa / Tagihan
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

                                {{ $item->tahunAjaran->tahun_ajaran ?? '-' }}

                                -

                                Sisa Rp {{ number_format($sisa, 0, ',', '.') }}

                            </option>

                        @endif

                    @endforeach

                </select>

            </div>


            <div class="form-group">

                <label for="nominal">
                    Nominal Pembayaran
                </label>

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


            <div class="form-group">

                <label for="metode">
                    Metode Pembayaran
                </label>

                <input
                    type="text"
                    id="metode"
                    value="Cash"
                    readonly
                    class="input-readonly"
                >

            </div>


            <div class="form-group">

                <label for="tanggal_kirim">
                    Tanggal Pembayaran
                </label>

                <input
                    type="date"
                    name="tanggal_kirim"
                    id="tanggal_kirim"
                    value="{{ old('tanggal_kirim', date('Y-m-d')) }}"
                    required
                >

            </div>


            <div class="manual-info">

                <strong>
                    Informasi:
                </strong>

                <span>
                    Pembayaran cash akan langsung dicatat sebagai
                    <strong>dibayar</strong>
                    dan tidak memerlukan proses persetujuan.
                </span>

            </div>


            <div class="form-actions">

                <a
                    href="{{ route('admin.pembayaran.index') }}"
                    class="btn-batal"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn-simpan"
                >
                    Simpan Pembayaran
                </button>

            </div>

        </form>

    </div>

</div>

@endsection