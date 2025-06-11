@extends('layouts.guest')

@section('title', 'Đăng nhập - Timind')

@section('content')
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-clock text-blue-500 me-2"></i>
                Timind
            </h1>
            <p class="text-gray-600 mt-2">Đăng nhập vào tài khoản của bạn</p>
        </div>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="email"
                    name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input id="password" class="form-control @error('password') is-invalid @enderror" type="password"
                    name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-3 form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label">Ghi nhớ đăng nhập</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    Đăng nhập
                </button>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    Demo: admin@timind.com / password
                </small>
            </div>
        </form>
    </div>
@endsection
