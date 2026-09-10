<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pembayaran Tagihan</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7f6;
            color: #333;
        }

        .navbar {
            background: #0f5132;
            color: white;
            padding: 18px 30px;
        }

        .navbar strong {
            font-size: 18px;
        }

        .container {
            max-width: 750px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 14px;

            box-shadow:
                0 4px 15px rgba(0,0,0,.08);
        }

        h2 {
            color: #0f5132;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error {
            background: #f8d7da;
            color: #842029;
        }

        .info {
            background: #f1f3f5;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 11px 0;
            border-bottom: 1px solid #ddd;
        }

        .row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
        }

        .sisa {
            color: #0f5132;
            font-size: 18px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 11px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #198754;
        }

        .help {
            display: block;
            margin-top: 7px;
            color: #777;
            font-size: 13px;
        }

        .metode {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .metode-option {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
        }

        .metode-option:hover {
            border-color: #198754;
        }

        .metode-option label {
            margin: 0;
            cursor: pointer;
        }

        .metode-option input {
            margin-right: 8px;
        }

        .rekening,
        .qris {
            display: none;
            margin-top: 15px;
            padding: 18px;
            background: #e9f5ee;
            border-radius: 9px;
            color: #0f5132;
        }

        .rekening p,
        .qris p {
            margin: 8px 0;
        }

        .button-area {
            display: flex;
            gap: 10px;
            margin-top: 28px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-bayar {
            background: #198754;
            color: white;
        }

        .btn-bayar:hover {
            background: #157347;
        }

        .btn-kembali {
            background: #6c757d;
            color: white;
        }

        .btn-kembali:hover {
            background: #5c636a;
        }

        ul {
            margin: 0;
            padding-left: 20px;
        }

        @media(max-width: 600px) {

            .container {
                margin: 20px auto;
            }

            .card {
                padding: 20px;
            }

            .metode {
                grid-template-columns: 1fr;
            }

            .row {
                flex-direction: column;
                gap: 5px;
            }

            .button-area {
                flex-direction: column;
            }

            .btn {
                text-align: center;
            }

        }

    </style>

</head>

<body>


<div class="navbar">

    <strong>
        SMP Plus Al-I'tam
    </strong>

</div>


<div class="container">

    <div class="card">

        <h2>
            Pembayaran Tagihan
        </h2>


        {{-- =====================================
             ERROR SESSION
        ====================================== --}}

        @if(session('error'))

            <div class="alert error">

                {{ session('error') }}

            </div>

        @endif


        {{-- =====================================
             ERROR VALIDASI
        ====================================== --}}

        @if($errors->any())

            <div class="alert error">

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- =====================================
             INFORMASI TAGIHAN
        ====================================== --}}

        <div class="info">

            <div class="row">

                <span class="label">
                    Siswa
                </span>

                <span>
                    {{ $tagihan->siswa->nama }}
                </span>

            </div>


            <div class="row">

                <span class="label">
                    Tahun Ajaran
                </span>

                <span>
                    {{ $tagihan->tahunAjaran->nama ?? '-' }}
                </span>

            </div>


            <div class="row">

                <span class="label">
                    Kategori
                </span>

                <span>
                    {{ $tagihan->kategori->nama ?? '-' }}
                </span>

            </div>


            <div class="row">

                <span class="label">
                    Total Tagihan
                </span>

                <span>

                    Rp
                    {{ number_format(
                        $tagihan->nominal,
                        0,
                        ',',
                        '.'
                    ) }}

                </span>

            </div>


            <div class="row">

                <span class="label">
                    Sudah Dibayar
                </span>

                <span>

                    Rp
                    {{ number_format(
                        $totalDibayar,
                        0,
                        ',',
                        '.'
                    ) }}

                </span>

            </div>


            <div class="row">

                <span class="label">
                    Sisa Tagihan
                </span>

                <span class="sisa">

                    Rp
                    {{ number_format(
                        $sisaTagihan,
                        0,
                        ',',
                        '.'
                    ) }}

                </span>

            </div>

        </div>


        {{-- =====================================
             FORM PEMBAYARAN
        ====================================== --}}

        <form
            action="{{ route(
                'orangtua.pembayaran.store',
                $tagihan->id
            ) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf


            {{-- NOMINAL --}}

            <div class="form-group">

                <label>
                    Nominal Pembayaran
                </label>

                <input
                    type="number"
                    name="nominal"
                    value="{{ old(
                        'nominal',
                        $sisaTagihan
                    ) }}"
                    min="1"
                    max="{{ $sisaTagihan }}"
                    required
                >

                <small class="help">

                    Maksimal pembayaran:

                    Rp
                    {{ number_format(
                        $sisaTagihan,
                        0,
                        ',',
                        '.'
                    ) }}

                </small>

            </div>


            {{-- METODE --}}

            <div class="form-group">

                <label>
                    Metode Pembayaran
                </label>

                <div class="metode">

                    <div class="metode-option">

                        <label>

                            <input
                                type="radio"
                                name="metode"
                                value="transfer"
                                onchange="pilihMetode('transfer')"
                                {{ old('metode') === 'transfer'
                                    ? 'checked'
                                    : ''
                                }}
                                required
                            >

                            Transfer Bank

                        </label>

                    </div>


                    <div class="metode-option">

                        <label>

                            <input
                                type="radio"
                                name="metode"
                                value="qris"
                                onchange="pilihMetode('qris')"
                                {{ old('metode') === 'qris'
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            QRIS

                        </label>

                    </div>

                </div>


                {{-- TRANSFER --}}

                <div
                    id="transfer"
                    class="rekening"
                >

                    <strong>
                        Informasi Transfer
                    </strong>

                    <p>
                        Bank: BRI
                    </p>

                    <p>
                        No. Rekening:
                        <strong>
                            1234567890
                        </strong>
                    </p>

                    <p>
                        Atas Nama:
                        <strong>
                            SMP Plus Al-I'tam
                        </strong>
                    </p>

                </div>


                {{-- QRIS --}}

                <div
                    id="qris"
                    class="qris"
                >

                    <strong>
                        Pembayaran QRIS
                    </strong>

                    <p>
                        Silakan scan QRIS sekolah
                        menggunakan aplikasi pembayaran
                        yang tersedia.
                    </p>

                    <p>
                        <strong>
                            QRIS SMP Plus Al-I'tam
                        </strong>
                    </p>

                </div>

            </div>


            {{-- BUKTI PEMBAYARAN --}}

            <div class="form-group">

                <label>
                    Upload Bukti Pembayaran
                </label>

                <input
                    type="file"
                    name="bukti_pembayaran"
                    accept=".jpg,.jpeg,.png"
                    required
                >

                <small class="help">

                    Format JPG/JPEG/PNG.
                    Maksimal 2 MB.

                </small>

            </div>


            {{-- BUTTON --}}

            <div class="button-area">

                <button
                    type="submit"
                    class="btn btn-bayar"
                >
                    Kirim Bukti Pembayaran
                </button>


                <a
                    href="{{ route(
                        'orangtua.pembayaran.index'
                    ) }}"
                    class="btn btn-kembali"
                >
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>


<script>

function pilihMetode(metode)
{
    document.getElementById('transfer')
        .style.display = 'none';

    document.getElementById('qris')
        .style.display = 'none';


    if (metode === 'transfer') {

        document.getElementById('transfer')
            .style.display = 'block';

    }


    if (metode === 'qris') {

        document.getElementById('qris')
            .style.display = 'block';

    }
}


/*
 * Jika sebelumnya sudah memilih metode
 * lalu halaman reload karena validasi error,
 * tampilkan kembali informasinya.
 */

document.addEventListener(
    'DOMContentLoaded',
    function ()
    {
        const metode =
            document.querySelector(
                'input[name="metode"]:checked'
            );

        if (metode) {

            pilihMetode(metode.value);

        }
    }
);

</script>

</body>

</html>