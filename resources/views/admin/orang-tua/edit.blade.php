<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Orang Tua</title>

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
        textarea {
            width: 100%;

            padding: 11px;

            border: 1px solid #ddd;

            border-radius: 7px;
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

        .info {
            background: #cff4fc;

            color: #055160;

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
            Edit Orang Tua
        </h2>


        <div class="info">

            Kosongkan password jika tidak ingin
            mengubah password login.

        </div>


        @if($errors->any())

            <div class="error">

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            action="{{ route(
                'admin.orang-tua.update',
                $orangTua->id
            ) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            <div class="form-group">

                <label>
                    Nama Orang Tua
                </label>

                <input
                    type="text"
                    name="nama"
                    value="{{ old(
                        'nama',
                        $orangTua->nama
                    ) }}"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Email Login
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old(
                        'email',
                        $orangTua->email
                    ) }}"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Kosongkan jika tidak diubah"
                >

            </div>


            <div class="form-group">

                <label>
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Ulangi password baru"
                >

            </div>


            <div class="form-group">

                <label>
                    No HP
                </label>

                <input
                    type="text"
                    name="no_hp"
                    value="{{ old(
                        'no_hp',
                        $orangTua->no_hp
                    ) }}"
                >

            </div>


            <div class="form-group">

                <label>
                    Alamat
                </label>

                <textarea
                    name="alamat"
                >{{ old(
                    'alamat',
                    $orangTua->alamat
                ) }}</textarea>

            </div>


            <div class="actions">

                <button
                    type="submit"
                    class="btn btn-simpan"
                >
                    Update
                </button>

                <a
                    href="{{ route('admin.orang-tua.index') }}"
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