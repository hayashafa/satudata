@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-start g-4">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('datasets.public') }}">Datasets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail Dataset</li>
                </ol>
            </nav>

            <h1 class="h3 fw-bold mb-2">{{ $dataset->title }}</h1>

            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                <span class="badge bg-primary">
                    {{ $dataset->category->name ?? 'Tidak ada kategori' }}
                </span>
                @if($dataset->year)
                    <span class="badge bg-secondary">{{ $dataset->year }}</span>
                @endif
                @if($dataset->creator)
                    <span class="badge bg-info text-dark">{{ $dataset->creator }}</span>
                @endif
            </div>

            @if($dataset->description)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white pb-1">
                        <h2 class="h6 fw-semibold mb-0">Deskripsi Dataset</h2>
                    </div>
                    <div class="card-body pt-2">
                        <p class="text-muted mb-0">{{ $dataset->description }}</p>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white pb-1">
                    <h2 class="h6 fw-semibold mb-0">Informasi Dataset</h2>
                </div>
                <div class="card-body pt-2">
                    <div class="row g-2 small text-muted">
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold me-2">ID Dataset</span>
                                <span>{{ $dataset->id }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold me-2">Format</span>
                                <span>
                                    @php
                                        $ext = $dataset->file_path ? strtolower(pathinfo($dataset->file_path, PATHINFO_EXTENSION)) : '-';
                                    @endphp
                                    {{ $ext ?: '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold me-2">Status</span>
                                <span><span class="badge bg-success">Approved</span></span>
                            </div>
                        </div>
                        @if($dataset->approved_at)
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold me-2">Disetujui pada</span>
                                <span>{{ $dataset->approved_at }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-2">
                @if($dataset->file_path)
                    <a href="{{ route('datasets.viewFile', $dataset->id) }}" class="btn btn-outline-primary">
                        Lihat File
                    </a>
                    <a href="{{ route('datasets.downloadFile', $dataset->id) }}" class="btn btn-primary">
                        Download
                    </a>
                @endif
                <a href="{{ route('datasets.public') }}" class="btn btn-outline-secondary">
                    Kembali ke Daftar Dataset
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            @if($dataset->image)
                <div class="card shadow-sm mb-3 border-0 overflow-hidden">
                    <img src="{{ asset('storage/'.$dataset->image) }}" class="card-img-top" alt="Gambar dataset">
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    Informasi Tambahan
                </div>
                <div class="card-body small text-muted">
                    <p class="mb-1"><strong>Kategori:</strong> {{ $dataset->category->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Tahun:</strong> {{ $dataset->year ?? '-' }}</p>
                    <p class="mb-0"><strong>Pembuat:</strong> {{ $dataset->creator ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
