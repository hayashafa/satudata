@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        {{-- SIDEBAR --}}
        <div class="col-md-2 sidebar-bsn">
            <ul class="list-group">
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.dashboard') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none">
                        Ringkasan Dashboard
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.index') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none">
                        Semua Dataset Diupload
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.approved') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none">
                        Dataset yang Disetujui
                    </a>
                </li>
                <li class="list-group-item p-0">
                    <a href="{{ route('admin.datasets.create') }}"
                       class="d-block px-3 py-2 text-white bg-dark-blue text-decoration-none">
                        Tambah Dataset Baru
                    </a>
                </li>
            </ul>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="col-md-10">
            <h2 class="mb-4">Edit Profil</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST" class="card p-4 shadow-sm">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Asli</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Unit Kerja</label>
                    <input type="text" name="workplace" class="form-control" value="{{ old('workplace', $user->workplace) }}" placeholder="Contoh: Direktorat Data dan Informasi BSN">
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" class="form-select">
                        <option value="">- Pilih -</option>
                        <option value="L" {{ old('gender', $user->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $user->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
