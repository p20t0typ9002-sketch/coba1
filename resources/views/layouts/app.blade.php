
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Sistem Peminjaman Alat')</title>

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

        header {
            background: #fff;
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

        nav a:hover,
        nav a.active {
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

        main {
            flex: 1;
            width: 100%;
            padding: 45px 7%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-heading {
            margin-bottom: 28px;
        }

        .page-heading h1 {
            color: #0f172a;
            font-size: 30px;
            margin-bottom: 9px;
        }

        .page-heading p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 5px 18px rgba(15, 23, 42, .03);
        }

        .btn {
            display: inline-block;
            border: none;
            border-radius: 8px;
            padding: 11px 17px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .alert {
            padding: 14px 17px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
        }

        .alert-success {
            color: #166534;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

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

        @media (max-width: 768px) {
            header {
                padding: 16px 5%;
            }

            nav {
                order: 3;
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 15px;
            }

            .brand h2 {
                font-size: 16px;
            }

            main {
                padding: 30px 5%;
            }

            .page-heading h1 {
                font-size: 25px;
            }

            .user-area {
                gap: 8px;
            }

            .greeting {
                font-size: 12px;
            }
        }

        .user-area {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 14px;
    flex-shrink: 0;
}

.user-area form {
    margin: 0;
}

.user-area .greeting {
    white-space: nowrap;
}

@media (max-width: 600px) {
    .user-area {
        gap: 8px;
    }

    .user-area .greeting {
        font-size: 12px;
    }

    .user-area .logout-btn {
        padding: 7px 10px;
        font-size: 12px;
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

    @stack('styles')
</head>

<body>
    <header>
        <div class="brand">
            <div class="logo">⚙</div>

            <div>
                <h2>Sistem Peminjaman Alat</h2>
                <p>Platform Peminjaman Peralatan</p>
            </div>
        </div>

        <nav>
            
            <a href="{{ route('home') }}"
                class="{{ request()->routeIs('home') ? 'active' : '' }}">
                Beranda
            </a>


            @if(auth()->check() && auth()->user()->isPeminjam())
                <a href="{{ route('peminjaman.katalog') }}"
                   class="{{ request()->routeIs('peminjaman.*') ? 'active' : '' }}">
                    Katalog Alat
                </a>

                <a href="{{ route('peminjaman.riwayat') }}"
                    class="{{ request()->routeIs('peminjaman.riwayat') ? 'active' : '' }}">
                    Riwayat Peminjaman
                </a>

                <a href="{{ route('pengembalian.index') }}"
                    class="{{ request()->routeIs('pengembalian.*') ? 'active' : '' }}">
                    Pengembalian Alat
                </a>
            @endif

            @if(auth()->check() && auth()->user()->isPetugas())
                <a href="{{ route('laporan.index') }}"
                    class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    Laporan
                </a>
            @endif
        </nav>

        <div class="user-area">
            @auth
            <span class="greeting">
            Hi, {{ Auth::user()->username ?? Auth::user()->name }}
        </span>

        @include('components.notification-bell')

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                Logout
            </button>
        </form>
    @else
        <a href="{{ route('login') }}" class="btn">Login</a>
            @endauth
        </div>
    </header>

    <main>
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer>
        <p>
            &copy; {{ date('Y') }}
            Sistem Peminjaman Alat.
            Dikembangkan oleh <strong>Achmad David Agustian</strong>.
        </p>
    </footer>
    @stack('scripts')
</body>
</html>
