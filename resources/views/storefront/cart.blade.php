@extends('layouts.storefront')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container">
    <h1 class="h4 mb-3">Giỏ hàng của bạn</h1>
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
            <a href="{{ route('checkout.show') }}" class="btn btn-dark">Thanh toán</a>
        </div>
    </div>
</div>
@endsection


