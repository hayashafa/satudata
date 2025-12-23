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

        {{-- MAIN CONTENT SUPERADMIN --}}
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
                                <h2 class="mb-3" id="stat-total-datasets">{{ $totalDatasets }}</h2>
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
                                <h2 class="mb-3" id="stat-incoming-datasets">{{ $incomingDatasets }}</h2>
                            </div>
                            <a href="{{ route('admin.datasets.index', ['status' => 'pending']) }}" class="btn btn-sm btn-light">
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
                                <h5 class="mb-2">Dataset yang Sudah Disetujui</h5>
                                <h2 class="mb-3" id="stat-approved-datasets">{{ $approvedDatasets }}</h2>
                            </div>
                            <a href="{{ route('admin.datasets.index', ['status' => 'approved']) }}" class="btn btn-sm btn-light">
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
                                <h2 class="mb-3" id="stat-total-users">{{ $totalUsers }}</h2>
                            </div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light">
                                Lihat Tabel
                            </a>
                        </div>
                    </div>
                </div>

                {{-- REKAPAN USER (KARTU RINGKAS) --}}
                @if(isset($topUploaders) && $topUploaders->isNotEmpty())
                <div class="col-md-3 mb-3">
                    <div class="card bg-dark-blue text-white h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="mb-2">Rekapan User</h5>
                                <p class="mb-0 small">Lihat ringkasan upload, approve, tolak, dan edit per admin.</p>
                            </div>
                            <a href="{{ route('admin.dashboard.rekapanUser') }}" class="btn btn-sm btn-light mt-2">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                {{-- KATEGORI --}}
                <div class="col-md-3 mb-3">
                    <div class="card bg-dark-blue text-white h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="mb-2">Kategori</h5>
                                <p class="mb-0 small">Kelola dan atur kategori dataset.</p>
                            </div>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-light mt-2">
                                Lihat Kategori
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            {{-- DATASET TERBARU --}}
            @if(isset($latestDatasets) && $latestDatasets->isNotEmpty())
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Dataset Terbaru</h4>
                            <a href="{{ route('admin.datasets.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive mb-0">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Tahun</th>
                                            <th>Status</th>
                                            <th>Uploader</th>
                                            <th style="width: 120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($latestDatasets as $data)
                                            <tr>
                                                <td>{{ $data->title }}</td>
                                                <td>{{ optional($data->category)->name ?? '-' }}</td>
                                                <td>{{ $data->year ?? '-' }}</td>
                                                <td>
                                                    @if ($data->status === 'approved')
                                                        <span class="badge bg-success">Disetujui</span>
                                                    @elseif ($data->status === 'pending')
                                                        <span class="badge bg-secondary">Menunggu</span>
                                                    @elseif ($data->status === 'rejected')
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @else
                                                        <span class="badge bg-light text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ optional($data->user)->name ?? '-' }}</td>
                                                <td class="text-nowrap">
                                                    @if ($data->status === 'approved')
                                                        <a href="{{ route('datasets.detail', $data->id) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                                    @else
                                                        <a href="{{ route('admin.datasets.show', $data->id) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    try {
        const response = await fetch('{{ route('admin.dashboard.summary') }}', {
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();

        const elTotalDatasets = document.getElementById('stat-total-datasets');
        const elIncoming = document.getElementById('stat-incoming-datasets');
        const elApproved = document.getElementById('stat-approved-datasets');
        const elTotalUsers = document.getElementById('stat-total-users');

        if (elTotalDatasets && typeof data.totalDatasets !== 'undefined') {
            elTotalDatasets.textContent = data.totalDatasets;
        }
        if (elIncoming && typeof data.incomingDatasets !== 'undefined') {
            elIncoming.textContent = data.incomingDatasets;
        }
        if (elApproved && typeof data.approvedDatasets !== 'undefined') {
            elApproved.textContent = data.approvedDatasets;
        }
        if (elTotalUsers && typeof data.totalUsers !== 'undefined') {
            elTotalUsers.textContent = data.totalUsers;
        }
    } catch (e) {
        console.error(e);
    }
});
</script>
@endpush
@endsection
