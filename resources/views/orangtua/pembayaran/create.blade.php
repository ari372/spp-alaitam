@extends('layouts.orangtua')

@section('title', 'Pembayaran Tagihan')

@push('styles')
    @vite('resources/css/orang-tua/pembayaran.css')
@endpush

@section('content')

@php
    $isSpp = strtolower(trim($tagihan->kategori->nama ?? '')) === 'spp';

    /*
    |--------------------------------------------------------------------------
    | DATA SPP
    |--------------------------------------------------------------------------
    | Jika kategori SPP, ambil seluruh tagihan SPP siswa pada tahun ajaran
    | yang sama karena SPP merupakan tagihan tahunan yang dibagi menjadi
    | 12 tagihan bulanan.
    |--------------------------------------------------------------------------
    */

    $totalTagihanTampilan = $tagihan->nominal;
    $totalDibayarTampilan = $totalDibayar;
    $sisaTagihanTampilan = $sisaTagihan;

    $tagihanSpp = collect();

    if ($isSpp) {
        $tagihanSpp = \App\Models\Tagihan::with('pembayaran')
            ->where('siswa_id', $tagihan->siswa_id)
            ->where('tahun_ajaran_id', $tagihan->tahun_ajaran_id)
            ->whereHas('kategori', function ($query) {
                $query->whereRaw('LOWER(TRIM(nama)) = ?', ['spp']);
            })
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $totalTagihanTampilan = $tagihanSpp->sum(function ($item) {
            return (float) $item->nominal;
        });

        $totalDibayarTampilan = $tagihanSpp->sum(function ($item) {
            return $item->pembayaran
                ->whereIn('status', ['dibayar', 'disetujui'])
                ->sum(function ($pembayaran) {
                    return (float) ($pembayaran->nominal ?? 0);
                });
        });

        $sisaTagihanTampilan = max(
            $totalTagihanTampilan - $totalDibayarTampilan,
            0
        );
    }
@endphp

