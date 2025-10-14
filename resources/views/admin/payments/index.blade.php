@extends('layouts.admin')

@section('title', 'Quản lý Thanh toán - Admin MixiShop')
@section('page-title', 'Quản lý Thanh toán')
@section('page-description', 'Danh sách tất cả giao dịch thanh toán')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['total']) }}</div>
            <div class="stats-label">Tổng Giao Dịch</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['paid']) }}</div>
            <div class="stats-label">Đã Thanh Toán</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['pending']) }}</div>
            <div class="stats-label">Chờ Xử Lý</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card danger">
            <div class="stats-icon text-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['failed']) }}</div>
            <div class="stats-label">Thất Bại</div>
        </div>
    </div>
</div>

<!-- Order Status Stats -->
<div class="row mb-4">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['orders_pending']) }}</div>
            <div class="stats-label">Đơn Hàng Chờ Xác Nhận</div>
            <div class="mt-2">
                <a href="{{ route('admin.payments.index', ['order_status' => 'pending']) }}" 
                   class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['orders_confirmed']) }}</div>
            <div class="stats-label">Đơn Hàng Đã Xác Nhận</div>
            <div class="mt-2">
                <a href="{{ route('admin.payments.index', ['order_status' => 'confirmed']) }}" 
                   class="btn btn-sm btn-outline-info">
                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Stats -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-success">
                    <i class="fas fa-coins me-2"></i>Tổng Doanh Thu
                </h5>
                <h2 class="text-success mb-0">{{ number_format($stats['total_amount'], 0, ',', '.') }}₫</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-info">
                    <i class="fas fa-calendar-day me-2"></i>Doanh Thu Hôm Nay
                </h5>
                <h2 class="text-info mb-0">{{ number_format($stats['today_amount'], 0, ',', '.') }}₫</h2>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search me-2"></i>Tìm Kiếm & Lọc</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.payments.index') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Order ID, Mã GD...">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="provider" class="form-label">Phương thức</label>
                            <select class="form-select" id="provider" name="provider">
                                <option value="">Tất cả</option>
                                <option value="vnpay" {{ request('provider') === 'vnpay' ? 'selected' : '' }}>VNPay</option>
                                <option value="momo" {{ request('provider') === 'momo' ? 'selected' : '' }}>MoMo</option>
                                <option value="cash" {{ request('provider') === 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                <option value="bank_transfer" {{ request('provider') === 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">TT Thanh toán</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Thất bại</option>
                                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="order_status" class="form-label">TT Đơn hàng</label>
                            <select class="form-select" id="order_status" name="order_status">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('order_status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ request('order_status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="preparing" {{ request('order_status') === 'preparing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                                <option value="shipping" {{ request('order_status') === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                <option value="delivered" {{ request('order_status') === 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                <option value="cancelled" {{ request('order_status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if(request()->hasAny(['search', 'provider', 'status', 'date_from', 'date_to']))
                                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times me-1"></i>Xóa bộ lọc
                                        </a>
                                        <span class="text-muted ms-2">
                                            Tìm thấy {{ $payments->total() }} giao dịch
                                        </span>
                                    @endif
                                </div>
                                
                                <div>
                                    <a href="{{ route('admin.payments.export', request()->all()) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-download me-1"></i>Export CSV
                                    </a>
                                    <a href="{{ route('admin.payments.statistics') }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-chart-bar me-1"></i>Thống kê
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Danh Sách Giao Dịch 
                    <span class="badge bg-primary">{{ $payments->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Order</th>
                                    <th>Khách hàng</th>
                                    <th>Phương thức</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Mã GD</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td><strong>#{{ $payment->id }}</strong></td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-decoration-none">
                                            #{{ $payment->order_id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>{{ $payment->order->ship_full_name ?? ($payment->order->user->name ?? 'N/A') }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>{{ $payment->order->ship_phone ?? 'Chưa có SĐT' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $payment->provider_label }}</span>
                                    </td>
                                    <td class="fw-bold text-success">{{ $payment->formatted_amount }}</td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-{{ $payment->status_color }}">
                                                {{ $payment->status_label }}
                                            </span>
                                            <span class="badge bg-{{ $payment->order->status_color }} badge-sm">
                                                {{ $payment->order->status_label }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $payment->vnp_TransactionNo ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $payment->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.payments.show', $payment) }}" 
                                               class="btn btn-outline-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($payment->order->status === 'pending')
                                                <form method="POST" action="{{ route('admin.payments.confirm-order', $payment) }}" 
                                                      class="d-inline" onsubmit="return confirm('Xác nhận đơn hàng này?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-primary" title="Xác nhận đơn hàng">
                                                        <i class="fas fa-clipboard-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($payment->status === 'pending' && $payment->order->status === 'confirmed')
                                                <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" 
                                                      class="d-inline" onsubmit="return confirm('Xác nhận thanh toán thành công?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Xác nhận thanh toán">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Hiển thị {{ $payments->firstItem() ?? 0 }} - {{ $payments->lastItem() ?? 0 }} 
                            trong tổng số {{ $payments->total() }} giao dịch
                        </div>
                        <div>
                            {{ $payments->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-money-bill-wave fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có giao dịch nào</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit on filter change
    const autoSubmitElements = ['provider', 'status', 'date_from', 'date_to'];
    
    autoSubmitElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('change', function() {
                document.getElementById('searchForm').submit();
            });
        }
    });
});
</script>
@endpush

