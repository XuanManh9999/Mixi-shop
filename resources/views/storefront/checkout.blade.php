@extends('layouts.storefront')

@section('title', 'Thanh toán')

@section('content')
<div class="container">
    <h1 class="h4 mb-3">Thông tin thanh toán</h1>
    <div class="row g-4">
        <div class="col-lg-7">
            <form id="checkoutForm" method="POST" action="{{ route('checkout.place') }}">@csrf
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ tên</label>
                                <input name="customer_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input name="customer_phone" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Địa chỉ giao hàng</label>
                                <textarea name="customer_address" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Phương thức thanh toán</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                                    <option value="vnpay">VNPay (tích hợp sau)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vận chuyển</label>
                                <select name="shipping_method" class="form-select" required>
                                    <option value="standard">Tiêu chuẩn</option>
                                    <option value="express">Hỏa tốc</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="items" id="itemsInput">
                <div class="text-end">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Quay lại giỏ hàng</a>
                    <button class="btn btn-dark">Đặt hàng</button>
                </div>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Tóm tắt đơn hàng</div>
                <div class="card-body" id="summaryBody"></div>
                <div class="card-body border-top d-flex justify-content-between">
                    <div>Tổng cộng</div>
                    <div class="fw-semibold" id="summaryTotal">0₫</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    const STORAGE_KEY = 'mixishop_cart_v1';
    function loadCart(){ try{ return JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); }catch(e){ return []; } }

    const items = loadCart();
    const summaryBody = document.getElementById('summaryBody');
    const summaryTotal = document.getElementById('summaryTotal');
    const itemsInput = document.getElementById('itemsInput');

    function render(){
        if(items.length===0){ summaryBody.innerHTML = '<div class="text-muted">Giỏ hàng trống.</div>'; summaryTotal.textContent = '0₫'; return; }
        let total = 0;
        summaryBody.innerHTML = items.map(i=>{
            const line = i.price * i.quantity; total += line;
            return `<div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                    <img src="${i.image}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #eee" class="me-2">
                    <div>
                        <div class="fw-semibold">${i.name}</div>
                        <div class="small text-muted">x${i.quantity}</div>
                    </div>
                </div>
                <div>${line.toLocaleString('vi-VN')}₫</div>
            </div>`;
        }).join('');
        summaryTotal.textContent = total.toLocaleString('vi-VN') + '₫';
        itemsInput.value = JSON.stringify(items.map(i=>({id:i.id, quantity:i.quantity})));
    }
    render();
})();
<\/script>
@endpush

@endsection


