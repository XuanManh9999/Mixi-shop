@extends('layouts.storefront')

@section('title', 'Tra cứu đơn hàng')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-search me-2"></i>Tra cứu đơn hàng
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center mb-4">
                        Nhập mã đơn hàng và số điện thoại để tra cứu trạng thái đơn hàng của bạn
                    </p>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('orders.track') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="order_id" class="form-label">Mã đơn hàng</label>
                            <input type="number" class="form-control @error('order_id') is-invalid @enderror" 
                                   id="order_id" name="order_id" value="{{ old('order_id') }}" 
                                   placeholder="Nhập mã đơn hàng (ví dụ: 123)" required>
                            @error('order_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                   placeholder="Nhập số điện thoại đặt hàng" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Tra cứu đơn hàng
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-2">Đã có tài khoản?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để xem tất cả đơn hàng
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-question-circle me-2"></i>Cần hỗ trợ?
                    </h6>
                    <p class="card-text text-muted">
                        Nếu bạn không tìm thấy đơn hàng hoặc gặp vấn đề, vui lòng liên hệ với chúng tôi:
                    </p>
                    <div class="row text-center">
                        <div class="col-md-6">
                            <i class="fas fa-phone text-primary"></i>
                            <br>
                            <strong>Hotline</strong>
                            <br>
                            <small>1900 1234</small>
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-envelope text-primary"></i>
                            <br>
                            <strong>Email</strong>
                            <br>
                            <small>support@mixishop.com</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
