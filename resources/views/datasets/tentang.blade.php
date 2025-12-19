@extends('layouts.app')

@section('content')

{{-- HEADER BANNER --}}
<div class="text-white py-5"
     style="background: linear-gradient(120deg, #003d99, #005dcc); padding: 70px 0;">
    <div class="container">
        <h1 class="fw-bold mb-2">Tentang BSN</h1>
        <p class="m-0">
            <a href="{{ route('home') }}" class="text-white text-decoration-none">
                Halaman Utama
            </a>
            /
            <span class="text-warning fw-semibold">Tentang BSN</span>
        </p>
    </div>
</div>

{{-- KONTEN TENTANG --}}
<div class="container my-5">

    <div class="row align-items-start g-4">

        {{-- KIRI: CERITA --}}
        <div class="col-md-6">
            
            <h2 class="fw-bold text-primary">Badan Standardisasi Nasional</h2>
            <p class="text-muted mb-4">Sejak 1997</p>

            <div class="bg-light p-4 rounded-3 shadow-sm">
                <h4 class="fw-bold mb-3">Cerita Kami</h4>

                <p class="text-secondary" style="text-align: justify;">
                    Badan Standardisasi Nasional (BSN) adalah lembaga pemerintah
                    yang bertanggung jawab mengembangkan, membina, dan mengoordinasikan
                    kegiatan standardisasi di Indonesia. BSN memastikan bahwa standar 
                    nasional diterapkan untuk meningkatkan kualitas produk, layanan, 
                    dan daya saing Indonesia.
                </p>

                <p class="text-secondary" style="text-align: justify;">
                    Dengan komitmen tinggi terhadap mutu dan akreditasi, BSN bekerja
                    bersama berbagai sektor untuk memastikan penerapan SNI di berbagai
                    bidang industri, teknologi, dan pelayanan publik. Tujuan utama BSN
                    adalah memberikan perlindungan bagi konsumen, mendukung inovasi,
                    serta meningkatkan daya saing bangsa.
                </p>

                <p class="text-secondary" style="text-align: justify;">
                    Hingga saat ini, BSN terus bertransformasi untuk menghadirkan 
                    sistem standardisasi yang modern, terpercaya, dan mampu menjawab 
                    tantangan global melalui peningkatan kualitas SNI dan layanan akreditasi.
                </p>

            </div>
        </div>

        {{-- KANAN: GAMBAR --}}
        <div class="col-md-6 text-center">

            {{-- LOGO --}}
            <img src="/images/LogoBSN.jpeg"
                 alt="Logo BSN"
                 class="img-fluid mb-4 rounded shadow-sm"
                 style="max-height: 180px; object-fit: contain;">

            {{-- GEDUNG --}}
            <img src="/images/gedung1.jpeg"
                 alt="Gedung BSN"
                 class="img-fluid rounded-3 shadow"
                 style="max-height: 320px; width: 100%; object-fit: cover;">
        </div>

    </div>
</div>

@endsection
