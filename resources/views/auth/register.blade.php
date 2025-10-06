@extends('layouts.app')

@section('title', 'Đăng ký - MixiShop')

@section('content')
<div class="auth-header">
    <div class="logo">
        <i class="fas fa-hamburger"></i>
    </div>
    <h2>Đăng ký</h2>
    <p>Tạo tài khoản mới tại MixiShop!</p>
</div>

<div class="auth-body">
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="form-floating">
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" placeholder="Họ và tên" 
                   value="{{ old('name') }}" required autofocus>
            <label for="name">
                <i class="fas fa-user me-2"></i>Họ và tên
            </label>
        </div>

        <div class="form-floating">
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" placeholder="name@example.com" 
                   value="{{ old('email') }}" required>
            <label for="email">
                <i class="fas fa-envelope me-2"></i>Email
            </label>
        </div>

        <div class="form-floating">
            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                   id="phone" name="phone" placeholder="Số điện thoại" 
                   value="{{ old('phone') }}">
            <label for="phone">
                <i class="fas fa-phone me-2"></i>Số điện thoại (không bắt buộc)
            </label>
        </div>

        <div class="form-floating">
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" placeholder="Mật khẩu" required>
            <label for="password">
                <i class="fas fa-lock me-2"></i>Mật khẩu
            </label>
        </div>

        <div class="form-floating">
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                   id="password_confirmation" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
            <label for="password_confirmation">
                <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Đăng ký
        </button>
    </form>

    <div class="divider">
        <span>hoặc</span>
    </div>

    <div class="auth-links">
        <p class="mb-0">
            Đã có tài khoản? 
            <a href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập ngay
            </a>
        </p>
    </div>
</div>
@endsection
