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
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom py-2">
        <div class="container">

            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="https://www.bsn.go.id">
                <img src="/images/bsn-logo.png" alt="Logo" height="40" class="me-2">

                <div class="d-flex flex-column lh-1">
                    <span class="fw-bold text-uppercase logo-tagline-main">
                        Semua untuk satu
                    </span>
                    <span class="text-uppercase logo-tagline-sub">
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
    <main class="py-4 flex-grow-1">
        <div class="@yield('main_container_class', 'container')">
            <button id="sidebarToggle"
                    type="button"
                    class="btn btn-outline-primary btn-sm mb-3 d-none">
                <i class="fa fa-bars me-1"></i> Menu Sidebar
            </button>

            @yield('content')
        </div>
    </main>

    <!-- FOOTER -->
    @include('layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sidebar = document.querySelector('.sidebar-bsn');
            var toggleBtn = document.getElementById('sidebarToggle');

            if (!sidebar || !toggleBtn) {
                return;
            }

            // Tampilkan tombol hanya jika di halaman ini ada sidebar
            toggleBtn.classList.remove('d-none');

            var mainCol = sidebar.nextElementSibling;

            toggleBtn.addEventListener('click', function () {
                var isHidden = sidebar.classList.toggle('sidebar-hidden');

                if (!mainCol) {
                    return;
                }

                if (isHidden) {
                    mainCol.classList.remove('col-md-10');
                    mainCol.classList.add('col-md-12');
                } else {
                    mainCol.classList.remove('col-md-12');
                    mainCol.classList.add('col-md-10');
                }
            });
        });
    </script>

    @stack('scripts')

</body>

</html>
