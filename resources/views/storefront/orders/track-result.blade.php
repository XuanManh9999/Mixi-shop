@extends('layouts.storefront')

@section('title', 'Kết quả tra cứu đơn hàng #' . $order->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('orders.track') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Tra cứu đơn hàng khác
                </a>
            </div>

            <!-- Order Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shopping-bag me-3 fa-2x"></i>
                                <div>
                                    <h4 class="mb-0">Mã đơn hàng: #{{ $order->id }}</h4>
                                    <small>Đặt ngày {{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-light text-dark fs-6">{{ $order->status_label }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Thông tin đơn hàng</h6>
                            <p><strong>Tổng tiền:</strong> <span class="text-success">{{ $order->formatted_total }}</span></p>
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method_label }}</p>
                            <p><strong>Trạng thái thanh toán:</strong> 
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @elseif($order->payment_status === 'expired')
                                    <span class="badge bg-danger">Hết hạn</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                @else
                                    <span class="badge bg-warning">Chưa thanh toán</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Thông tin giao hàng</h6>
                            <p><strong>Người nhận:</strong> {{ $order->ship_full_name }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $order->ship_phone }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->ship_address }}</p>
                        </div>
                    </div>

                    <!-- Payment Warning for VNPay -->
                    @if($order->canPayVNPay())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Chú ý:</strong> Đơn hàng sẽ tự động hủy sau 15 phút nếu chưa thanh toán.
                            <br>
                            <small>Thời gian còn lại: <span id="countdown"></span></small>
                            <br>
                            <a href="{{ route('payment.vnpay', $order) }}" class="btn btn-success btn-sm mt-2" id="paymentBtn">
                                <i class="fas fa-credit-card me-1"></i>Thanh toán ngay
                            </a>
                        </div>
                    @endif

                    @if($order->payment_method === 'vnpay' && $order->payment_status === 'unpaid' && $order->isVNPayExpired())
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Đã hết hạn thanh toán</strong> - Đơn hàng đã hết thời gian thanh toán (15 phút).
                        </div>
                    @endif

                    @if($order->payment_status === 'expired')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Đơn hàng đã hết hạn thanh toán</strong> - Đơn hàng đã bị hủy do không thanh toán trong thời gian quy định.
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Sản phẩm đã đặt</h5>
                        </div>
                        <div class="card-body">
                            @foreach($order->orderItems as $item)
                            <div class="d-flex align-items-center py-3 @if(!$loop->last) border-bottom @endif">
                                <div class="flex-shrink-0">
                                    @if($item->product && $item->product->thumbnail_url)
                                        <img src="{{ asset($item->product->thumbnail_url) }}" alt="{{ $item->product_name }}" 
                                             class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $item->product_name }}</h6>
                                    <small class="text-muted">SKU: {{ $item->sku }}</small>
                                    <div class="mt-1">
                                        <span class="text-muted">{{ number_format($item->unit_price, 0, ',', '.') }}₫ × {{ $item->quantity }}</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong>{{ number_format($item->total_price, 0, ',', '.') }}₫</strong>
                                </div>
                            </div>
                            @endforeach

                            <!-- Order Summary -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tạm tính:</span>
                                            <span>{{ number_format($order->subtotal_amount, 0, ',', '.') }}₫</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Phí vận chuyển:</span>
                                            <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                                        </div>
                                        @if($order->discount_amount > 0)
                                        <div class="d-flex justify-content-between mb-2 text-success">
                                            <span>Giảm giá:</span>
                                            <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
                                        </div>
                                        @endif
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Tổng cộng:</span>
                                            <span class="text-success">{{ $order->formatted_total }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Order Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Trạng thái đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $controller = app(\App\Http\Controllers\OrderController::class);
                                $timeline = $controller->getOrderTimeline($order);
                            @endphp

                            <div class="timeline">
                                @foreach($timeline as $item)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $item['color'] }}">
                                        <i class="{{ $item['icon'] }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $item['title'] }}</h6>
                                        <p class="mb-1 text-muted">{{ $item['description'] }}</p>
                                        <small class="text-muted">{{ $item['time']->format('d/m/Y H:i:s') }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Contact Support -->
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <h6 class="card-title">Cần hỗ trợ?</h6>
                            <p class="card-text text-muted">Liên hệ với chúng tôi nếu bạn có thắc mắc</p>
                            <div class="row">
                                <div class="col-6">
                                    <i class="fas fa-phone text-primary"></i>
                                    <br>
                                    <small>1900 1234</small>
                                </div>
                                <div class="col-6">
                                    <i class="fas fa-envelope text-primary"></i>
                                    <br>
                                    <small>support@mixishop.com</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    padding-left: 15px;
}
</style>
@endpush

@push('scripts')
<script>
// Countdown timer for VNPay orders
@if($order->canPayVNPay())
(function() {
    const createdAt = new Date('{{ $order->created_at->toISOString() }}');
    const expireAt = new Date(createdAt.getTime() + 15 * 60 * 1000); // 15 minutes
    const paymentBtn = document.getElementById('paymentBtn');
    
    function updateCountdown() {
        const now = new Date();
        const timeLeft = expireAt - now;
        
        if (timeLeft <= 0) {
            document.getElementById('countdown').innerHTML = '<span class="text-danger">Đã hết hạn</span>';
            
            // Ẩn nút thanh toán
            if (paymentBtn) {
                paymentBtn.style.display = 'none';
            }
            
            // Ẩn toàn bộ alert warning và hiển thị alert danger
            const warningAlert = paymentBtn ? paymentBtn.closest('.alert-warning') : null;
            if (warningAlert) {
                warningAlert.style.display = 'none';
                
                // Tạo alert hết hạn
                const expiredAlert = document.createElement('div');
                expiredAlert.className = 'alert alert-danger';
                expiredAlert.innerHTML = `
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Đã hết hạn thanh toán</strong> - Đơn hàng đã hết thời gian thanh toán (15 phút).
                `;
                
                warningAlert.parentNode.insertBefore(expiredAlert, warningAlert);
            }
            
            return;
        }
        
        const minutes = Math.floor(timeLeft / 60000);
        const seconds = Math.floor((timeLeft % 60000) / 1000);
        
        document.getElementById('countdown').innerHTML = 
            `<span class="text-warning">${minutes}:${seconds.toString().padStart(2, '0')}</span>`;
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
})();
@endif
</script>
@endpush
