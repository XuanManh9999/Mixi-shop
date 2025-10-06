@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - MixiShop')

@section('content')
<div class="auth-header">
    <div class="logo">
        <i class="fas fa-lock"></i>
    </div>
    <h2>Đặt lại mật khẩu</h2>
    <p>Nhập mật khẩu mới cho tài khoản của bạn</p>
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

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        
        <input type="hidden" name="token" value="{{ $token }}">
        
        <div class="form-floating">
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" placeholder="name@example.com" 
                   value="{{ old('email', request()->email) }}" required autofocus>
            <label for="email">
                <i class="fas fa-envelope me-2"></i>Email
            </label>
        </div>

        <div class="form-floating">
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" placeholder="Mật khẩu mới" required>
            <label for="password">
                <i class="fas fa-lock me-2"></i>Mật khẩu mới
            </label>
        </div>

        <div class="form-floating">
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                   id="password_confirmation" name="password_confirmation" placeholder="Xác nhận mật khẩu mới" required>
            <label for="password_confirmation">
                <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu mới
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Đặt lại mật khẩu
        </button>
    </form>

    <div class="divider">
        <span>hoặc</span>
    </div>

    <div class="auth-links">
        <p class="mb-0">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left me-1"></i>Quay lại đăng nhập
            </a>
        </p>
    </div>
</div>
@endsection
