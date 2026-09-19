@extends('layouts.admin')

@section('title', 'Edit Pembayaran')
@section('page-title', 'Edit Pembayaran')

@push('styles')
    @vite('resources/css/admin/laporan.css')
@endpush

@section('content')

<div class="laporan-edit-page">

    {{-- HEADER --}}
    <div class="laporan-detail-header">

        <div class="laporan-detail-header-content">

            <div class="laporan-detail-header-icon">
                <i data-lucide="pencil"></i>
            </div>

            <div>
                <h2>Edit Pembayaran</h2>
                <p>Perbarui data pembayaran yang tercatat dalam laporan.</p>
            </div>

        </div>

        <a
            href="{{ route('admin.laporan.index') }}"
            class="laporan-back-button"
        >
            <i data-lucide="arrow-left"></i>
            Kembali
        </a>

    </div>


    {{-- ERROR --}}
    @if($errors->any())

        <div class="laporan-error">

            <i data-lucide="triangle-alert"></i>

            <div>

                <strong>Terdapat kesalahan:</strong>

                <ul style="margin: 6px 0 0 18px;">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    <div class="laporan-edit-card">

        <div class="laporan-edit-header">

            <div class="laporan-edit-header-icon">
                <i data-lucide="file-pen-line"></i>
            </div>

            <div>

                <h3>Koreksi Data Pembayaran</h3>

                <p>
                    Pastikan data yang diubah sesuai dengan kondisi pembayaran.
                </p>

            </div>

        </div>


        <div class="laporan-edit-body">

            <form
                action="{{ route('admin.laporan.update', $pembayaran->id) }}"
                method="POST"
            >

                @csrf
                @method('PUT')


                <div class="laporan-edit-grid">

                    {{-- SISWA --}}
                    <div class="laporan-edit-form-group">

                        <label>
                            Siswa
                        </label>

                        <input
                            type="text"
                            value="{{ $pembayaran->tagihan?->siswa?->nama ?? '-' }}"
                            class="laporan-readonly"
                            readonly
                        >

                    </div>


                    {{-- NIS --}}
                    <div class="laporan-edit-form-group">

                        <label>
                            NIS
                        </label>

                        <input
                            type="text"
                            value="{{ $pembayaran->tagihan?->siswa?->nis ?? '-' }}"
                            class="laporan-readonly"
                            readonly
                        >

                    </div>


                    {{-- KATEGORI --}}
                    <div class="laporan-edit-form-group">

                        <label>
                            Kategori
                        </label>

                        <input
                            type="text"
                            value="{{ $pembayaran->tagihan?->kategori?->nama ?? '-' }}"
                            class="laporan-readonly"
                            readonly
                        >

                    </div>


                    {{-- TAHUN AJARAN --}}
                    <div class="laporan-edit-form-group">

                        <label>
                            Tahun Ajaran
                        </label>

                        <input
                            type="text"
                            value="{{ $pembayaran->tagihan?->tahunAjaran?->nama ?? '-' }}"
                            class="laporan-readonly"
                            readonly
                        >

                    </div>


                    {{-- NOMINAL --}}
                    <div class="laporan-edit-form-group">

                        <label for="nominal">

                            Nominal Pembayaran

                            <span class="laporan-required">
                                *
                            </span>

                        </label>

                        <input
                            type="number"
                            name="nominal"
                            id="nominal"
                            value="{{ old('nominal', $pembayaran->nominal) }}"
                            min="1"
                            required
                        >

                        <small>
                            Masukkan nominal pembayaran yang benar.
                        </small>

                    </div>


                    {{-- METODE --}}
                    <div class="laporan-edit-form-group">

                        <label for="metode">

                            Metode Pembayaran

                            <span class="laporan-required">
                                *
                            </span>

                        </label>

                        <select
                            name="metode"
                            id="metode"
                            required
                        >

                            <option value="">
                                Pilih metode
                            </option>

                            <option
                                value="transfer"
                                {{ old('metode', $pembayaran->metode) === 'transfer' ? 'selected' : '' }}
                            >
                                Transfer
                            </option>

                            <option
                                value="qris"
                                {{ old('metode', $pembayaran->metode) === 'qris' ? 'selected' : '' }}
                            >
                                QRIS
                            </option>

                            <option
                                value="cash"
                                {{ old('metode', $pembayaran->metode) === 'cash' ? 'selected' : '' }}
                            >
                                Cash
                            </option>

                        </select>

                    </div>


                    {{-- STATUS --}}
                    <div class="laporan-edit-form-group">

                        <label for="status">

                            Status Pembayaran

                            <span class="laporan-required">
                                *
                            </span>

                        </label>

                        <select
                            name="status"
                            id="status"
                            required
                        >

                            <option
                                value="menunggu"
                                {{ old('status', $pembayaran->status) === 'menunggu' ? 'selected' : '' }}
                            >
                                Menunggu
                            </option>

                            <option
                                value="dibayar"
                                {{ old('status', $pembayaran->status) === 'dibayar' ? 'selected' : '' }}
                            >
                                Dibayar
                            </option>

                            <option
                                value="ditolak"
                                {{ old('status', $pembayaran->status) === 'ditolak' ? 'selected' : '' }}
                            >
                                Ditolak
                            </option>

                        </select>

                    </div>


                    {{-- TANGGAL --}}
                    <div class="laporan-edit-form-group">

                        <label for="tanggal_kirim">

                            Tanggal Pembayaran

                            <span class="laporan-required">
                                *
                            </span>

                        </label>

                        <input
                            type="datetime-local"
                            name="tanggal_kirim"
                            id="tanggal_kirim"
                            value="{{ old(
                                'tanggal_kirim',
                                $pembayaran->tanggal_kirim
                                    ? $pembayaran->tanggal_kirim->format('Y-m-d\TH:i')
                                    : ''
                            ) }}"
                            required
                        >

                    </div>


                    {{-- CATATAN --}}
                    <div class="laporan-edit-form-group full-width">

                        <label for="catatan">
                            Catatan
                        </label>

                        <textarea
                            name="catatan"
                            id="catatan"
                            placeholder="Masukkan catatan jika diperlukan..."
                        >{{ old('catatan', $pembayaran->catatan) }}</textarea>

                    </div>

                </div>


                {{-- ACTION --}}
                <div class="laporan-edit-actions">

                    <a
                        href="{{ route('admin.laporan.index') }}"
                        class="laporan-edit-button laporan-edit-cancel"
                    >
                        <i data-lucide="x"></i>
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="laporan-edit-button laporan-edit-save"
                    >
                        <i data-lucide="save"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script src="https://unpkg.com/lucide@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>

@endsection