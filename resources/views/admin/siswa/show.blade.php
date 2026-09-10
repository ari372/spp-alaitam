<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Detail Siswa</title>

    <style>

        body {
            margin: 0;

            font-family: Arial, sans-serif;

            background: #f5f7f6;
        }

        .navbar {
            background: #0f5132;

            color: white;

            padding: 16px 30px;

            font-weight: bold;
        }

        .content {
            max-width: 800px;

            margin: 30px auto;

            padding: 0 20px;
        }

        .card {
            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 3px 10px rgba(0,0,0,.08);
        }

        h2 {
            color: #0f5132;

            margin-top: 0;
        }

        .row {
            display: grid;

            grid-template-columns: 180px 1fr;

            padding: 12px 0;

            border-bottom: 1px solid #eee;
        }

        .label {
            font-weight: bold;
        }

        .btn {
            display: inline-block;

            margin-top: 25px;

            padding: 10px 18px;

            background: #6c757d;

            color: white;

            text-decoration: none;

            border-radius: 7px;
        }

    </style>

</head>

<body>

<div class="navbar">
    SMP Plus Al-I'tam
</div>


<div class="content">

    <div class="card">

        <h2>
            Detail Siswa
        </h2>


        <div class="row">

            <div class="label">
                NIS
            </div>

            <div>
                {{ $siswa->nis }}
            </div>

        </div>


        <div class="row">

            <div class="label">
                Nama
            </div>

            <div>
                {{ $siswa->nama }}
            </div>

        </div>


        <div class="row">

            <div class="label">
                Jenis Kelamin
            </div>

            <div>

                {{ $siswa->jenis_kelamin === 'L'
                    ? 'Laki-laki'
                    : 'Perempuan' }}

            </div>

        </div>


        <div class="row">

            <div class="label">
                Alamat
            </div>

            <div>
                {{ $siswa->alamat ?? '-' }}
            </div>

        </div>


        <div class="row">

            <div class="label">
                Kelas
            </div>

            <div>
                {{ $siswa->kelas->nama_kelas ?? '-' }}
            </div>

        </div>


        <div class="row">

            <div class="label">
                Orang Tua
            </div>

            <div>
                {{ $siswa->orangTua->nama ?? '-' }}
            </div>

        </div>


        <a
            href="{{ route('admin.siswa.index') }}"
            class="btn"
        >
            Kembali
        </a>

    </div>

</div>

</body>

</html>