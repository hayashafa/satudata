@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Pengguna</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" value="{{ $search ?? request('search') }}" class="form-control" placeholder="Cari nama atau email pengguna">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                @php($currentSort = $sort ?? request('sort', 'latest'))
                <option value="latest" {{ $currentSort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="name_az" {{ $currentSort === 'name_az' ? 'selected' : '' }}>Nama A - Z</option>
                <option value="name_za" {{ $currentSort === 'name_za' ? 'selected' : '' }}>Nama Z - A</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Terapkan</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <table class="table table-bordered">
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
@endsection