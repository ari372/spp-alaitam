<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - SPP SMP Al-I'tam</title>

    @vite('resources/css/auth/login.css')

</head>

<body>

<div class="login-container">

    <div class="login-card">

        <div class="login-header">

            <div class="login-logo">
    <img
        src="{{ asset('images/logo-alaitam.png.jpg') }}"
        alt="Logo SMP Plus Al-I'tam"
    >
</div>

            <h2>
                SMP Plus Al-I'tam
            </h2>

            <p>
                Sistem Pembayaran SPP
            </p>

        </div>


        @if ($errors->any())

            <div class="error">
                {{ $errors->first() }}
            </div>

        @endif


        <form
            action="{{ route('login.process') }}"
            method="POST"
        >

            @csrf


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="password-input"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword()"
                        title="Tampilkan password"
                    >
                        👁
                    </button>

                </div>

            </div>


            <button
                type="submit"
                class="btn-login"
            >
                Login
            </button>

        </form>


        <div class="login-footer">
            Sistem Pembayaran SPP SMP Plus Al-I'tam
        </div>

    </div>

</div>


<script>

    function togglePassword() {

        const password =
            document.getElementById('password');

        const button =
            document.querySelector('.toggle-password');

        if (password.type === 'password') {

            password.type = 'text';

            button.innerText = '🙈';

            button.title = 'Sembunyikan password';

        } else {

            password.type = 'password';

            button.innerText = '👁';

            button.title = 'Tampilkan password';

        }

    }

</script>

</body>

</html>