<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Siswa</title>

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
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;

            margin-bottom: 7px;

            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;

            padding: 11px;

            border: 1px solid #ddd;

            border-radius: 7px;

            font-size: 14px;
        }

        textarea {
            min-height: 100px;
        }

        .error {
            background: #f8d7da;

            color: #842029;

            padding: 12px;

            border-radius: 7px;

            margin-bottom: 20px;
        }

        .actions {
            display: flex;

            gap: 10px;

            margin-top: 25px;
        }

        .btn {
            padding: 11px 18px;

            border: none;

            border-radius: 7px;

            text-decoration: none;

            cursor: pointer;
        }

        .btn-simpan {
            background: #198754;

            color: white;
        }

        .btn-kembali {
            background: #6c757d;

            color: white;
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
            Edit Data Siswa
        </h2>


        @if ($errors->any())

            <div class="error">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            action="{{ route(
                'admin.siswa.update',
                $siswa->id
            ) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            <div class="form-group">

                <label>NIS</label>

                <input
                    type="text"
                    name="nis"
                    value="{{ old(
                        'nis',
                        $siswa->nis
                    ) }}"
                    required
                >

            </div>


            <div class="form-group">

                <label>Nama Siswa</label>

                <input
                    type="text"
                    name="nama"
                    value="{{ old(
                        'nama',
                        $siswa->nama
                    ) }}"
                    required
                >

            </div>


            <div class="form-group">

                <label>Jenis Kelamin</label>

                <select
                    name="jenis_kelamin"
                    required
                >

                    <option value="L"
                        {{ old(
                            'jenis_kelamin',
                            $siswa->jenis_kelamin
                        ) == 'L'
                            ? 'selected'
                            : '' }}
                    >
                        Laki-laki
                    </option>

                    <option value="P"
                        {{ old(
                            'jenis_kelamin',
                            $siswa->jenis_kelamin
                        ) == 'P'
                            ? 'selected'
                            : '' }}
                    >
                        Perempuan
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>Alamat</label>

                <textarea
                    name="alamat"
                >{{ old(
                    'alamat',
                    $siswa->alamat
                ) }}</textarea>

            </div>


            <div class="form-group">

                <label>Kelas</label>

                <select
                    name="kelas_id"
                    required
                >

                    @foreach ($kelas as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ old(
                                'kelas_id',
                                $siswa->kelas_id
                            ) == $item->id
                                ? 'selected'
                                : '' }}
                        >
                            {{ $item->nama_kelas }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="form-group">

                <label>Orang Tua / Wali</label>

                <select
                    name="orang_tua_id"
                    required
                >

                    @foreach ($orangTua as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ old(
                                'orang_tua_id',
                                $siswa->orang_tua_id
                            ) == $item->id
                                ? 'selected'
                                : '' }}
                        >
                            {{ $item->nama }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="actions">

                <button
                    type="submit"
                    class="btn btn-simpan"
                >
                    Update
                </button>

                <a
                    href="{{ route('admin.siswa.index') }}"
                    class="btn btn-kembali"
                >
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

</body>

</html>