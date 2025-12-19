@extends('layouts.app')

@section('content')
<div class="container mt-5" style="max-width: 480px;">
    <h3 class="mb-4">Login</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-danger w-100">Login</button>
    </form>
</div>
@endsection 