@extends('layouts.storefront')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <!-- Order Info -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-receipt me-3 fa-2x text-primary"></i>
                                <div>
                                    <h4 class="mb-0 text-primary">Mã đơn hàng: #{{ $order->id }}</h4>
                                    <small class="text-muted">Chi tiết đơn hàng và trạng thái</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex flex-column align-items-end">
                                <span class="badge bg-{{ $order->status_color }} mb-1">{{ $order->status_label }}</span>
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @elseif($order->payment_status === 'expired')
                                    <span class="badge bg-danger">Hết hạn thanh toán</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="badge bg-danger">Thanh toán thất bại</span>
                                @else
                                    <span class="badge bg-warning">Chưa thanh toán</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method_label }}</p>
                            @if($order->canPayVNPay())
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Lưu ý:</strong> Đơn hàng sẽ tự động hủy sau 15 phút nếu chưa thanh toán.
                                    <br>
                                    <small>Thời gian còn lại: <span id="countdown"></span></small>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tổng tiền:</strong> <span class="text-success fw-bold">{{ $order->formatted_total }}</span></p>
                            <p><strong>Trạng thái thanh toán:</strong> 
                                @if($order->payment_status === 'paid')
                                    <span class="text-success">Đã thanh toán</span>
                                @elseif($order->payment_status === 'expired')
                                    <span class="text-danger">Hết hạn</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="text-danger">Thất bại</span>
                                @else
                                    <span class="text-warning">Chưa thanh toán</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-3">
                        @if($order->canPayVNPay())
                            <a href="{{ route('payment.vnpay', $order) }}" class="btn btn-success" id="paymentBtn">
                                <i class="fas fa-credit-card me-2"></i>Thanh toán ngay
                            </a>
                        @elseif($order->payment_method === 'vnpay' && $order->payment_status === 'unpaid' && $order->isVNPayExpired())
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>Đã hết hạn thanh toán</strong> - Đơn hàng đã hết thời gian thanh toán (15 phút).
                            </div>
                        @endif

                        @if(in_array($order->status, ['pending', 'confirmed']) && $order->payment_status !== 'paid')
                            <button type="button" class="btn btn-outline-danger" onclick="cancelOrder({{ $order->id }})">
                                <i class="fas fa-times me-2"></i>Hủy đơn hàng
                            </button>
                        @endif

                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>

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

            <!-- Payment Info -->
            @if($order->payments->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin thanh toán</h5>
                </div>
                <div class="card-body">
                    @foreach($order->payments as $payment)
                    <div class="d-flex justify-content-between align-items-center py-2 @if(!$loop->last) border-bottom @endif">
                        <div>
                            <strong>{{ $payment->provider_label }}</strong>
                            <br>
                            <small class="text-muted">{{ $payment->created_at->format('d/m/Y H:i:s') }}</small>
                            @if($payment->vnp_TransactionNo)
                                <br><small>Mã GD: {{ $payment->vnp_TransactionNo }}</small>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $payment->status_color }}">{{ $payment->status_label }}</span>
                            <br>
                            <strong>{{ $payment->formatted_amount }}</strong>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Shipping Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Người nhận:</strong> {{ $order->ship_full_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->ship_phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->ship_address }}</p>
                    @if($order->ship_city)
                        <p><strong>Thành phố:</strong> {{ $order->ship_city }}</p>
                    @endif
                    @if($order->note)
                        <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
                    @endif
                </div>
            </div>

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
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận hủy đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }} không?</p>
                <p class="text-muted">Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Không</button>
                <form id="cancelOrderForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Có, hủy đơn hàng</button>
                </form>
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
function cancelOrder(orderId) {
    const form = document.getElementById('cancelOrderForm');
    form.action = `/orders/${orderId}/cancel`;
    
    const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    modal.show();
}

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
            
            // Ẩn nút thanh toán và hiển thị thông báo hết hạn
            if (paymentBtn) {
                paymentBtn.style.display = 'none';
            }
            
            // Hiển thị thông báo hết hạn
            const expiredAlert = document.createElement('div');
            expiredAlert.className = 'alert alert-danger mt-3';
            expiredAlert.innerHTML = `
                <i class="fas fa-times-circle me-2"></i>
                <strong>Đã hết hạn thanh toán</strong> - Đơn hàng đã hết thời gian thanh toán (15 phút).
            `;
            
            if (paymentBtn && paymentBtn.parentNode) {
                paymentBtn.parentNode.insertBefore(expiredAlert, paymentBtn);
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