<div class="payment-page">

    {{-- HEADER --}}
    <div class="page-header">
        <h1>
            Pembayaran Tagihan
        </h1>

        <p>
            Silakan lakukan pembayaran dan upload bukti pembayaran.
        </p>
    </div>


    {{-- ERROR --}}
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif


    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- DETAIL + PEMBAYARAN --}}
    <div class="payment-grid">

        {{-- DETAIL TAGIHAN --}}
        <div class="card">

            <h2 class="card-title">
                Detail Tagihan
            </h2>

            <div class="detail-list">

                {{-- NAMA SISWA --}}
                <div class="detail-item">
                    <span class="detail-label">
                        Nama Siswa
                    </span>

                    <span class="detail-value">
                        {{ $tagihan->siswa->nama ?? '-' }}
                    </span>
                </div>


                {{-- NIS --}}
                <div class="detail-item">
                    <span class="detail-label">
                        NIS
                    </span>

                    <span class="detail-value">
                        {{ $tagihan->siswa->nis ?? '-' }}
                    </span>
                </div>


                {{-- TAHUN AJARAN --}}
                <div class="detail-item">
                    <span class="detail-label">
                        Tahun Ajaran
                    </span>

                    <span class="detail-value">
                        {{ $tagihan->tahunAjaran->nama ?? '-' }}
                    </span>
                </div>


                {{-- KATEGORI --}}
                <div class="detail-item">
                    <span class="detail-label">
                        Kategori
                    </span>

                    <span class="detail-value green">

                        @if($isSpp)
                            SPP
                        @else
                            {{ $tagihan->kategori->nama ?? '-' }}
                        @endif

                    </span>
                </div>


                {{-- PERIODE SPP --}}
                @if($isSpp)
                    <div class="detail-item">

                        <span class="detail-label">
                            Periode SPP
                        </span>

                        <span class="detail-value">

                            @php
                                $bulanPertama = $tagihanSpp->first();
                                $bulanTerakhir = $tagihanSpp->last();

                                $namaBulan = [
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember',
                                ];
                            @endphp

                            @if($bulanPertama && $bulanTerakhir && $bulanPertama->bulan && $bulanTerakhir->bulan)

                                {{ $namaBulan[(int) $bulanPertama->bulan] ?? '-' }}
                                {{ $bulanPertama->tahun }}

                                -

                                {{ $namaBulan[(int) $bulanTerakhir->bulan] ?? '-' }}
                                {{ $bulanTerakhir->tahun }}

                            @else
                                -
                            @endif

                        </span>

                    </div>
                @endif


                {{-- TOTAL TAGIHAN --}}
                <div class="detail-item">

                    <span class="detail-label">
                        Total Tagihan
                    </span>

                    <span class="detail-value nominal">
                        Rp {{ number_format(
                            $totalTagihanTampilan,
                            0,
                            ',',
                            '.'
                        ) }}
                    </span>

                </div>


                {{-- SUDAH DIBAYAR --}}
                <div class="detail-item">

                    <span class="detail-label">
                        Sudah Dibayar
                    </span>

                    <span class="detail-value">
                        Rp {{ number_format(
                            $totalDibayarTampilan,
                            0,
                            ',',
                            '.'
                        ) }}
                    </span>

                </div>


                {{-- SISA TAGIHAN --}}
                <div class="detail-item">

                    <span class="detail-label">
                        Sisa Tagihan
                    </span>

                    <span class="detail-value nominal">
                        Rp {{ number_format(
                            $sisaTagihanTampilan,
                            0,
                            ',',
                            '.'
                        ) }}
                    </span>

                </div>

            </div>
        </div>


        {{-- DETAIL REKENING --}}
        <div class="card">

            <h2 class="card-title">
                Detail Pembayaran
            </h2>


            <div class="payment-method-info">

                <h3>
                    Transfer Bank
                </h3>


                <div class="bank-box">

                    <div class="bank-name">
                        BRI
                    </div>

                    <div class="account-number">
                        1234567890
                    </div>

                    <div class="account-owner">
                        a.n. SMP Plus Al-I'tam
                    </div>

                </div>


                <div class="bank-box">

                    <div class="bank-name">
                        BCA
                    </div>

                    <div class="account-number">
                        1234567890
                    </div>

                    <div class="account-owner">
                        a.n. SMP Plus Al-I'tam
                    </div>

                </div>

            </div>


            {{-- QRIS --}}
            <div class="qris-box">

                <div class="qris-title">
                    Pembayaran QRIS
                </div>

                @if(file_exists(public_path('images/qris.jpeg')))

                    <img
                        src="{{ asset('images/qris.jpeg') }}"
                        alt="QRIS SMP Plus Al-I'tam"
                    >

                @else

                    <div style="
                        padding:35px 15px;
                        color:#777;
                        border:1px dashed #ccc;
                        border-radius:8px;
                        margin-bottom:10px;
                    ">
                        QRIS sekolah belum tersedia
                    </div>

                @endif

                <div class="qris-description">
                    Silakan scan QRIS sekolah untuk melakukan pembayaran.
                </div>

            </div>

        </div>

    </div>


    {{-- FORM PEMBAYARAN --}}
    <div class="form-card">

        <h2 class="card-title">
            Form Pembayaran
        </h2>


        <form
            action="{{ route(
                'orangtua.pembayaran.store',
                ['tagihan' => $tagihan->id]
            ) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf


            {{-- INFORMASI NOMINAL --}}
            <div class="payment-info-box">

                <div class="payment-info-icon">
                    ℹ
                </div>

                <div class="payment-info-content">

                    <strong>
                        Pembayaran sebagian diperbolehkan
                    </strong>

                    @if($isSpp)

                        <p>
                            Total SPP tahun ajaran ini sebesar
                            <strong>
                                Rp {{ number_format(
                                    $totalTagihanTampilan,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </strong>.
                        </p>

                        <p>
                            Sudah dibayar sebesar
                            <strong>
                                Rp {{ number_format(
                                    $totalDibayarTampilan,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </strong>
                            dan sisa yang harus dibayar sebesar
                            <strong>
                                Rp {{ number_format(
                                    $sisaTagihanTampilan,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </strong>.
                        </p>

                        <p>
                            Silakan transfer sesuai nominal yang ingin dibayarkan
                            atau sesuai kesepakatan dengan pihak sekolah.
                        </p>

                        <p>
                            Setelah pembayaran dilakukan, upload bukti pembayaran
                            di bawah ini. Nominal pembayaran akan diperiksa dan
                            ditentukan oleh admin berdasarkan bukti pembayaran.
                        </p>

                    @else

                        <p>
                            Silakan transfer sesuai nominal yang ingin dibayarkan
                            atau sesuai kesepakatan dengan pihak sekolah.
                        </p>

                        <p>
                            Setelah pembayaran dilakukan, upload bukti pembayaran
                            di bawah ini. Nominal pembayaran akan diperiksa dan
                            ditentukan oleh admin berdasarkan bukti pembayaran.
                        </p>

                    @endif

                </div>

            </div>


            {{-- METODE PEMBAYARAN --}}
            <div class="form-group">

                <label class="form-label">

                    Metode Pembayaran

                    <span class="required">
                        *
                    </span>

                </label>


                <div class="method-options">

                    {{-- TRANSFER --}}
                    <div class="method-option">

                        <input
                            type="radio"
                            name="metode"
                            id="transfer"
                            value="transfer"
                            {{ old('metode') === 'transfer' ? 'checked' : '' }}
                            required
                        >

                        <label for="transfer">

                            <span class="method-icon">
                                🏦
                            </span>

                            Transfer Bank

                        </label>

                    </div>


                    {{-- QRIS --}}
                    <div class="method-option">

                        <input
                            type="radio"
                            name="metode"
                            id="qris"
                            value="qris"
                            {{ old('metode') === 'qris' ? 'checked' : '' }}
                        >

                        <label for="qris">

                            <span class="method-icon">
                                ▦
                            </span>

                            QRIS

                        </label>

                    </div>

                </div>

            </div>


            {{-- BUKTI PEMBAYARAN --}}
            <div class="form-group">

                <label
                    for="bukti_pembayaran"
                    class="form-label"
                >

                    Bukti Pembayaran

                    <span class="required">
                        *
                    </span>

                </label>


                <div class="file-box">

                    <input
                        type="file"
                        name="bukti_pembayaran"
                        id="bukti_pembayaran"
                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                        required
                    >

                    <small class="form-help">
                        Format JPG, JPEG, PNG.
                        Maksimal 2 MB.
                    </small>

                </div>

            </div>


            {{-- ACTION --}}
            <div class="action-buttons">

                <a
                    href="{{ route('orangtua.dashboard') }}"
                    class="btn btn-back"
                >
                    ← Kembali
                </a>


                <button
                    type="submit"
                    class="btn btn-submit"
                >
                    Kirim Bukti Pembayaran
                </button>

            </div>

        </form>

    </div>

</div>

@endsection