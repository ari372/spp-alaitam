
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

    <div class="login-page">


        {{-- ==============================
             SISI KIRI - BRANDING
        ============================== --}}

        <section class="login-brand">

            <div class="brand-content">


                {{-- LOGO SEKOLAH --}}

                <div class="brand-logo">

                    <img
                        src="{{ asset('images/logo-alaitam.png.jpg') }}"
                        alt="Logo SMP Plus Al-I'tam"
                    >

                </div>


                {{-- JUDUL BRANDING --}}

                <h1>
                    Sistem Pembayaran <span>SPP</span>
                </h1>


                {{-- DESKRIPSI --}}

                <p>
                    Kelola pembayaran SPP dengan lebih mudah,
                    cepat, transparan, dan aman untuk mendukung
                    pendidikan yang lebih baik.
                </p>


                {{-- KEUNGGULAN --}}

                <div class="brand-features">


                    <div class="feature-item">

                        <div class="feature-icon">
                            ✓
                        </div>

                        <span>
                            Aman
                        </span>

                    </div>


                    <div class="feature-item">

                        <div class="feature-icon">
                            ⚡
                        </div>

                        <span>
                            Mudah
                        </span>

                    </div>


                    <div class="feature-item">

                        <div class="feature-icon">
                            ▣
                        </div>

                        <span>
                            Transparan
                        </span>

                    </div>


                </div>

            </div>

        </section>



        {{-- ==============================
             SISI KANAN - LOGIN
        ============================== --}}

        <section class="login-section">

            <div class="login-container">

                <div class="login-card">


                    {{-- HEADER LOGIN --}}

                    <div class="login-header">


                        <div class="logo">

                            <img
                                src="{{ asset('images/logo-alaitam.png.jpg') }}"
                                alt="Logo SMP Plus Al-I'tam"
                            >

                        </div>


                        <h2>
                            Selamat Datang!
                        </h2>


                        <p>
                            Silakan login untuk melanjutkan
                        </p>


                    </div>



                    {{-- PESAN ERROR --}}

                    @if ($errors->any())

                        <div class="error">

                            {{ $errors->first() }}

                        </div>

                    @endif



                    {{-- FORM LOGIN --}}

                    <form
                        action="{{ route('login.process') }}"
                        method="POST"
                    >

                        @csrf



                        {{-- INPUT EMAIL --}}

                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>

                            <div class="input-wrapper">

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Masukkan email"
                                    autocomplete="username"
                                    required
                                >

                            </div>

                        </div>



                        {{-- INPUT PASSWORD --}}

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
                                    aria-label="Tampilkan password"
                                >
                                    👁
                                </button>

                            </div>

                        </div>



                        {{-- TOMBOL LOGIN --}}

                        <button
                            type="submit"
                            class="btn-login"
                        >
                            Login
                        </button>


                    </form>



                    {{-- FOOTER --}}

                    <div class="login-footer">

                        Sistem Pembayaran SPP SMP Plus Al-I'tam

                    </div>


                </div>

            </div>

        </section>


    </div>



    {{-- ==============================
         JAVASCRIPT TOGGLE PASSWORD
    ============================== --}}

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

                button.setAttribute(
                    'aria-label',
                    'Sembunyikan password'
                );

            } else {

                password.type = 'password';

                button.innerText = '👁';

                button.title = 'Tampilkan password';

                button.setAttribute(
                    'aria-label',
                    'Tampilkan password'
                );

            }

        }

    </script>


</body>

</html>