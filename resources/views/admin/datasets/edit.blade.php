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
            <h2 class="mb-4">Edit Dataset</h2>

            <form action="{{ route('admin.datasets.update', $dataset->id) }}" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $dataset->title) }}"
                        required
                    >
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea
                        name="description"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="3"
                    >{{ old('description', $dataset->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar (opsional)</label>
                    <input
                        type="file"
                        name="image"
                        class="form-control @error('image') is-invalid @enderror"
                    >
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    @if ($dataset->image)
                        <p class="mt-2">Gambar sebelumnya:</p>
                        <img src="{{ asset('storage/'.$dataset->image) }}" width="150">
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select
                        name="category_id"
                        class="form-control @error('category_id') is-invalid @enderror"
                    >
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ old('category_id', $dataset->category_id) == $category->id ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tahun</label>
                    <input
                        type="number"
                        name="year"
                        class="form-control @error('year') is-invalid @enderror"
                        value="{{ old('year', $dataset->year) }}"
                        required
                    >
                    @error('year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">File Dataset (opsional)</label>
                    <input
                        type="file"
                        name="file"
                        class="form-control @error('file') is-invalid @enderror"
                    >
                    @error('file')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    @if($dataset->file_path)
                        <p class="mt-2">File sebelumnya:
                            <a href="{{ route('datasets.viewFile', $dataset->id) }}" target="_blank">Lihat</a>
                        </p>
                    @endif
                </div>

                <div class="d-flex justify-content-between mt-2">
                    <a href="{{ route('admin.datasets.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
