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
                        <a href="{{ route('admin.datasets.approved') }}"
                           class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none{{ request()->routeIs('admin.datasets.approved') ? ' sidebar-link-active' : '' }}">
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

            {{-- JUDUL HALAMAN --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="m-0">Dataset yang Sudah Disetujui</h2>
                    <p class="text-muted small mb-0">Daftar seluruh dataset yang berstatus disetujui dan tampil di halaman publik.</p>
                </div>
            </div>

            {{-- JIKA DATA KOSONG --}}
            @if ($datasets->isEmpty())
                <div class="alert alert-info">
                    Belum ada dataset yang di-approve.
                </div>
            @else

                {{-- TABEL DATASET APPROVED (VIEW ONLY) --}}
                <div class="table-responsive shadow-sm rounded bg-white">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th>Diupload Oleh</th>
                                <th>File</th>
                                <th>Gambar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datasets as $data)
                                <tr>
                                    <td>{{ $data->title }}</td>
                                    <td class="small text-muted" style="max-width: 260px;">
                                        {{ Str::limit($data->description ?? '-', 80) }}
                                    </td>
                                    <td>{{ optional($data->category)->name ?? '-' }}</td>
                                    <td>{{ $data->year }}</td>
                                    <td>{{ optional($data->user)->name ?? 'Tidak diketahui' }}</td>
                                    <td class="text-nowrap">
                                        @if ($data->file_path)
                                            <a href="{{ route('datasets.viewFile', $data->id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-info">
                                                Lihat File
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->image)
                                            <img src="{{ asset('storage/' . $data->image) }}" width="80" class="rounded border">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif

        </div>
    </div>
</div>
@endsection
