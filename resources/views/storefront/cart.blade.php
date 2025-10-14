@extends('layouts.storefront')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container">
    <h1 class="h4 mb-3">Giỏ hàng của bạn</h1>
    
    @guest
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Lưu ý:</strong> Bạn cần đăng nhập để có thể đặt hàng. 
            <a href="{{ route('login') }}" class="alert-link">Đăng nhập ngay</a> hoặc 
            <a href="{{ route('register') }}" class="alert-link">tạo tài khoản mới</a>.
        </div>
    @endguest
    
    <div id="cartApp" class="card">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th style="width:140px">Số lượng</th>
                        <th>Thành tiền</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="card-body d-flex justify-content-between align-items-center">
            <div class="small text-muted">Phí vận chuyển tính ở bước sau.</div>
            <div class="h5 mb-0">Tổng: <span id="cartTotal">0₫</span></div>
        </div>
        <div class="card-body text-end">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Tiếp tục chọn món</a>
            @auth
                <a href="{{ route('checkout.show') }}" class="btn btn-dark">Lên đơn</a>
            @else
                <div class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" title="Vui lòng đăng nhập để đặt hàng">
                    <a href="{{ route('login') }}" class="btn btn-dark">
                        <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập để đặt hàng
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection


