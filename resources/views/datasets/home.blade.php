@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
            <h1 class="fw-bold lh-sm" style="font-size: 44px;">
                Cari Data Apa <span class="text-danger">Hari Ini</span>?
            </h1>
            <p class="mt-3 fw-semibold text-secondary">Mudah, Cepat, dan Akurat</p>
            <p class="text-muted">Info data resmi dari Badan Standardisasi Nasional (BSN) untuk mendukung kebutuhan riset, analisis, dan pengembanganmu.</p>

            {{-- Form pencarian --}}
            <form class="mt-4" method="GET" action="{{ route('datasets.public') }}">
                <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2">
                    <div class="flex-grow-1">
                        <div class="search-bar">
                            <select name="type" class="search-bar__category form-select border-0">
                                <option value="" {{ request('type') ? '' : 'selected' }}>Semua Dataset</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string)request('type') === (string)$category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <input
                                type="text"
                                name="q"
                                class="search-bar__input form-control border-0"
                                placeholder="Cari dataset"
                                value="{{ request('q') }}">

                            <button type="submit" class="search-bar__button btn">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        <a href="{{ route('datasets.public') }}" class="btn btn-success w-100 w-md-auto">
                            Semua Data
                        </a>
                    </div>
                </div>
            </form>

            {{-- Box informasi sambutan --}}
            <div class="alert alert-success mt-4" role="alert">
                Selamat datang! Platform ini menyediakan berbagai dataset terkait standardisasi nasional dari BSN yang dapat kamu akses dengan mudah dan terpercaya.
            </div>
        </div>

        <div class="col-md-6 d-flex justify-content-center align-items-center">
            <img src="/images/home1.jpg"
                 alt="Ilustrasi Data"
                 class="img-fluid rounded shadow-sm"
                 style="max-width: 80%; height: auto; object-fit: cover;">
        </div>
    </div>
</div>
@endsection