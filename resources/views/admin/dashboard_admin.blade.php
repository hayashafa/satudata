@extends('layouts.app')

@section('main_container_class', 'container-fluid px-0')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- SIDEBAR --}}
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
        </div>

        {{-- MAIN CONTENT ADMIN BIASA --}}
        <div class="col-md-10">

            <h2 class="mb-4">Dashboard</h2>

            {{-- KARTU STATISTIK --}}
            <div class="row mb-4">

                {{-- JUMLAH DATASET YANG DIUPLOAD --}}
                <div class="col-md-3 mb-3">
                    <div class="card bg-dark-blue text-white h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="mb-2">Jumlah Dataset Diupload</h5>
                                <h2 class="mb-3">{{ $totalDatasets }}</h2>
                            </div>
                            <a href="{{ route('admin.datasets.index') }}" class="btn btn-sm btn-light">
                                Lihat File
                            </a>
                        </div>
                    </div>
                </div>

                {{-- DATASET SUDAH DISETUJUI --}}
                <div class="col-md-3 mb-3">
                    <div class="card bg-dark-blue text-white h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="mb-2">Dataset yang Sudah Disetujui</h5>
                                <h2 class="mb-3">{{ $approvedDatasets }}</h2>
                            </div>
                            <a href="{{ route('admin.datasets.approved') }}" class="btn btn-sm btn-light">
                                Lihat File
                            </a>
                        </div>
                    </div>
                </div>

                {{-- KARTU TAMBAH DATASET --}}
                <div class="col-md-3 mb-3">
                    <div class="card bg-dark-blue text-white h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="mb-2">Tambah Dataset Baru</h5>
                                <p class="mb-3">Upload dataset milik Anda ke sistem.</p>
                            </div>
                            <a href="{{ route('admin.datasets.create') }}" class="btn btn-sm btn-light">
                                Tambah Dataset
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            {{-- INFO AKUN UNTUK ADMIN BIASA --}}
            <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                <h4 class="mb-0">Info Akun</h4>
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-sm btn-outline-primary">Edit Profil</a>
            </div>
            <table class="table table-bordered">
                <tr>
                    <th>Nama Asli</th>
                    <td>{{ auth()->user()->name }}</td>
                </tr>
                <tr>
                    <th>Peran</th>
                    <td>
                        @php($role = auth()->user()->role)
                        @if ($role === 'superadmin')
                            Super Admin
                        @elseif ($role === 'admin')
                            Admin
                        @else
                            {{ ucfirst($role ?? '-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ auth()->user()->email }}</td>
                </tr>
                <tr>
                    <th>Unit Kerja</th>
                    <td>{{ auth()->user()->workplace ?: 'Belum diisi' }}</td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <td>
                        @php($gender = auth()->user()->gender)
                        @if ($gender === 'L' || strtolower($gender) === 'laki-laki')
                            Laki-laki
                        @elseif ($gender === 'P' || strtolower($gender) === 'perempuan')
                            Perempuan
                        @else
                            Belum diisi
                        @endif
                    </td>
                </tr>
            </table>

        </div>
    </div>
</div>
@endsection
