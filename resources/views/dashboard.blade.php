@extends('layouts.storefront')

@section('title', 'Bảng điều khiển')

@section('content')
        <div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <div class="me-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-tachometer-alt fa-2x text-white"></i>
                    </div>
                </div>
                <div>
                    <h1 class="h3 mb-1">Bảng điều khiển</h1>
                    <p class="text-muted mb-0">Chào mừng trở lại, <strong>{{ $user->name }}</strong>!</p>
                </div>
            </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Tổng đơn hàng -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                    </div>
                    <h3 class="h2 mb-1">{{ $orderCount }}</h3>
                    <p class="text-muted mb-0">Tổng đơn hàng</p>
                </div>
            </div>
        </div>

        <!-- Đơn hàng chờ xử lý -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x text-warning"></i>
                    </div>
                    <h3 class="h2 mb-1">{{ $pendingOrders }}</h3>
                    <p class="text-muted mb-0">Đang chờ xử lý</p>
                </div>
                </div>
            </div>
            
        <!-- Đơn hàng hoàn thành -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h3 class="h2 mb-1">{{ $completedOrders }}</h3>
                    <p class="text-muted mb-0">Đã hoàn thành</p>
                </div>
                </div>
            </div>
            
        <!-- Tổng chi tiêu -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-money-bill-wave fa-3x text-info"></i>
                    </div>
                    <h3 class="h2 mb-1">{{ number_format($totalSpent, 0, ',', '.') }}₫</h3>
                    <p class="text-muted mb-0">Tổng chi tiêu</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Thông tin tài khoản -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Thông tin tài khoản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <strong>Họ tên:</strong><br>
                            <span class="text-muted">{{ $user->name }}</span>
                        </div>
                        <div class="col-12">
                            <strong>Email:</strong><br>
                            <span class="text-muted">{{ $user->email }}</span>
                        </div>
                        @if($user->phone)
                        <div class="col-12">
                            <strong>Số điện thoại:</strong><br>
                            <span class="text-muted">{{ $user->phone }}</span>
                        </div>
                        @endif
                        @if($user->address)
                        <div class="col-12">
                            <strong>Địa chỉ:</strong><br>
                            <span class="text-muted">{{ $user->address }}</span>
                        </div>
                        @endif
                        <div class="col-12">
                            <strong>Ngày tham gia:</strong><br>
                            <span class="text-muted">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            
                    <div class="text-end mt-3">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>Chỉnh sửa thông tin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hành động nhanh -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Hành động nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-utensils me-2"></i>Xem thực đơn
                        </a>
                        
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-bag me-2"></i>Quản lý đơn hàng
                        </a>
                        
                        <a href="{{ route('orders.track') }}" class="btn btn-outline-info">
                            <i class="fas fa-search me-2"></i>Tra cứu đơn hàng
                        </a>
                        
                        <a href="{{ url('/cart') }}" class="btn btn-outline-dark">
                            <i class="fas fa-shopping-cart me-2"></i>Xem giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($orderCount > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Đơn hàng gần đây
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng tiền</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders()->latest()->limit(5)->get() as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color ?? 'secondary' }}">
                                            {{ $order->status_label ?? $order->status }}
                                        </span>
                                    </td>
                                    <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong></td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Xem
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i>Xem tất cả đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto-hide success alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush