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
            <h2 class="mb-3">Tambah Pengguna</h2>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="card p-3">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Peran</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    </select>
                    @error('role')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
