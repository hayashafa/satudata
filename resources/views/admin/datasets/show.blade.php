@extends('layouts.app')

@section('main_container_class', 'container-fluid px-0')

@section('content')
<div class="container-fluid py-3">
    <div class="row">

        {{-- SIDEBAR --}}
        <div class="col-md-2 sidebar-bsn">
            @if(session('yii_user.role') === 'superadmin')
                <ul class="list-group">
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.dashboard') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.dashboard') ? ' sidebar-link-active' : '' }}">
                            Ringkasan Dashboard
                        </a>
                    </li>
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.datasets.index') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.datasets.index') ? ' sidebar-link-active' : '' }}">
                            Semua Dataset
                        </a>
                    </li>
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.categories.index') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.categories.index') ? ' sidebar-link-active' : '' }}">
                            Kategori
                        </a>
                    </li>
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.users.index') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.users.index') ? ' sidebar-link-active' : '' }}">
                            Pengguna Terdaftar
                        </a>
                    </li>
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.dashboard.rekapanUser') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.dashboard.rekapanUser') ? ' sidebar-link-active' : '' }}">
                            Rekapan User
                        </a>
                    </li>
                </ul>
            @else
                <ul class="list-group">
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.dashboard') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.dashboard') ? ' sidebar-link-active' : '' }}">
                            Ringkasan Dashboard
                        </a>
                    </li>
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.datasets.index') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.datasets.index') ? ' sidebar-link-active' : '' }}">
                            Semua Dataset Diupload
                        </a>
                    </li>
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.datasets.approved') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.datasets.approved') ? ' sidebar-link-active' : '' }}">
                            Dataset yang Disetujui
                        </a>
                    </li>
                    <li class="list-group-item p-0">
                        <a href="{{ route('admin.datasets.create') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.datasets.create') ? ' sidebar-link-active' : '' }}">
                            Tambah Dataset Baru
                        </a>
                    </li>
                </ul>
            @endif
        </div>

        {{-- MAIN CONTENT --}}
        <div class="col-md-10">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="m-0">Detail Dataset</h2>
                    <p class="text-muted small mb-0">Informasi lengkap mengenai dataset yang diupload.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.datasets.index') }}" class="btn btn-outline-secondary btn-sm">Kembali ke Daftar</a>

                    @if(
                        session('yii_user.role') === 'superadmin' ||
                        ((int) session('yii_user.id') === (int) $dataset->user_id && $dataset->status !== 'approved')
                    )
                        <a href="{{ route('admin.datasets.edit', $dataset->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    @endif

                    @if(session('yii_user.role') === 'superadmin' && $dataset->status === 'pending')
                        <form action="{{ route('admin.datasets.approve', $dataset->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve dataset ini?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h4 class="card-title mb-1">{{ $dataset->title }}</h4>
                    <p class="text-muted mb-3">Oleh {{ optional($dataset->user)->name ?? '-' }}</p>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <dl class="row mb-0 small">
                                <dt class="col-sm-4">Kategori</dt>
                                <dd class="col-sm-8">{{ optional($dataset->category)->name ?? '-' }}</dd>

                                <dt class="col-sm-4">Tahun</dt>
                                <dd class="col-sm-8">{{ $dataset->year ?? '-' }}</dd>

                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    @if ($dataset->status === 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif ($dataset->status === 'pending')
                                        <span class="badge bg-secondary">Menunggu</span>
                                    @elseif ($dataset->status === 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-light text-muted">-</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row mb-0 small">
                                <dt class="col-sm-4">Uploader</dt>
                                <dd class="col-sm-8">{{ optional($dataset->user)->name ?? '-' }}</dd>

                                <dt class="col-sm-4">Dibuat</dt>
                                <dd class="col-sm-8">{{ $dataset->created_at ?? '-' }}</dd>

                                <dt class="col-sm-4">Terakhir Update</dt>
                                <dd class="col-sm-8">{{ $dataset->updated_at ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>

                    @if (!empty($dataset->description))
                        <h6>Deskripsi</h6>
                        <p class="mb-0">{{ $dataset->description }}</p>
                    @else
                        <p class="text-muted mb-0">Belum ada deskripsi yang dituliskan.</p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <strong>File Dataset</strong>
                        </div>
                        <div class="card-body">
                            @if (!empty($dataset->file_path))
                                <p class="mb-2 small text-muted">{{ $dataset->file_path }}</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('datasets.downloadFile', $dataset->id) }}" class="btn btn-sm btn-primary">Download</a>
                                    <a href="{{ route('datasets.viewFile', $dataset->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">Lihat</a>
                                </div>
                            @else
                                <p class="text-muted mb-0">Belum ada file yang diupload.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <strong>Gambar</strong>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            @if (!empty($dataset->image))
                                <img src="{{ asset('storage/' . $dataset->image) }}" alt="Gambar Dataset" class="img-fluid rounded border">
                            @else
                                <p class="text-muted mb-0">Tidak ada gambar.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
