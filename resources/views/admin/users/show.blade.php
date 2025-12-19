@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Pengguna</h2>

    {{-- TABEL INFO USER --}}
    <div class="mb-4 mt-3">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th style="width: 200px;">Nama</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Tanggal Daftar</th>
                    <td>{{ $user->created_at }}</td>
                </tr>
                <tr>
                    <th>Unit Kerja</th>
                    <td>{{ $user->workplace ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- TABEL DATASET YANG DIUPLOAD USER --}}
    <h3>Dataset yang Diupload {{ $user->name }}</h3>

    @if($datasets->isEmpty())
        <div class="alert alert-info mt-2">
            User ini belum mengupload dataset.
        </div>
    @else
        <div class="table-responsive mt-2">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Tahun</th>
                        <th>File</th>
                        <th>Gambar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datasets as $data)
                        <tr>
                            <td>{{ $data->title }}</td>
                            <td>{{ $data->description ?? '-' }}</td>
                            <td>{{ optional($data->category)->name ?? '-' }}</td>
                            <td>{{ $data->year }}</td>
                            <td>
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
                                    <img src="{{ asset('storage/' . $data->image) }}" 
                                         width="80" 
                                         class="rounded border">
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
@endsection
