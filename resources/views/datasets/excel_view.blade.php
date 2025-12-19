@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="row mb-4 align-items-start g-4">
        <div class="col-lg-7">
            <h1 class="h3 fw-bold mb-2">{{ $dataset->title }}</h1>
            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                <span class="badge bg-primary">
                    {{ $dataset->category->name ?? 'Tanpa Kategori' }}
                </span>
                @if($dataset->year)
                    <span class="badge bg-secondary">{{ $dataset->year }}</span>
                @endif
                @if($dataset->creator)
                    <span class="badge bg-light text-dark">{{ $dataset->creator }}</span>
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

            <div class="d-flex flex-wrap gap-2 mt-1">
                <a href="{{ route('datasets.downloadFile', $dataset->id) }}" class="btn btn-primary">
                    <i class="fa fa-download me-1"></i> Download Dataset
                </a>

                <a href="{{ route('datasets.public') }}" class="btn btn-outline-secondary">
                    Kembali ke Daftar Dataset
                </a>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="alert alert-info mb-0 small" role="alert">
                <strong>Preview Excel</strong><br>
                File ini ditampilkan melalui Office Web Viewer. Untuk pengolahan data lebih lanjut,
                silakan unduh dan buka di aplikasi spreadsheet favoritmu.
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Isi Dataset (Excel)</span>
        </div>
        <div class="ratio ratio-16x9">
            <iframe src="{{ $viewerUrl }}" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

</div>
@endsection
