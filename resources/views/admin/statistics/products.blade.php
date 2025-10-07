@extends('layouts.admin')

@section('title', 'Thống kê Sản phẩm - Admin MixiShop')
@section('page-title', 'Thống kê Sản phẩm')
@section('page-description', 'Phân tích chi tiết về sản phẩm')

@section('content')
<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stats-number">{{ $bestSellers->count() }}</div>
            <div class="stats-label">Sản Phẩm Bán Chạy</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stats-number">{{ $lowStock->count() }}</div>
            <div class="stats-label">Tồn Kho Thấp</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card danger">
            <div class="stats-icon text-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number">{{ $outOfStock->count() }}</div>
            <div class="stats-label">Hết Hàng</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="stats-number">{{ $newProducts->count() }}</div>
            <div class="stats-label">Sản Phẩm Mới</div>
        </div>
    </div>
</div>

<!-- Best Sellers -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Top 20 Sản Phẩm Bán Chạy Nhất</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">Hạng</th>
                                <th width="40%">Sản phẩm</th>
                                <th width="15%">Đã bán</th>
                                <th width="20%">Doanh thu</th>
                                <th width="20%">Trung bình/đơn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bestSellers as $index => $item)
                            <tr>
                                <td>
                                    @if($index < 3)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-trophy"></i> {{ $index + 1 }}
                                        </span>
                                    @else
                                        <strong>{{ $index + 1 }}</strong>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product)
                                            <img src="{{ $item->product->main_image }}" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $item->product_name }}</div>
                                            @if($item->product)
                                                <small class="text-muted">{{ $item->product->sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6">{{ number_format($item->total_sold) }}</span>
                                </td>
                                <td class="text-success fw-bold">{{ number_format($item->total_revenue, 0, ',', '.') }}₫</td>
                                <td class="text-muted">{{ number_format($item->total_revenue / $item->total_sold, 0, ',', '.') }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock & Out of Stock -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Sản Phẩm Tồn Kho Thấp (≤10)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Tồn kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStock as $product)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product) }}">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $product->stock_qty }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Không có sản phẩm nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-times-circle me-2"></i>Sản Phẩm Hết Hàng</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outOfStock as $product)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product) }}">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $product->updated_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Không có sản phẩm nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="row">
    <div class="col-12">
        <a href="{{ route('admin.statistics.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay Lại Tổng Quan
        </a>
    </div>
</div>
@endsection

