@extends('layouts.storefront')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-4">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <div class="display-6 mb-3">
                    @if($order->payment_status === 'paid')
                        <i class="fas fa-check-circle text-success me-2"></i>Thanh toán thành công!
                    @elseif(session('order_created'))
                        <i class="fas fa-check-circle text-success me-2"></i>Lên đơn hàng thành công!
                    @else
                        <i class="fas fa-clock text-warning me-2"></i>Cảm ơn bạn!
                    @endif
                </div>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-receipt me-3 fa-2x text-primary"></i>
                        <div>
                            <h4 class="mb-0 text-primary">Mã đơn hàng: #{{ $order->id }}</h4>
                            <small class="text-muted">Thông tin chi tiết đơn hàng</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Trạng thái đơn hàng:</strong> 
                                <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                            </p>
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method_label }}</p>
                            <p><strong>Trạng thái thanh toán:</strong> 
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                @else
                                    <span class="badge bg-warning">Chưa thanh toán</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tổng tiền:</strong> <span class="text-success fw-bold">{{ $order->formatted_total }}</span></p>
                            <p><strong>Người nhận:</strong> {{ $order->ship_full_name }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $order->ship_phone }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->ship_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm đã đặt -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sản phẩm đã đặt</h5>
                </div>
                <div class="card-body">
                    @foreach($order->orderItems as $item)
                    <div class="d-flex justify-content-between align-items-center py-2 @if(!$loop->last) border-bottom @endif">
                        <div>
                            <h6 class="mb-1">{{ $item->product_name }}</h6>
                            <small class="text-muted">SKU: {{ $item->sku }}</small>
                        </div>
                        <div class="text-end">
                            <div>{{ number_format($item->unit_price, 0, ',', '.') }}₫ × {{ $item->quantity }}</div>
                            <div class="fw-bold">{{ number_format($item->total_price, 0, ',', '.') }}₫</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Thông tin thanh toán VNPay (nếu có) -->
            @if($order->payment_method === 'vnpay' && $order->latestPayment)
                @php $payment = $order->latestPayment @endphp
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin thanh toán VNPay</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Trạng thái:</strong> 
                                    <span class="badge bg-{{ $payment->status_color }}">{{ $payment->status_label }}</span>
                                </p>
                                @if($payment->vnp_BankCode)
                                    <p><strong>Ngân hàng:</strong> {{ $payment->vnp_BankCode }}</p>
                                @endif
                                @if($payment->vnp_CardType)
                                    <p><strong>Loại thẻ:</strong> {{ $payment->vnp_CardType }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($payment->vnp_TransactionNo)
                                    <p><strong>Mã giao dịch:</strong> {{ $payment->vnp_TransactionNo }}</p>
                                @endif
                                @if($payment->paid_at)
                                    <p><strong>Thời gian thanh toán:</strong> {{ $payment->paid_at->format('d/m/Y H:i:s') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Hành động -->
            <div class="text-center">
                @if($order->payment_status === 'unpaid' && $order->payment_method === 'vnpay')
                    <a class="btn btn-primary me-2" href="{{ route('payment.vnpay', $order) }}">
                        <i class="fas fa-credit-card me-2"></i>Thanh toán lại
                    </a>
                    
                    <!-- Nút mô phỏng thanh toán thành công (chỉ để test) -->
                    <a class="btn btn-success me-2" href="{{ route('simulate.vnpay.success', $order) }}" 
                       onclick="return confirm('Mô phỏng thanh toán thành công? (Chỉ để test)')">
                        <i class="fas fa-check-circle me-2"></i>Mô phỏng thành công
                    </a>
                @endif
                
                <a class="btn btn-dark me-2" href="{{ route('home') }}">
                    <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                </a>
                
                <a class="btn btn-outline-secondary" href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart me-2"></i>Xem giỏ hàng
                </a>
            </div>

            <!-- Thông báo cho COD -->
            @if($order->payment_method === 'cod')
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Đơn hàng đã được tạo thành công!</strong>
                    <br>
                    • Đơn hàng của bạn đang chờ được xác nhận bởi quản trị viên
                    <br>
                    • Chúng tôi sẽ liên hệ với bạn để xác nhận đơn hàng và thời gian giao hàng
                    <br>
                    • Bạn sẽ thanh toán khi nhận được hàng (COD)
                    <br>
                    <small class="text-muted">
                        <strong>Mã đơn hàng:</strong> #{{ $order->id }} - Vui lòng lưu lại để tra cứu
                    </small>
                </div>
            @endif

            <!-- Thông báo cho VNPay -->
            @if($order->payment_method === 'vnpay' && $order->payment_status === 'unpaid')
                <div class="alert alert-warning mt-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Cần thanh toán trong 15 phút!</strong>
                    <br>
                    Đơn hàng VNPay sẽ tự động hủy nếu không thanh toán trong 15 phút.
                </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
(function(){
    // Clear cart khi thanh toán thành công hoặc luôn luôn clear trên trang thank you
    const STORAGE_KEY = 'mixishop_cart_v1';
    
    @if(session('clear_cart') || $order->payment_status === 'paid')
        // Clear cart khi thanh toán thành công
        try { 
            localStorage.removeItem(STORAGE_KEY); 
            console.log('Cart cleared - Payment successful');
        } catch(e) {
            console.error('Error clearing cart:', e);
        }
        
        // Cập nhật badge
        const badge = document.getElementById('cartBadge');
        if (badge) { 
            badge.textContent = '0'; 
            badge.classList.add('d-none'); 
        }
        
        // Hiển thị thông báo
        @if(session('clear_cart'))
            console.log('🎉 Thanh toán thành công! Giỏ hàng đã được xóa.');
        @endif
    @else
        // Chỉ clear cart nếu đã hoàn tất đơn hàng (không phải pending)
        if ('{{ $order->status }}' !== 'pending') {
    try { localStorage.removeItem(STORAGE_KEY); } catch(e) {}
    const badge = document.getElementById('cartBadge');
            if (badge) { badge.textContent = '0'; badge.classList.add('d-none'); }
        }
    @endif
})();
</script>
@endpush
@endsection


