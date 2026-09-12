<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Dashboard Orang Tua')
    </title>

    @vite([
        'resources/css/orang-tua/layout.css',
        'resources/css/orang-tua/dashboard.css',
        'resources/css/orang-tua/pembayaran.css'
    ])

</head>


<body>

    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <header class="navbar">

        {{-- BRAND --}}
<div class="navbar-title">

    <img
        src="{{ asset('images/logo-alaitam.png.jpg') }}"
        alt="Logo SMP Plus Al-I'tam"
        class="navbar-logo"
    >

    <div class="navbar-text">

        <h3>
            SMP Plus Al-I'tam
        </h3>

        <p>
            Sistem Pembayaran SPP
        </p>

    </div>

</div>

        {{-- NAVBAR RIGHT --}}
        <div class="navbar-right">

            {{-- PROFILE --}}
            <div class="parent-profile">

                {{-- INFORMASI ORANG TUA --}}
                <div class="parent-info">

                    <strong>
                        {{ auth()->user()->name ?? 'Orang Tua' }}
                    </strong>

                    <span>
                        Orang Tua / Wali Murid
                    </span>

                </div>


                {{-- AVATAR --}}
                <div class="parent-avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name ?? 'O',
                            0,
                            1
                        )
                    ) }}

                </div>

            </div>


            {{-- LOGOUT --}}
            <form
                action="{{ route('logout') }}"
                method="POST"
                class="logout-form"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-btn"
                >

                    {{-- ICON LOGOUT --}}
                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </div>

    </header>


    <!-- =====================================================
         CONTENT
    ====================================================== -->

    <main class="content">

        @yield('content')

    </main>


</body>

</html>