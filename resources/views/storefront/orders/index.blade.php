@extends('layouts.storefront')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tài khoản của tôi</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                    </a>
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i>Thông tin tài khoản
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Đơn hàng của tôi</h5>
                    <a href="{{ route('orders.track') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Tra cứu đơn hàng
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                                <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="payment_status" class="form-select">
                                <option value="">Tất cả thanh toán</option>
                                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Thất bại</option>
                                <option value="expired" {{ request('payment_status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Lọc
                            </button>
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Xóa bộ lọc
                            </a>
                        </div>
                    </form>

                    @if($orders->count() > 0)
                        <!-- Orders List -->
                        <div class="row">
                            @foreach($orders as $order)
                            <div class="col-12 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary">Mã đơn hàng</strong>
                                                    <h5 class="mb-1 text-dark">#{{ $order->id }}</h5>
                                                    <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex flex-column">
                                                    @foreach($order->orderItems->take(2) as $item)
                                                    <small>{{ $item->product_name }} x{{ $item->quantity }}</small>
                                                    @endforeach
                                                    @if($order->orderItems->count() > 2)
                                                    <small class="text-muted">và {{ $order->orderItems->count() - 2 }} sản phẩm khác</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                                                <br>
                                                @if($order->payment_status === 'paid')
                                                    <span class="badge bg-success mt-1">Đã thanh toán</span>
                                                @elseif($order->payment_status === 'expired')
                                                    <span class="badge bg-danger mt-1">Hết hạn</span>
                                                @elseif($order->payment_status === 'failed')
                                                    <span class="badge bg-danger mt-1">Thất bại</span>
                                                @else
                                                    <span class="badge bg-warning mt-1">Chưa thanh toán</span>
                                                @endif
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <strong class="text-success">{{ $order->formatted_total }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $order->payment_method_label }}</small>
                                            </div>
                                            <div class="col-md-3 text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                                                </a>
                                                
                                                @if(in_array($order->status, ['pending', 'confirmed']) && $order->payment_status !== 'paid')
                                                    <button type="button" class="btn btn-outline-danger btn-sm mt-1" 
                                                            onclick="cancelOrder({{ $order->id }})">
                                                        <i class="fas fa-times me-1"></i>Hủy đơn
                                                    </button>
                                                @endif

                                                @if($order->payment_method === 'vnpay' && $order->payment_status === 'unpaid')
                                                    <a href="{{ route('payment.vnpay', $order) }}" class="btn btn-success btn-sm mt-1">
                                                        <i class="fas fa-credit-card me-1"></i>Thanh toán
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $orders->appends(request()->query())->links('custom.pagination') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5>Chưa có đơn hàng nào</h5>
                            <p class="text-muted">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm!</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i>Bắt đầu mua sắm
                            </a>
                        </div>
                    @endif
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
                <p>Bạn có chắc chắn muốn hủy đơn hàng này không?</p>
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

@push('scripts')
<script>
function cancelOrder(orderId) {
    const form = document.getElementById('cancelOrderForm');
    form.action = `/orders/${orderId}/cancel`;
    
    const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    modal.show();
}
</script>
@endpush
