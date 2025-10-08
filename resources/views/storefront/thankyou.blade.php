@extends('layouts.storefront')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container text-center py-5">
    <div class="display-6 mb-3">Cảm ơn bạn!</div>
    <p class="lead">Đơn hàng #{{ $order->id }} đã được tạo ở trạng thái chờ xử lý.</p>
    <p class="text-muted">Chúng tôi sẽ liên hệ và giao hàng sớm nhất.</p>
    <a class="btn btn-dark" href="{{ route('home') }}">Tiếp tục mua sắm</a>
    <a class="btn btn-outline-secondary ms-2" href="{{ route('cart.index') }}">Xem giỏ hàng</a>
</div>
@endsection


