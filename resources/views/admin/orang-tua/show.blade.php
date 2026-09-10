<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Detail Orang Tua</title>

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
            max-width: 900px;

            margin: 30px auto;

            padding: 0 20px;
        }

        .card {
            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 3px 10px rgba(0,0,0,.08);

            margin-bottom: 20px;
        }

        h2,
        h3 {
            color: #0f5132;
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

        table {
            width: 100%;

            border-collapse: collapse;
        }

        th {
            background: #0f5132;

            color: white;

            padding: 11px;

            text-align: left;
        }

        td {
            padding: 11px;

            border-bottom: 1px solid #eee;
        }

        .btn {
            display: inline-block;

            margin-top: 10px;

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
            Detail Orang Tua
        </h2>


        <div class="row">

            <div class="label">
                Nama
            </div>

            <div>
                {{ $orangTua->nama }}
            </div>

        </div>


        <div class="row">

            <div class="label">
                Email
            </div>

            <div>
                {{ $orangTua->email ?? '-' }}
            </div>

        </div>


        <div class="row">

            <div class="label">
                No HP
            </div>

            <div>
                {{ $orangTua->no_hp ?? '-' }}
            </div>

        </div>


        <div class="row">

            <div class="label">
                Alamat
            </div>

            <div>
                {{ $orangTua->alamat ?? '-' }}
            </div>

        </div>

    </div>


    <div class="card">

        <h3>
            Anak / Siswa
        </h3>


        @if($orangTua->siswa->count() > 0)

            <table>

                <thead>

                    <tr>

                        <th>
                            NIS
                        </th>

                        <th>
                            Nama
                        </th>

                        <th>
                            Kelas
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $orangTua->siswa
                        as $siswa
                    )

                        <tr>

                            <td>
                                {{ $siswa->nis }}
                            </td>

                            <td>
                                {{ $siswa->nama }}
                            </td>

                            <td>
                                {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <p>
                Orang tua ini belum memiliki
                data siswa.
            </p>

        @endif

    </div>


    <a
        href="{{ route('admin.orang-tua.index') }}"
        class="btn"
    >
        Kembali
    </a>

</div>

</body>

</html>