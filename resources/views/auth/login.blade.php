@extends('layouts.app')

@section('title', 'Đăng nhập - MixiShop')

@section('content')
<div class="auth-header">
    <div class="logo">
        <i class="fas fa-hamburger"></i>
    </div>
    <h2>Đăng nhập</h2>
    <p>Chào mừng trở lại với MixiShop!</p>
</div>

<div class="auth-body">
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-floating">
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" placeholder="name@example.com" 
                   value="{{ old('email') }}" required autofocus>
            <label for="email">
                <i class="fas fa-envelope me-2"></i>Email
            </label>
        </div>

        <div class="form-floating">
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" placeholder="Mật khẩu" required>
            <label for="password">
                <i class="fas fa-lock me-2"></i>Mật khẩu
            </label>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Ghi nhớ đăng nhập
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
        </button>
    </form>

    <div class="divider">
        <span>hoặc</span>
    </div>

    <div class="auth-links">
        <p class="mb-2">
            <a href="{{ route('password.request') }}">
                <i class="fas fa-key me-1"></i>Quên mật khẩu?
            </a>
        </p>
        <p class="mb-0">
            Chưa có tài khoản? 
            <a href="{{ route('register') }}">
                <i class="fas fa-user-plus me-1"></i>Đăng ký ngay
            </a>
        </p>
    </div>
</div>
@endsection
