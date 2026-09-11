@extends('layouts.admin')

@section('title', 'Persetujuan Pembayaran')

@section('page-title', 'Persetujuan Pembayaran')

@section('content')

@vite('resources/css/admin/pembayaran.css')

<div class="pembayaran-container">

    {{-- HEADER --}}

    <div class="pembayaran-header">

        <div>
            <h2>
                Persetujuan Pembayaran
            </h2>

            <p>
                Periksa dan proses pembayaran yang dikirim oleh orang tua siswa.
            </p>
        </div>

        <a
            href="{{ route('admin.pembayaran.manual') }}"
            class="btn-manual"
        >
            + Pembayaran Manual
        </a>

    </div>


    {{-- SUCCESS --}}

    @if(session('success'))

        <div class="pembayaran-alert pembayaran-alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR --}}

    @if(session('error'))

        <div class="pembayaran-alert pembayaran-alert-error">
            {{ session('error') }}
        </div>

    @endif


    {{-- DAFTAR PEMBAYARAN --}}

    @forelse($pembayaran as $item)

        <div class="pembayaran-card">

            {{-- HEADER CARD --}}

            <div class="pembayaran-card-header">

                <div>
                    <h3>
                        Pembayaran Baru
                    </h3>
                </div>

                <span class="pembayaran-status">
                    Menunggu Persetujuan
                </span>

            </div>


            {{-- INFORMASI --}}

            <div class="pembayaran-info">

                {{-- SISWA --}}

                <div class="pembayaran-info-item">

                    <span class="pembayaran-info-label">
                        Siswa
                    </span>

                    <span class="pembayaran-info-value">
                        {{ $item->tagihan->siswa->nama ?? '-' }}
                    </span>

                </div>


                {{-- TAHUN AJARAN --}}

                <div class="pembayaran-info-item">

                    <span class="pembayaran-info-label">
                        Tahun Ajaran
                    </span>

                    <span class="pembayaran-info-value">
                        {{ $item->tagihan->tahunAjaran->nama ?? '-' }}
                    </span>

                </div>


                {{-- KATEGORI --}}

                <div class="pembayaran-info-item">

                    <span class="pembayaran-info-label">
                        Kategori
                    </span>

                    <span class="pembayaran-info-value">
                        {{ $item->tagihan->kategori->nama ?? '-' }}
                    </span>

                </div>


                {{-- NOMINAL --}}

                <div class="pembayaran-info-item">

                    <span class="pembayaran-info-label">
                        Nominal
                    </span>

                    <span class="pembayaran-info-value">
                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </span>

                </div>


                {{-- METODE --}}

                <div class="pembayaran-info-item">

                    <span class="pembayaran-info-label">
                        Metode Pembayaran
                    </span>

                    <span class="pembayaran-info-value">
                        {{ strtoupper($item->metode) }}
                    </span>

                </div>


                {{-- WAKTU --}}

                <div class="pembayaran-info-item">

                    <span class="pembayaran-info-label">
                        Waktu Pengiriman
                    </span>

                    <span class="pembayaran-info-value">

                        {{ $item->tanggal_kirim
                            ? $item->tanggal_kirim->format('d-m-Y H:i')
                            : '-'
                        }}

                    </span>

                </div>

            </div>


            {{-- BUKTI PEMBAYARAN --}}

            <div class="pembayaran-bukti">

                <h4>
                    Bukti Pembayaran
                </h4>

                @if($item->bukti_pembayaran)

                    <img
                        src="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                        alt="Bukti pembayaran"
                    >

                @else

                    <p class="pembayaran-bukti-empty">
                        Tidak ada bukti pembayaran.
                    </p>

                @endif

            </div>


            {{-- AKSI --}}

            <div class="pembayaran-actions">

                {{-- SETUJUI --}}

                <form
                    action="{{ route('admin.pembayaran.setujui', $item->id) }}"
                    method="POST"
                >

                    @csrf

                    @method('PATCH')

                    <button
                        type="submit"
                        class="pembayaran-btn pembayaran-btn-setujui"
                        onclick="return confirm('Yakin pembayaran ini benar dan ingin menyetujuinya?')"
                    >
                        ✓ Setujui Pembayaran
                    </button>

                </form>


                {{-- TOLAK --}}

                <form
                    action="{{ route('admin.pembayaran.tolak', $item->id) }}"
                    method="POST"
                    class="pembayaran-tolak-form"
                >

                    @csrf

                    @method('PATCH')

                    <textarea
                        name="catatan"
                        placeholder="Alasan penolakan..."
                        required
                    ></textarea>

                    <button
                        type="submit"
                        class="pembayaran-btn pembayaran-btn-tolak"
                        onclick="return confirm('Yakin ingin menolak pembayaran ini?')"
                    >
                        ✕ Tolak Pembayaran
                    </button>

                </form>

            </div>

        </div>

    @empty

        <div class="pembayaran-empty">

            <div class="pembayaran-empty-icon">
                ✓
            </div>

            <h3>
                Tidak ada pembayaran baru
            </h3>

            <p>
                Semua pembayaran sudah diproses.
            </p>

        </div>

    @endforelse

</div>

@endsection