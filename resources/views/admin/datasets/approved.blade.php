@extends('layouts.app')

@section('content') 
<div class="container py-3">

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
@endsection
