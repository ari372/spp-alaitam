<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Bukti Pembayaran
    </title>

    <style>

        @page {
            margin: 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            margin: 0;
        }

        .header {
            text-align: center;
            padding-bottom: 18px;
            border-bottom: 3px solid #0f5132;
            margin-bottom: 25px;
        }

        .header-title {
            color: #0f5132;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-subtitle {
            color: #374151;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .header-address {
            color: #6b7280;
            font-size: 10px;
        }

        .document-title {
            text-align: center;
            margin-bottom: 22px;
        }

        .document-title h2 {
            margin: 0;
            color: #0f5132;
            font-size: 18px;
        }

        .document-title p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 11px;
        }

        .section-title {
            color: #0f5132;
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 10px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .detail-table td:first-child {
            width: 38%;
            color: #6b7280;
        }

        .detail-table td:last-child {
            color: #1f2937;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            background: #dcfce7;
            color: #166534;
            border-radius: 20px;
            font-weight: bold;
        }

        .nominal {
            color: #0f5132 !important;
            font-size: 14px;
        }

        .proof-section {
            margin-top: 25px;
        }

        .proof-box {
            padding: 12px;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #f8fafc;
        }

        .proof-box img {
            max-width: 430px;
            max-height: 430px;
        }

        .no-proof {
            padding: 25px;
            text-align: center;
            color: #6b7280;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }

    </style>

</head>

<body>


    <div class="header">

        <div class="header-title">
            SISTEM PEMBAYARAN SPP
        </div>

        <div class="header-subtitle">
            SMP PLUS AL-I'TAM
        </div>

        <div class="header-address">
            Bukti Transaksi Pembayaran
        </div>

    </div>


    <div class="document-title">

        <h2>
            BUKTI PEMBAYARAN
        </h2>

        <p>
            Dokumen resmi transaksi pembayaran siswa
        </p>

    </div>


    <div class="section-title">
        Data Siswa
    </div>


    <table class="detail-table">

        <tr>

            <td>
                Nama Siswa
            </td>

            <td>
                {{ $pembayaran->tagihan->siswa->nama ?? '-' }}
            </td>

        </tr>


        <tr>

            <td>
                NIS
            </td>

            <td>
                {{ $pembayaran->tagihan->siswa->nis ?? '-' }}
            </td>

        </tr>


        <tr>

            <td>
                Kelas
            </td>

            <td>
                {{ $pembayaran->tagihan->siswa->kelas->nama_kelas ?? '-' }}
            </td>

        </tr>

    </table>


    <div class="section-title">
        Detail Pembayaran
    </div>


    <table class="detail-table">

        <tr>

            <td>
                Kategori
            </td>

            <td>
                {{ $pembayaran->tagihan->kategori->nama ?? '-' }}
            </td>

        </tr>


        <tr>

            <td>
                Tahun Ajaran
            </td>

            <td>
                {{ $pembayaran->tagihan->tahunAjaran->nama ?? '-' }}
            </td>

        </tr>


        <tr>

            <td>
                Nominal Pembayaran
            </td>

            <td class="nominal">
                Rp {{ number_format(
                    $pembayaran->nominal,
                    0,
                    ',',
                    '.'
                ) }}
            </td>

        </tr>


        <tr>

            <td>
                Metode Pembayaran
            </td>

            <td>

                @if ($pembayaran->metode === 'transfer')

                    Transfer Bank

                @elseif ($pembayaran->metode === 'qris')

                    QRIS

                @else

                    {{ $pembayaran->metode ?? '-' }}

                @endif

            </td>

        </tr>


        <tr>

            <td>
                Tanggal Pembayaran
            </td>

            <td>

                @if ($pembayaran->tanggal_kirim)

                    {{ \Carbon\Carbon::parse(
                        $pembayaran->tanggal_kirim
                    )->format('d-m-Y H:i') }}

                @else

                    -

                @endif

            </td>

        </tr>


        @if ($pembayaran->tanggal_disetujui)

            <tr>

                <td>
                    Tanggal Disetujui
                </td>

                <td>

                    {{ \Carbon\Carbon::parse(
                        $pembayaran->tanggal_disetujui
                    )->format('d-m-Y H:i') }}

                </td>

            </tr>

        @endif


        <tr>

            <td>
                Status
            </td>

            <td>

                @if (
                    $pembayaran->status === 'dibayar' ||
                    $pembayaran->status === 'disetujui'
                )

                    <span class="status">
                        Disetujui & Lunas
                    </span>

                @elseif ($pembayaran->status === 'menunggu')

                    <span>
                        Menunggu Persetujuan
                    </span>

                @elseif ($pembayaran->status === 'ditolak')

                    <span>
                        Ditolak
                    </span>

                @else

                    {{ ucfirst($pembayaran->status) }}

                @endif

            </td>

        </tr>

    </table>


    <div class="proof-section">

        <div class="section-title">
            Bukti Pembayaran
        </div>


        @if ($pembayaran->bukti_pembayaran)

            <div class="proof-box">

                <img
                    src="{{ public_path(
                        'storage/' . $pembayaran->bukti_pembayaran
                    ) }}"
                    alt="Bukti Pembayaran"
                >

            </div>

        @else

            <div class="proof-box no-proof">

                Bukti pembayaran tidak tersedia.

            </div>

        @endif

    </div>


    <div class="footer">

        Pembayaran telah tercatat dalam sistem
        pembayaran SMP Plus Al-I'tam.

        <br>

        Dokumen ini dibuat secara otomatis oleh sistem.

    </div>


</body>

</html>