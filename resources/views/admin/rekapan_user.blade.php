@extends('layouts.app')

@section('main_container_class', 'container-fluid px-0')

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        <div class="col-md-2 sidebar-bsn">
            <ul class="list-group">
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.dashboard') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.dashboard') ? ' sidebar-link-active' : '' }}">
                        Ringkasan Dashboard
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.index') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.datasets.index') && !request('status') ? ' sidebar-link-active' : '' }}">
                        Semua Dataset
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.index', ['status' => 'pending']) }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.datasets.index') && request('status') === 'pending' ? ' sidebar-link-active' : '' }}">
                        Dataset Menunggu Review
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.index', ['status' => 'approved']) }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.datasets.index') && request('status') === 'approved' ? ' sidebar-link-active' : '' }}">
                        Dataset yang Disetujui
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
        </div>

        <div class="col-md-10">
            <h2 class="mb-3">Rekapan User</h2>
            <p class="text-muted small mb-3">
                Ringkasan aktivitas semua admin/superadmin: berapa banyak dataset yang diupload, disetujui, pending,
                dan berapa dataset yang pernah diedit.
            </p>

            @if($topUploaders->isNotEmpty())
                @php
                    $topUploader = $topUploaders->first();
                @endphp
                <div class="alert alert-success mb-3" role="alert">
                    <span class="fw-semibold">Informasi:</span>
                    Terdapat Top Uploader yaitu <strong>{{ $topUploader->name }}</strong> dengan total upload
                    <strong>{{ $topUploader->datasets_count }}</strong> dataset.
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Nama</th>
                                    <th class="text-end">Total Upload</th>
                                    <th class="text-end">Disetujui</th>
                                    <th class="text-end">Pending</th>
                                    <th class="text-end">Dataset Pernah Diedit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topUploaders as $i => $user)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td class="text-end">{{ $user->datasets_count }}</td>
                                        <td class="text-end">{{ $user->approved_datasets_count }}</td>
                                        <td class="text-end">{{ $user->pending_datasets_count }}</td>
                                        <td class="text-end">{{ $user->edited_datasets_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            Belum ada data rekapan aktivitas admin/superadmin.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
