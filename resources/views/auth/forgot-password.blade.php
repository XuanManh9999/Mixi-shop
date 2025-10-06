@extends('layouts.app')

@section('title', 'Quên mật khẩu - MixiShop')

@section('content')
<div class="auth-header">
    <div class="logo">
        <i class="fas fa-key"></i>
    </div>
    <h2>Quên mật khẩu</h2>
    <p>Nhập email để nhận link reset mật khẩu</p>
</div>

<div class="auth-body">
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div class="form-floating">
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" placeholder="name@example.com" 
                   value="{{ old('email') }}" required autofocus>
            <label for="email">
                <i class="fas fa-envelope me-2"></i>Email
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-2"></i>Gửi link reset mật khẩu
        </button>
    </form>

    <div class="divider">
        <span>hoặc</span>
    </div>

    <div class="auth-links">
        <p class="mb-2">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left me-1"></i>Quay lại đăng nhập
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
