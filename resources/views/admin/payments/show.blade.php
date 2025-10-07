@extends('layouts.admin')

@section('title', 'Chi tiết Thanh toán #' . $payment->id . ' - Admin MixiShop')
@section('page-title', 'Chi tiết Thanh toán #' . $payment->id)

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Payment Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông Tin Giao Dịch</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Mã Giao Dịch</label>
                        <div class="fw-bold">#{{ $payment->id }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Đơn Hàng</label>
                        <div>
                            <a href="{{ route('admin.orders.show', $payment->order) }}" class="fw-bold">
                                #{{ $payment->order_id }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Khách Hàng</label>
                        <div class="fw-bold">{{ $payment->order->user->name ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $payment->order->user->email ?? '' }}</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Phương Thức</label>
                        <div>
                            <span class="badge bg-info">{{ $payment->provider_label }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Số Tiền</label>
                        <div class="fw-bold text-success fs-4">{{ $payment->formatted_amount }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Trạng Thái</label>
                        <div>
                            <span class="badge bg-{{ $payment->status_color }} fs-6">
                                {{ $payment->status_label }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Ngày Tạo</label>
                        <div>{{ $payment->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Ngày Thanh Toán</label>
                        <div>{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i:s') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VNPay Details -->
        @if($payment->provider === 'vnpay' && $payment->vnp_TransactionNo)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Thông Tin VNPay</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Mã Giao Dịch VNPay</label>
                        <div class="fw-bold">{{ $payment->vnp_TransactionNo }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Ngân Hàng</label>
                        <div>{{ $payment->vnp_BankCode ?? '-' }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Loại Thẻ</label>
                        <div>{{ $payment->vnp_CardType ?? '-' }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Response Code</label>
                        <div>
                            <span class="badge {{ $payment->vnp_ResponseCode === '00' ? 'bg-success' : 'bg-danger' }}">
                                {{ $payment->vnp_ResponseCode ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Raw Callback Data -->
        @if($callbackData)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-code me-2"></i>Dữ Liệu Callback</h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded"><code>{{ json_encode($callbackData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Thao Tác</h5>
            </div>
            <div class="card-body">
                @if($payment->status === 'pending')
                    <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" 
                          onsubmit="return confirm('Xác nhận thanh toán thành công?')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check me-2"></i>Xác Nhận Thanh Toán
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.payments.mark-failed', $payment) }}" 
                          onsubmit="return confirm('Đánh dấu thanh toán thất bại?')">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>Đánh Dấu Thất Bại
                        </button>
                    </form>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Không thể thay đổi trạng thái thanh toán đã hoàn tất.
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Tóm Tắt Đơn Hàng</h5>
            </div>
            <div class="card-body">
                <div class="mb-2 d-flex justify-content-between">
                    <span>Tạm tính:</span>
                    <strong>{{ number_format($payment->order->subtotal_amount, 0, ',', '.') }}₫</strong>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span>Giảm giá:</span>
                    <strong class="text-danger">-{{ number_format($payment->order->discount_amount, 0, ',', '.') }}₫</strong>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span>Phí ship:</span>
                    <strong>{{ number_format($payment->order->shipping_fee, 0, ',', '.') }}₫</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Tổng cộng:</strong>
                    <strong class="text-success fs-5">{{ number_format($payment->order->total_amount, 0, ',', '.') }}₫</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay Lại
        </a>
    </div>
</div>
@endsection

