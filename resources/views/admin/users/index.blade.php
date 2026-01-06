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
            <h2 class="mb-3">Daftar Pengguna</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-3 align-items-center">
                <div class="col-lg-5 col-md-6 col-12 mb-2 mb-md-0">
                    <input type="text" name="search" value="{{ $search ?? request('search') }}" class="form-control" placeholder="Cari nama atau email pengguna">
                </div>
                <div class="col-lg-2 col-md-3 col-6 mb-2 mb-md-0">
                    <select name="sort" class="form-select">
                        @php($currentSort = $sort ?? request('sort', 'latest'))
                        <option value="latest" {{ $currentSort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="name_az" {{ $currentSort === 'name_az' ? 'selected' : '' }}>Nama A - Z</option>
                        <option value="name_za" {{ $currentSort === 'name_za' ? 'selected' : '' }}>Nama Z - A</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-6 mb-2 mb-md-0 d-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
                </div>
                <div class="col-lg-2 col-12 mt-2 mt-lg-0 d-flex justify-content-lg-end">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success w-100 w-lg-auto">Tambah Pengguna</a>
                </div>
            </form>

            <table class="table table-bordered table-responsive-md">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Unit Kerja</th>
                        <th>Tanggal Daftar</th>
                        <th>Status Akun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $i => $user)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->workplace ?? '-' }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>
                            @if($user->is_frozen)
                                <span class="badge bg-danger">Dibekukan</span>
                            @else
                                <span class="badge bg-success">Aktif</span>
                            @endif
                        </td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">Detail</a>

                            @if($user->is_frozen)
                                <form action="{{ route('admin.users.unfreeze', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-warning" onclick="return confirm('Aktifkan kembali user ini?')">Aktifkan</button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.freeze', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-secondary" onclick="return confirm('Bekukan user ini? User tidak bisa akses halaman admin.');">Bekukan</button>
                                </form>
                            @endif

                            <form action="{{ route('admin.users.destroy', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection