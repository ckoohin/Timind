@extends('layouts.guest')

@section('title', 'Đăng ký - Timind')

@section('content')
<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="text-center mb-4">
        <h1 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-clock text-blue-500 me-2"></i>
            Timind
        </h1>
        <p class="text-gray-600 mt-2">Đăng ký tài khoản</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Họ tên</label>
            <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" autofocus>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" autofocus>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="passwordConfirmation" class="form-label">Nhập lại mật khẩu</label>
            <input id="passwordConfirmation" class="form-control @error('password') is-invalid @enderror" type="password" name="password_confirmation">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                Đăng ký
            </button>
        </div>
        <div class="text-center mt-3">
            <a href="/login" class="text-primary" style="text-decoration: none;">Bạn đã có tài khoản? Đăng nhập</a>
        </div>
    </form>
</div>
@endsection