@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Rekapan User</h2>
    <p class="text-muted small mb-3">
        Ringkasan aktivitas semua admin/superadmin: berapa banyak dataset yang diupload, disetujui, ditolak/dihapus, pending,
        dan berapa dataset yang pernah diedit.
    </p>

    @if($topUploaders->isNotEmpty())
        @php
            $topUploader = $topUploaders->first();
        @endphp
        <div class="alert alert-success mb-3" role="alert">
            <span class="fw-semibold">Informasi:</span>
            Terdapat Top Uploader yaitu <strong>{{ $topUploader->name }}</strong> dengan total upload
            <strong>{{ $topUploader->datasets_count }}</strong> dataset.
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Total Upload</th>
                            <th>Disetujui</th>
                            <th>Ditolak/Dihapus*</th>
                            <th>Pending</th>
                            <th>Dataset Pernah Diedit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topUploaders as $i => $user)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->datasets_count }}</td>
                                <td>{{ $user->approved_datasets_count }}</td>
                                <td>{{ $user->rejected_datasets_count }}</td>
                                <td>{{ $user->pending_datasets_count }}</td>
                                <td>{{ $user->edited_datasets_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
