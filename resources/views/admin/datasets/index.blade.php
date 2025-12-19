@extends('layouts.app')

@section('main_container_class', 'container-fluid px-0')

@section('content')
<div class="container-fluid py-3">
    <div class="row">

        {{-- SIDEBAR --}}
        <div class="col-md-2 sidebar-bsn">
            @if(auth()->user() && auth()->user()->isSuperAdmin())
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
                    <h2 class="m-0">{{ $title ?? 'Daftar Dataset yang Diupload' }}</h2>
                    <p class="text-muted small mb-0">Kelola dataset yang diunggah admin dan superadmin. Gunakan tombol aksi di kanan untuk menyetujui, mengedit, atau menghapus.</p>
                </div>

                <a href="{{ route('admin.datasets.create') }}" class="btn btn-primary">
                    + Tambah Dataset
                </a>
            </div>

            {{-- JIKA DATA KOSONG --}}
            @if ($datasets->isEmpty())
                <div class="alert alert-info">
                    Belum ada dataset yang tersedia.
                </div>

            @else

                {{-- TABEL DATASET --}}
                <div class="table-responsive shadow-sm rounded bg-white">
                    <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Diupload Oleh</th>
                        <th>File</th>
                        <th>Gambar</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($datasets as $data)
                    <tr>
                        {{-- JUDUL --}}
                        <td>{{ $data->title }}</td>

                        {{-- DESKRIPSI (dipendekkan) --}}
                        <td class="small text-muted" style="max-width: 260px;">
                            {{ Str::limit($data->description ?? '-', 80) }}
                        </td>

                        {{-- KATEGORI --}}
                        <td>{{ optional($data->category)->name ?? '-' }}</td>

                        {{-- TAHUN --}}
                        <td>{{ $data->year }}</td>

                        {{-- STATUS --}}
                        <td class="text-center">
                            @if ($data->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif ($data->status === 'pending')
                                <span class="badge bg-secondary">Menunggu</span>
                            @else
                                <span class="badge bg-light text-muted">-</span>
                            @endif
                        </td>

                        {{-- USER PENGUPLOAD --}}
                        <td>{{ optional($data->user)->name ?? 'Tidak diketahui' }}</td>

                        {{-- FILE --}}
                        <td class="text-nowrap">
                            @if ($data->file_path)
                                <a href="{{ route('datasets.viewFile', $data->id) . '?v=' . optional($data->updated_at)->timestamp }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    Lihat File
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- GAMBAR --}}
                        <td>
                            @if ($data->image)
                                <img src="{{ asset('storage/' . $data->image) }}" 
                                     width="80" 
                                     class="rounded border">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="d-flex flex-column gap-1">
                            {{-- TOMBOL APPROVE (hanya untuk superadmin & dataset pending) --}}
                            @if(auth()->user()->isSuperAdmin() && $data->status === 'pending')
                                <form action="{{ route('datasets.approve', $data->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Approve dataset ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        Setujui
                                    </button>
                                </form>
                            @endif

                            @if(
                                auth()->user()->isSuperAdmin() ||
                                (auth()->id() === $data->user_id && $data->status !== 'approved')
                            )
                                {{-- TOMBOL EDIT --}}
                                <a href="{{ route('admin.datasets.edit', $data->id) }}" 
                                   class="btn btn-warning btn-sm w-100">
                                    Edit
                                </a>

                                {{-- TOMBOL HAPUS --}}
                                <form action="{{ route('admin.datasets.destroy', $data->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus dataset ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm w-100">
                                        Hapus
                                    </button>
                                </form>
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
