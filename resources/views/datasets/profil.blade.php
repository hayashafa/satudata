@extends('layouts.app')

@section('content')

{{-- HEADER BANNER --}}
<div class="text-white py-5"
     style="background: linear-gradient(120deg, #003d99, #005dcc); padding: 70px 0;">
    <div class="container">
        <h1 class="fw-bold mb-2">Profil BSN Data</h1>
        <p class="m-0">
            <a href="{{ route('home') }}" class="text-white text-decoration-none">
                Halaman Utama
            </a>
            /
            <span class="text-warning fw-semibold">Profil</span>
        </p>
    </div>
</div>

{{-- KONTEN PROFIL --}}
<div class="container my-5">
    <div class="row align-items-start g-4">

        {{-- KIRI: Profil Singkat --}}
        <div class="col-md-6">
            <h2 class="fw-bold text-primary">Portal BSN Data</h2>
            <p class="text-muted mb-4">Profil Singkat</p>

            <div class="bg-light p-4 rounded-3 shadow-sm">
                <h4 class="fw-bold mb-3">Siapa Kami</h4>
                <p class="text-secondary" style="text-align: justify;">
                    Portal BSN Data merupakan gerbang akses data sederhana yang dikembangkan
                    untuk memudahkan publik dalam menemukan dataset yang dikelola oleh
                    Badan Standardisasi Nasional (BSN). Melalui portal ini, pengguna dapat
                    menjelajahi berbagai dataset yang telah dikurasi dan dipublikasikan.
                </p>

                <p class="text-secondary" style="text-align: justify;">
                    Portal ini ditujukan sebagai langkah awal menuju layanan data yang
                    lebih terbuka, terstruktur, dan mudah dimanfaatkan oleh masyarakat,
                    peneliti, maupun pemangku kepentingan lainnya.
                </p>
            </div>
        </div>

        {{-- KANAN: Visi Misi / Highlight --}}
        <div class="col-md-6">
            <div class="bg-primary text-white p-4 rounded-3 shadow-sm h-100">
                <h4 class="fw-bold mb-3">Tujuan Portal</h4>
                <ul class="mb-4">
                    <li>Menyediakan akses cepat ke dataset yang telah disetujui BSN.</li>
                    <li>Mendukung transparansi dan pemanfaatan data standardisasi.</li>
                    <li>Menjadi dasar pengembangan portal data yang lebih komprehensif di masa depan.</li>
                </ul>

                <h4 class="fw-bold mb-3">Kontak</h4>
                <p class="mb-1">Untuk masukan atau permintaan data tambahan, silakan hubungi BSN melalui kanal resmi yang tercantum pada bagian footer.</p>
            </div>
        </div>

    </div>
</div>

@endsection
