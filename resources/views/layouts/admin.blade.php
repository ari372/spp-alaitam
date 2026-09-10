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

    {{-- CSS ADMIN --}}

    @vite([
    'resources/css/admin/layout.css',
    'resources/js/app.js'
])

    {{-- CSS KHUSUS HALAMAN --}}

    @stack('styles')

</head>

<body>


    {{-- =====================================================
         SIDEBAR ADMIN
    ====================================================== --}}

    <aside class="sidebar">

        <div class="logo">

            <h2>
                SMP Plus Al-I'tam
            </h2>

            <p>
                Sistem Pembayaran SPP
            </p>

        </div>


        <div class="menu-title">
            Menu Utama
        </div>


        <ul class="menu">

            {{-- DASHBOARD --}}

            <li>

                <a
                    href="{{ url('/admin/dashboard') }}"
                    class="{{ request()->is('admin/dashboard') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        🏠
                    </span>

                    <span class="menu-text">
                        Dashboard
                    </span>

                </a>

            </li>


            {{-- TAGIHAN --}}

            <li>

                <a
                    href="{{ url('/admin/tagihan') }}"
                    class="{{ request()->is('admin/tagihan*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        📋
                    </span>

                    <span class="menu-text">
                        Tagihan
                    </span>

                </a>

            </li>


            {{-- PEMBAYARAN --}}

            <li>

                <a
                    href="{{ url('/admin/pembayaran') }}"
                    class="{{ request()->is('admin/pembayaran*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        💳
                    </span>

                    <span class="menu-text">
                        Pembayaran
                    </span>

                </a>

            </li>


            {{-- LAPORAN --}}

            <li>

                <a
                    href="{{ url('/admin/laporan') }}"
                    class="{{ request()->is('admin/laporan*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        📊
                    </span>

                    <span class="menu-text">
                        Laporan
                    </span>

                </a>

            </li>

        </ul>


        {{-- =================================================
             DATA MASTER
        ================================================== --}}

        <div class="menu-title">
            Data Master
        </div>


        <ul class="menu">

            {{-- SISWA --}}

            <li>

                <a
                    href="{{ url('/admin/siswa') }}"
                    class="{{ request()->is('admin/siswa*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        👨‍🎓
                    </span>

                    <span class="menu-text">
                        Data Siswa
                    </span>

                </a>

            </li>


            {{-- ORANG TUA --}}

            <li>

                <a
                    href="{{ url('/admin/orang-tua') }}"
                    class="{{ request()->is('admin/orang-tua*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        👨‍👩‍👦
                    </span>

                    <span class="menu-text">
                        Data Orang Tua
                    </span>

                </a>

            </li>


            {{-- KELAS --}}

            <li>

                <a
                    href="{{ url('/admin/kelas') }}"
                    class="{{ request()->is('admin/kelas*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        🏫
                    </span>

                    <span class="menu-text">
                        Data Kelas
                    </span>

                </a>

            </li>


            {{-- KATEGORI --}}

            <li>

                <a
                    href="{{ url('/admin/kategori') }}"
                    class="{{ request()->is('admin/kategori*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        📑
                    </span>

                    <span class="menu-text">
                        Kategori Pembayaran
                    </span>

                </a>

            </li>

        </ul>


        {{-- =================================================
             SISTEM
        ================================================== --}}

        <div class="menu-title">
            Sistem
        </div>


        <ul class="menu">

            <li>

                <a
                    href="{{ url('/admin/profil') }}"
                    class="{{ request()->is('admin/profil*') ? 'active' : '' }}"
                >

                    <span class="menu-icon">
                        👤
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

            <div class="navbar-title">

                <h3>
                    @yield('page-title', 'Dashboard')
                </h3>

                <p>
                    Sistem Administrasi Pembayaran
                </p>

            </div>


            <div class="navbar-right">


                {{-- NOTIFIKASI --}}

                <div class="notification-wrapper">

                    <a
                        href="{{ url('/admin/pembayaran') }}"
                        class="notification-btn"
                        title="Pembayaran Menunggu"
                    >

                        🔔

                        <span
                            id="notification-badge"
                            class="notification-badge"
                            style="display:none;"
                        >
                            0
                        </span>

                    </a>

                </div>


                {{-- ADMIN PROFILE --}}

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
                        Logout
                    </button>

                </form>

            </div>

        </header>



        {{-- =================================================
             CONTENT
        ================================================== --}}

        <section class="content">

            @yield('content')

        </section>

    </main>



    {{-- =====================================================
         SCRIPT
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


        loadNotification();


        setInterval(
            loadNotification,
            10000
        );

    </script>


    @stack('scripts')

</body>

</html>