@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng - Admin MixiShop')
@section('page-title', 'Quản lý Đơn hàng')
@section('page-description', 'Danh sách tất cả đơn hàng trong hệ thống')

@section('content')
<!-- Search & Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search me-2"></i>Tìm Kiếm & Lọc</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="ID, tên, SĐT...">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                                <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Đã giao</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="payment_status" class="form-label">Thanh toán</label>
                            <select class="form-select" id="payment_status" name="payment_status">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Chờ TT</option>
                                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã TT</option>
                                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Thất bại</option>
                                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Hoàn tiền</option>
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
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                <a href="{{ route('admin.orders.export', request()->query()) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stats-number">{{ $stats['total'] }}</div>
            <div class="stats-label">Tổng Đơn</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number">{{ $stats['pending'] }}</div>
            <div class="stats-label">Chờ Xác Nhận</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-check"></i>
            </div>
            <div class="stats-number">{{ $stats['confirmed'] }}</div>
            <div class="stats-label">Đã Xác Nhận</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-truck"></i>
            </div>
            <div class="stats-number">{{ $stats['shipping'] }}</div>
            <div class="stats-label">Đang Giao</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['delivered'] }}</div>
            <div class="stats-label">Hoàn Thành</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card danger">
            <div class="stats-icon text-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['cancelled'] }}</div>
            <div class="stats-label">Đã Hủy</div>
        </div>
    </div>
</div>

<!-- Revenue Stats -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['today_revenue'], 0, ',', '.') }}₫</div>
            <div class="stats-label">Doanh Thu Hôm Nay</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['month_revenue'], 0, ',', '.') }}₫</div>
            <div class="stats-label">Doanh Thu Tháng</div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-bag me-2"></i>Danh Sách Đơn Hàng 
                    <span class="badge bg-primary">{{ $orders->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thanh toán</th>
                                    <th>Ngày đặt</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order->id }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $order->ship_full_name }}</div>
                                            <small class="text-muted">{{ $order->ship_phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $order->total_items }} món
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">{{ $order->formatted_total }}</div>
                                        <small class="text-muted">{{ $order->payment_method_label }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->payment_status === 'paid')
                                            <span class="badge bg-success">Đã TT</span>
                                        @elseif($order->payment_status === 'pending')
                                            <span class="badge bg-warning">Chờ TT</span>
                                        @elseif($order->payment_status === 'failed')
                                            <span class="badge bg-danger">Thất bại</span>
                                        @else
                                            <span class="badge bg-info">{{ $order->payment_status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Hiển thị {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} 
                            trong tổng số {{ $orders->total() }} đơn hàng
                        </div>
                        <div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có đơn hàng nào</h5>
                        <p class="text-muted">Đơn hàng sẽ hiển thị khi khách hàng đặt mua</p>
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
    // Auto-submit search form
    const searchForm = document.getElementById('searchForm');
    const autoSubmitElements = ['status', 'payment_status', 'date_from', 'date_to'];
    
    autoSubmitElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('change', function() {
                searchForm.submit();
            });
        }
    });
    
    // Real-time search
    let searchTimeout;
    const searchInput = document.getElementById('search');
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2 || this.value.length === 0) {
                searchForm.submit();
            }
        }, 500);
    });
    
    // Stats cards click to filter
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('click', function() {
            const label = this.querySelector('.stats-label').textContent.trim();
            let statusFilter = '';
            
            switch(label) {
                case 'Chờ Xác Nhận':
                    statusFilter = 'pending';
                    break;
                case 'Đã Xác Nhận':
                    statusFilter = 'confirmed';
                    break;
                case 'Đang Giao':
                    statusFilter = 'shipping';
                    break;
                case 'Hoàn Thành':
                    statusFilter = 'delivered';
                    break;
                case 'Đã Hủy':
                    statusFilter = 'cancelled';
                    break;
            }
            
            if (statusFilter) {
                const url = new URL(window.location);
                url.searchParams.set('status', statusFilter);
                window.location.href = url.toString();
            }
        });
    });
});
</script>
@endpush
