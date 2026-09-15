<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Admin')
    </title>


    {{-- =====================================================
         CSS ADMIN
    ====================================================== --}}

    @vite([
        'resources/css/admin/layout.css',
        'resources/js/app.js'
    ])

    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
>


    {{-- =====================================================
         CSS KHUSUS HALAMAN
    ====================================================== --}}

    @stack('styles')

</head>


<body>


    {{-- =====================================================
         SIDEBAR ADMIN
    ====================================================== --}}

    <aside class="sidebar">


        {{-- =================================================
             LOGO SEKOLAH
        ================================================== --}}

        <div class="logo">

            <div class="logo-image">

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



        {{-- =================================================
             MENU UTAMA
        ================================================== --}}

        <div class="menu-title">
            Menu Utama
        </div>


        <ul class="menu">


            {{-- =================================================
                 DASHBOARD
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/dashboard') }}"
                    class="{{ request()->is('admin/dashboard') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <rect
                                x="3"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />

                            <rect
                                x="14"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                            />

                            <rect
                                x="3"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />

                            <rect
                                x="14"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Dashboard
                    </span>

                </a>

            </li>



            {{-- =================================================
                 TAGIHAN
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/tagihan') }}"
                    class="{{ request()->is('admin/tagihan*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <path
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                            />

                            <path
                                d="M14 2v6h6"
                            />

                            <path
                                d="M8 13h8"
                            />

                            <path
                                d="M8 17h5"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Tagihan
                    </span>

                </a>

            </li>



            {{-- =================================================
                 PEMBAYARAN
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/pembayaran') }}"
                    class="{{ request()->is('admin/pembayaran*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <rect
                                x="2"
                                y="5"
                                width="20"
                                height="14"
                                rx="2"
                            />

                            <path
                                d="M2 10h20"
                            />

                            <path
                                d="M6 15h4"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Pembayaran
                    </span>

                </a>

            </li>



            {{-- =================================================
                 LAPORAN
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/laporan') }}"
                    class="{{ request()->is('admin/laporan*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <path
                                d="M4 19V5"
                            />

                            <path
                                d="M4 19h17"
                            />

                            <rect
                                x="7"
                                y="11"
                                width="3"
                                height="5"
                                rx="1"
                            />

                            <rect
                                x="12"
                                y="8"
                                width="3"
                                height="8"
                                rx="1"
                            />

                            <rect
                                x="17"
                                y="4"
                                width="3"
                                height="12"
                                rx="1"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Laporan
                    </span>

                </a>

            </li>


        </ul>



        {{-- =====================================================
             DATA MASTER
        ====================================================== --}}

        <div class="menu-title">
            Data Master
        </div>


        <ul class="menu">


            {{-- =================================================
                 DATA SISWA
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/siswa') }}"
                    class="{{ request()->is('admin/siswa*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <path
                                d="M22 10 12 5 2 10l10 5 10-5z"
                            />

                            <path
                                d="M6 12v5c3 2 9 2 12 0v-5"
                            />

                            <path
                                d="M22 10v6"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Data Siswa
                    </span>

                </a>

            </li>



            {{-- =================================================
                 DATA ORANG TUA
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/orang-tua') }}"
                    class="{{ request()->is('admin/orang-tua*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <path
                                d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                            />

                            <circle
                                cx="9"
                                cy="7"
                                r="4"
                            />

                            <path
                                d="M22 21v-2a4 4 0 0 0-3-3.87"
                            />

                            <path
                                d="M16 3.13a4 4 0 0 1 0 7.75"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Data Orang Tua
                    </span>

                </a>

            </li>



            {{-- =================================================
                 DATA KELAS
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/kelas') }}"
                    class="{{ request()->is('admin/kelas*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <path
                                d="M3 21h18"
                            />

                            <path
                                d="M5 21V5l7-3 7 3v16"
                            />

                            <path
                                d="M9 9h1"
                            />

                            <path
                                d="M14 9h1"
                            />

                            <path
                                d="M9 13h1"
                            />

                            <path
                                d="M14 13h1"
                            />

                            <path
                                d="M9 17h6"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Data Kelas
                    </span>

                </a>

            </li>

            {{-- =================================================
     TAHUN AJARAN
================================================== --}}

<li>

    <a
        href="{{ url('/admin/tahun-ajaran') }}"
        class="{{ request()->is('admin/tahun-ajaran*') ? 'active' : '' }}"
    >

        <span class="menu-icon">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                aria-hidden="true"
            >

                <rect
                    x="3"
                    y="4"
                    width="18"
                    height="17"
                    rx="2"
                />

                <line
                    x1="16"
                    y1="2"
                    x2="16"
                    y2="6"
                />

                <line
                    x1="8"
                    y1="2"
                    x2="8"
                    y2="6"
                />

                <line
                    x1="3"
                    y1="10"
                    x2="21"
                    y2="10"
                />

                <line
                    x1="8"
                    y1="14"
                    x2="8"
                    y2="14"
                />

                <line
                    x1="12"
                    y1="14"
                    x2="12"
                    y2="14"
                />

                <line
                    x1="16"
                    y1="14"
                    x2="16"
                    y2="14"
                />

                <line
                    x1="8"
                    y1="18"
                    x2="8"
                    y2="18"
                />

                <line
                    x1="12"
                    y1="18"
                    x2="12"
                    y2="18"
                />

                <line
                    x1="16"
                    y1="18"
                    x2="16"
                    y2="18"
                />

            </svg>

        </span>

        <span class="menu-text">
            Tahun Ajaran
        </span>

    </a>

</li>



            {{-- =================================================
                 KATEGORI PEMBAYARAN
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/kategori') }}"
                    class="{{ request()->is('admin/kategori*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <rect
                                x="4"
                                y="4"
                                width="6"
                                height="6"
                                rx="1"
                            />

                            <rect
                                x="14"
                                y="4"
                                width="6"
                                height="6"
                                rx="1"
                            />

                            <rect
                                x="4"
                                y="14"
                                width="6"
                                height="6"
                                rx="1"
                            />

                            <rect
                                x="14"
                                y="14"
                                width="6"
                                height="6"
                                rx="1"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Kategori Pembayaran
                    </span>

                </a>

            </li>


        </ul>



        {{-- =====================================================
             SISTEM
        ====================================================== --}}

        <div class="menu-title">
            Sistem
        </div>


        <ul class="menu">


            {{-- =================================================
                 PROFIL
            ================================================== --}}

            <li>

                <a
                    href="{{ url('/admin/profil') }}"
                    class="{{ request()->is('admin/profil*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <circle
                                cx="12"
                                cy="8"
                                r="4"
                            />

                            <path
                                d="M4 21a8 8 0 0 1 16 0"
                            />

                        </svg>

                    </span>

                    <span class="menu-text">
                        Profil
                    </span>

                </a>

            </li>


        </ul>

    </aside>



    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <main class="main">


        {{-- =================================================
             NAVBAR
        ================================================== --}}

        <header class="navbar">


            {{-- =================================================
                 JUDUL HALAMAN
            ================================================== --}}

            <div class="navbar-title">

                <h3>
                    @yield('page-title', 'Dashboard')
                </h3>

                <p>
                    Sistem Administrasi Pembayaran
                </p>

            </div>



            {{-- =================================================
                 NAVBAR KANAN
            ================================================== --}}

            <div class="navbar-right">


                {{-- =================================================
                     NOTIFIKASI
                ================================================== --}}

                <div class="notification-wrapper">

                    <a
                        href="{{ url('/admin/pembayaran') }}"
                        class="notification-btn"
                        title="Pembayaran Menunggu"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <path
                                d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                            />

                            <path
                                d="M10 21h4"
                            />

                        </svg>


                        <span
                            id="notification-badge"
                            class="notification-badge"
                            style="display:none;"
                        >
                            0
                        </span>

                    </a>

                </div>



                {{-- =================================================
                     ADMIN PROFILE
                ================================================== --}}

                <div class="admin-profile">

                    <div class="admin-avatar">
                        A
                    </div>


                    <div class="admin-info">

                        <strong>
                            Admin
                        </strong>

                        <span>
                            Administrator
                        </span>

                    </div>

                </div>



                {{-- =================================================
                     LOGOUT
                ================================================== --}}

                <form
                    action="{{ route('logout') }}"
                    method="POST"
                    class="logout-form"
                >

                    @csrf

                    <button
                        type="submit"
                        class="logout-btn"
                        title="Keluar dari sistem"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >

                            <path
                                d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                            />

                            <path
                                d="m16 17 5-5-5-5"
                            />

                            <path
                                d="M21 12H9"
                            />

                        </svg>

                        <span>
                            Logout
                        </span>

                    </button>

                </form>


            </div>

        </header>



        {{-- =====================================================
             CONTENT
        ====================================================== --}}

        <section class="content">

            @yield('content')

        </section>

    </main>



    {{-- =====================================================
         NOTIFICATION SCRIPT
    ====================================================== --}}

    <script>

        function loadNotification()
        {

            fetch(
                "{{ url('/admin/pembayaran/notifikasi') }}"
            )

            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        'Gagal mengambil notifikasi'
                    );

                }

                return response.json();

            })

            .then(data => {

                const badge =
                    document.getElementById(
                        'notification-badge'
                    );


                if (!badge) {
                    return;
                }


                if (data.jumlah > 0) {

                    badge.innerText =
                        data.jumlah;

                    badge.style.display =
                        'flex';

                } else {

                    badge.style.display =
                        'none';

                }

            })

            .catch(error => {

                console.error(
                    'Gagal mengambil notifikasi:',
                    error
                );

            });

        }


        // Jalankan ketika halaman dibuka
        loadNotification();


        // Periksa notifikasi setiap 10 detik
        setInterval(
            loadNotification,
            10000
        );

    </script>


    {{-- =====================================================
         SCRIPT TAMBAHAN HALAMAN
    ====================================================== --}}

    @stack('scripts')


</body>

</html>