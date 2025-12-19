@extends('layouts.app') {{-- atau layout khusus admin --}}

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- SIDEBAR --}}
        <div class="col-md-2">
            <ul class="list-group">
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.dashboard') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none">
                        Dashboard
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.index') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none">
                        Datasets
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.index') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none">
                        Dataset Yang Masuk
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.index') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none">
                        Dataset Yang Sudah Di-Approve
                    </a>
                </li>
            </ul>
        </div>

        {{-- MAIN CONTENT --}}
        
        <div class="col-md-10">

            <h2 class="mb-4">Dashboard</h2>

            {{-- KARTU STATISTIK --}}
<div class="row mb-4">

    {{-- JUMLAH DATASET (APPROVED) --}}
    <div class="col-md-3 mb-3">
        <div class="card bg-dark-blue text-white h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h5 class="mb-2">Jumlah Dataset</h5>
                    <h2 class="mb-3">{{ $totalDatasets }}</h2>
                </div>
                <a href="{{ route('admin.datasets.index') }}" class="btn btn-sm btn-light">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>

    {{-- DATASET YANG MASUK (PENDING) --}}
    <div class="col-md-3 mb-3">
        <div class="card bg-dark-blue text-white h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h5 class="mb-2">Dataset Yang Masuk</h5>
                    <h2 class="mb-3">{{ $incomingDatasets }}</h2>
                </div>
                <a href="{{ route('admin.datasets.index') }}" class="btn btn-sm btn-light">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>

    {{-- DATASET SUDAH DI-APPROVE --}}
    <div class="col-md-3 mb-3">
        <div class="card bg-dark-blue text-white h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h5 class="mb-2">Dataset Yang Sudah di Approve</h5>
                    <h2 class="mb-3">{{ $approvedDatasets }}</h2>
                </div>
                <a href="{{ route('admin.datasets.index') }}" class="btn btn-sm btn-light">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>

    {{-- JUMLAH PENGGUNA --}}
    <div class="col-md-3 mb-3">
        <div class="card bg-dark-blue text-white h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h5 class="mb-2">Jumlah Pengguna Terdaftar</h5>
                    <h2 class="mb-3">{{ $totalUsers }}</h2>
                </div>
                {{-- bisa tambahkan link ke daftar user kalau mau --}}
            </div>
        </div>
    </div>

</div>

            {{-- TABEL PENGGUNA --}}
            <h4>Daftar Pengguna</h4>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($latestUsers as $i => $user)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">Detail</a>

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
