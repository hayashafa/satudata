<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Portal Data Sederhana</title>

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* ===== Layout dasar: sticky footer ===== */
        html,
        body {
            height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f5f7fb;
        }

        main {
            flex: 1 0 auto;
        }

        footer {
            flex-shrink: 0;
        }

        /* ===== Footer Custom ===== */
        .footer-bsn {
            background-color: #0052a4; /* biru utama */
            color: #ffffff;
            font-size: 1rem;
        }

        .footer-bsn-box {
            padding-top: 1.25rem;   /* sedikit lebih kecil dari pt-4 default */
            padding-bottom: 1.25rem;
        }

        .footer-bsn a {
            color: #ffffff;
            text-decoration: none;
        }

        .footer-bsn a:hover {
            text-decoration: underline;
        }

        .footer-title {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .footer-bsn-column p {
            margin-bottom: 0.25rem;
        }

        .footer-bsn-column ul li {
            margin-bottom: 0.15rem;
        }

        .footer-bsn-bottom {
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.25);
            padding-top: 0.6rem !important;
            padding-bottom: 0.6rem !important;
        }

        /* ===== Search Bar (halaman datasets public) ===== */
        .search-bar {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 900px;
            background-color: #ffffff;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .search-bar__category {
            max-width: 180px;
            border-radius: 999px 0 0 999px;
            border-right: 1px solid #e5e7eb !important;
            padding-left: 18px;
            padding-right: 18px;
            font-size: 0.9rem;
        }

        .search-bar__input {
            border-radius: 0;
            box-shadow: none !important;
            font-size: 0.95rem;
        }

        .search-bar__input::placeholder {
            color: #9ca3af;
        }

        .search-bar__button {
            border: none;
            border-radius: 0 999px 999px 0;
            padding-right: 18px;
            padding-left: 12px;
            color: #6b7280;
        }

        .search-bar__category:focus,
        .search-bar__input:focus,
        .search-bar__button:focus {
            outline: none;
            box-shadow: none;
        }

        /* ===== Navbar Custom ===== */
        .nav-link {
            color: #000 !important;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .nav-link:hover {
            color: #003c8f !important;
        }

        /* ===== Dataset Cards ===== */
        .dataset-card {
            border-radius: 18px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dataset-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        }

        .dataset-image {
            height: 180px;
            object-fit: cover;
        }

        .dataset-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .dataset-desc {
            font-size: 0.9rem;
            color: #6b7280;
            min-height: 3.2rem;
        }

        /* Tombol Login */
        .btn-login,
        .btn-primary {
            background: linear-gradient(90deg, #0d6efd, #1363df);
            border: none;
            color: #fff !important;
            padding: 6px 22px;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.25s ease;
        }

        .btn-login:hover,
        .btn-primary:hover {
            background: linear-gradient(90deg, #0a58ca, #0d6efd);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 80, 200, 0.3);
        }
        
        /* ===== Responsive tweak untuk search bar di layar kecil ===== */
        @media (max-width: 576px) {
            .search-bar {
                flex-wrap: wrap;
                border-radius: 18px;
            }

            .search-bar__category {
                max-width: 100%;
                width: 100%;
                border-right: none !important;
                border-bottom: 1px solid #e5e7eb !important;
                border-radius: 18px 18px 0 0;
            }

            .search-bar__input {
                border-radius: 0;
            }

            .search-bar__button {
                width: 100%;
                border-radius: 0 0 18px 18px;
                justify-content: center;
                display: flex;
                align-items: center;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom py-2">
        <div class="container">

            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="https://www.bsn.go.id">
                <img src="/images/bsn-logo.png" alt="Logo" height="40" class="me-2">

                <div class="d-flex flex-column lh-1">
                    <span class="fw-bold text-uppercase"
                          style="font-size: 11px; color:#003c8f;">
                        Semua untuk satu
                    </span>
                    <span class="text-uppercase"
                          style="font-size: 10px; color:#003c8f;">
                        Indonesia
                    </span>
                </div>
            </a>

            <!-- Toggle Mobile -->
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <!-- Menu -->
<div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
    <ul class="navbar-nav ms-auto align-items-lg-center text-end">

        <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">
                Halaman Utama
            </a>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Profil
            </a>
            <ul class="dropdown-menu" aria-labelledby="profilDropdown">
                <li>
                    <a class="dropdown-item" href="{{ route('profil') }}">Profil BSN Data</a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('tentang') }}">Tentang BSN</a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('datasets.public') }}">
                Datasets
            </a>
        </li>
        <!-- ===== TOMBOL LOGIN / DASHBOARD / LOGOUT ===== -->
@if (!session('is_logged_in'))
    <!-- Jika BELUM login -->
    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
        <a class="btn btn-primary px-4" href="{{ route('login') }}">
            Login
        </a>
    </li>
@else
    <!-- Jika SUDAH login (admin / superadmin) -->
    <li class="nav-item ms-lg-3 mt-2 mt-lg-0 me-2">
        <a class="btn btn-outline-primary px-4" href="{{ route('admin.dashboard') }}">
            Dashboard
        </a>
    </li>

    <li class="nav-item mt-2 mt-lg-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger px-4">
                Logout
            </button>
        </form>
    </li>
@endif

    </ul>
</div>

        </div>
    </nav>
    

    <!-- KONTEN -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- FOOTER -->
    @include('layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>

    @stack('scripts')

</body>

</html>
