
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Alat</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 44px;
            height: 44px;
            background: #2563eb;
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .brand h2 {
            font-size: 19px;
            color: #0f172a;
        }

        .brand p {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        nav a {
            text-decoration: none;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
        }

        nav a:hover {
            color: #2563eb;
        }

        .user-area {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .greeting {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .logout-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        .auth-link {
            text-decoration: none;
            color: #2563eb;
            font-weight: 600;
            font-size: 14px;
        }

        /* MAIN */
        main {
            flex: 1;
            padding: 65px 7%;
        }

        .welcome-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 45px;
            max-width: 1100px;
            margin: 0 auto;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.04);
        }

        .welcome-label {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 8px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .welcome-card h1 {
            font-size: 34px;
            line-height: 1.3;
            color: #0f172a;
            margin-bottom: 15px;
        }

        .welcome-card h1 span {
            color: #2563eb;
        }

        .welcome-card .description {
            color: #64748b;
            line-height: 1.8;
            max-width: 650px;
            font-size: 15px;
        }

        .info-section {
            max-width: 1100px;
            margin: 30px auto 0;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .info-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 25px;
        }

        .info-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #eff6ff;
            font-size: 21px;
            margin-bottom: 16px;
        }

        .info-card h3 {
            font-size: 16px;
            margin-bottom: 9px;
            color: #1e293b;
        }

        .info-card p {
            font-size: 13px;
            line-height: 1.7;
            color: #64748b;
        }

        /* FOOTER */
        footer {
            background: #0f172a;
            color: #cbd5e1;
            padding: 25px 7%;
            text-align: center;
            font-size: 13px;
        }

        footer strong {
            color: white;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            header {
                padding: 16px 5%;
            }

            nav {
                order: 3;
                width: 100%;
                justify-content: center;
                gap: 20px;
            }

            .brand h2 {
                font-size: 16px;
            }

            .user-area {
                gap: 8px;
            }

            .greeting {
                font-size: 12px;
            }

            .logout-btn {
                padding: 8px 11px;
            }

            main {
                padding: 35px 5%;
            }

            .welcome-card {
                padding: 28px 22px;
            }

            .welcome-card h1 {
                font-size: 27px;
            }

            .info-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 420px) {
            header {
                justify-content: center;
            }

            .user-area {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header>
        <div class="brand">
            <div class="logo">⚙</div>

            <div>
                <h2>Sistem Peminjaman Alat</h2>
                <p>Platform Peminjaman Peralatan</p>
            </div>
        </div>

        <nav>
            <a href="#beranda">Beranda</a>
            <a href="#tentang">Tentang</a>
            @if (auth()->check() && auth()->user()->isPeminjam())
                <a href="{{ route('pengembalian.index') }}">
                    Riwayat Peminjaman & Pengembalian
                </a>
            @endif
        </nav>

        <div class="user-area">
            @auth
                <span class="greeting">
                    Hi, {{ Auth::user()->username ?? Auth::user()->name }}
                </span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="auth-link">
                    Login
                </a>

                <a href="{{ route('register') }}" class="auth-link">
                    Register
                </a>
            @endauth
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main id="beranda">

        <section class="welcome-card">
            <span class="welcome-label">
                DASHBOARD PEMINJAM
            </span>

            @auth
                <h1>
                    Selamat Datang,
                    <span>{{ Auth::user()->name }}!</span>
                </h1>
            @else
                <h1>
                    Selamat Datang di
                    <span>Sistem Peminjaman Alat</span>
                </h1>
            @endauth

            <p class="description">
                Selamat datang di aplikasi Sistem Peminjaman Alat.
                Melalui platform ini, pengguna dapat mengakses informasi
                peralatan dan mengelola aktivitas peminjaman dengan lebih
                mudah, praktis, dan terorganisir.
            </p>
        </section>

        <!-- INFORMASI -->
        <section class="info-section" id="tentang">

            <div class="info-card">
                <div class="info-icon">📦</div>
                <h3>Informasi Peralatan</h3>
                <p>
                    Lihat informasi peralatan yang tersedia
                    untuk kebutuhan peminjaman.
                </p>
            </div>

            <div class="info-card">
                <div class="info-icon">📝</div>
                <h3>Peminjaman Alat</h3>
                <p>
                    Kelola proses pengajuan peminjaman
                    peralatan melalui sistem.
                </p>
            </div>

            <div class="info-card">
                <div class="info-icon">📋</div>
                <h3>Riwayat Peminjaman</h3>
                <p>
                    Pantau informasi dan status peminjaman
                    peralatan yang telah diajukan.
                </p>
            </div>

        </section>

    </main>

    <!-- FOOTER -->
    <footer>
        <p>
            &copy; {{ date('Y') }}
            Sistem Peminjaman Alat.
            Dikembangkan oleh <strong>Achmad David Agustian</strong>.
        </p>
    </footer>

</body>
</html>
