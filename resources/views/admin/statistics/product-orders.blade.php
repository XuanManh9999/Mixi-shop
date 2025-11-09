@extends('layouts.admin')

@section('title', 'Lịch Sử Đặt Hàng - ' . $product->name)
@section('page-title', 'Lịch Sử Đặt Hàng')
@section('page-description', 'Chi tiết đơn hàng của sản phẩm: ' . $product->name)

@section('content')
<!-- Product Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    @if($product->main_image)
                        <img src="{{ $product->main_image }}" 
                             class="rounded me-3" 
                             style="width: 80px; height: 80px; object-fit: cover;"
                             alt="{{ $product->name }}">
                    @endif
                    <div>
                        <h4 class="mb-1">{{ $product->name }}</h4>
                        <p class="text-muted mb-0">SKU: {{ $product->sku ?? 'N/A' }}</p>
                        <p class="text-muted mb-0">Giá: {{ number_format($product->price, 0, ',', '.') }}₫</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary">{{ number_format($stats['total_orders']) }}</h3>
                <p class="text-muted mb-0">Tổng đơn hàng</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success">{{ number_format($stats['total_quantity']) }}</h3>
                <p class="text-muted mb-0">Tổng số lượng đã bán</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-info">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</h3>
                <p class="text-muted mb-0">Tổng doanh thu</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.statistics.product-orders', $product->id) }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Áp dụng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh Sách Đơn Hàng</h5>
            </div>
            <div class="card-body">
                @if($orderItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $item)
                                <tr>
                                    <td>
                                        <strong>#{{ $item->order->id }}</strong>
                                    </td>
                                    <td>
                                        @if($item->order->user)
                                            <div>{{ $item->order->user->name }}</div>
                                            <small class="text-muted">{{ $item->order->user->email }}</small>
                                        @else
                                            <span class="text-muted">Khách vãng lai</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item->quantity }}</span>
                                    </td>
                                    <td>{{ number_format($item->unit_price, 0, ',', '.') }}₫</td>
                                    <td class="text-success fw-bold">{{ number_format($item->total_price, 0, ',', '.') }}₫</td>
                                    <td>
                                        <span class="badge bg-{{ $item->order->status_color ?? 'secondary' }}">
                                            {{ $item->order->status_label ?? $item->order->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $item->order->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $orderItems->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Không có đơn hàng nào trong khoảng thời gian này.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="row mt-4">
    <div class="col-12">
        <a href="{{ route('admin.statistics.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay Lại Thống Kê
        </a>
    </div>
</div>
@endsection

