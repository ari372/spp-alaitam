<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Dashboard Orang Tua')
    </title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7f6;
            color: #333;
        }

        /* ========================================
           NAVBAR
        ======================================== */

        .navbar {
            height: 82px;

            background: #0f5132;
            color: white;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 35px;

            box-shadow: 0 2px 8px rgba(0,0,0,.12);
        }

        .navbar-brand h2 {
            font-size: 21px;
            margin-bottom: 5px;
        }

        .navbar-brand p {
            font-size: 12px;
            opacity: .8;
        }

        /* ========================================
           NAVBAR RIGHT
        ======================================== */

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .parent-info {
            text-align: right;
        }

        .parent-info strong {
            display: block;
            font-size: 14px;
        }

        .parent-info span {
            display: block;
            font-size: 12px;
            opacity: .8;
            margin-top: 3px;
        }

        .parent-avatar {
            width: 42px;
            height: 42px;

            border-radius: 50%;

            background: #d4a017;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: bold;
            font-size: 18px;
        }

        /* ========================================
           LOGOUT
        ======================================== */

        .logout-form {
            margin-left: 5px;
        }

        .logout-btn {
            border: none;

            background: #dc3545;
            color: white;

            padding: 10px 17px;

            border-radius: 7px;

            font-size: 13px;
            font-weight: bold;

            cursor: pointer;

            transition: .2s;
        }

        .logout-btn:hover {
            background: #bb2d3b;
        }

        /* ========================================
           CONTENT
        ======================================== */

        .content {
            padding: 35px;
            max-width: 1400px;
            margin: auto;
        }

        /* ========================================
           RESPONSIVE
        ======================================== */

        @media(max-width: 700px) {

            .navbar {
                padding: 0 18px;
            }

            .parent-info {
                display: none;
            }

            .content {
                padding: 20px;
            }

        }

    </style>

</head>


<body>

    <!-- ========================================
         NAVBAR
    ======================================== -->

    <header class="navbar">

        <div class="navbar-brand">

            <h2>
                SMP Plus Al-I'tam
            </h2>

            <p>
                Sistem Pembayaran SPP
            </p>

        </div>


        <div class="navbar-right">

            <div class="parent-info">

                <strong>
                    {{ auth()->user()->name ?? 'Orang Tua' }}
                </strong>

                <span>
                    Orang Tua / Wali Murid
                </span>

            </div>


            <div class="parent-avatar">

                {{ strtoupper(substr(auth()->user()->name ?? 'O', 0, 1)) }}

            </div>


            <!-- LOGOUT -->

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


    <!-- ========================================
         CONTENT
    ======================================== -->

    <main class="content">

        @yield('content')

    </main>


</body>

</html>