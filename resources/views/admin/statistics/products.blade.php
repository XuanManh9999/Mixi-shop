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

<!-- Charts: Best Sellers & Worst Sellers -->
<div class="row mb-4">
    <!-- Top 10 Sản Phẩm Bán Chạy -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Top 10 Sản Phẩm Bán Chạy</h5>
            </div>
            <div class="card-body">
                <div style="height: 400px; position: relative;">
                    <canvas id="bestSellersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 10 Sản Phẩm Bán Kém -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Top 10 Sản Phẩm Bán Kém</h5>
            </div>
            <div class="card-body">
                <div style="height: 400px; position: relative;">
                    <canvas id="worstSellersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bảng Chi Tiết - Best Sellers -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Chi Tiết Top 20 Sản Phẩm Bán Chạy</h5>
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

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Dữ liệu Top 10 Bán Chạy
    const bestSellersData = @json($bestSellers->take(10)->values());
    
    // Dữ liệu Top 10 Bán Kém (query ngược lại)
    const allProducts = @json($bestSellers);
    const worstSellersData = allProducts.slice().reverse().slice(0, 10);
    
    // Chart Top 10 Bán Chạy
    const ctxBest = document.getElementById('bestSellersChart');
    if (ctxBest) {
        new Chart(ctxBest, {
            type: 'bar',
            data: {
                labels: bestSellersData.map(item => {
                    // Rút gọn tên sản phẩm nếu quá dài
                    const name = item.product_name;
                    return name.length > 20 ? name.substring(0, 20) + '...' : name;
                }),
                datasets: [{
                    label: 'Số lượng đã bán',
                    data: bestSellersData.map(item => item.total_sold),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)',
                        'rgba(255, 99, 255, 0.8)',
                        'rgba(99, 255, 132, 0.8)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)',
                        'rgba(255, 99, 255, 1)',
                        'rgba(99, 255, 132, 1)',
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const item = bestSellersData[context.dataIndex];
                                return [
                                    'Đã bán: ' + item.total_sold + ' sản phẩm',
                                    'Doanh thu: ' + Number(item.total_revenue).toLocaleString('vi-VN') + '₫'
                                ];
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Top 10 Sản Phẩm Bán Chạy Nhất',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Số lượng đã bán'
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }
    
    // Chart Top 10 Bán Kém  
    const ctxWorst = document.getElementById('worstSellersChart');
    if (ctxWorst && worstSellersData.length > 0) {
        new Chart(ctxWorst, {
            type: 'bar',
            data: {
                labels: worstSellersData.map(item => {
                    const name = item.product_name;
                    return name.length > 20 ? name.substring(0, 20) + '...' : name;
                }),
                datasets: [{
                    label: 'Số lượng đã bán',
                    data: worstSellersData.map(item => item.total_sold),
                    backgroundColor: [
                        'rgba(245, 87, 108, 0.8)',
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(201, 203, 207, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                    ],
                    borderColor: [
                        'rgba(245, 87, 108, 1)',
                        'rgba(240, 147, 251, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(201, 203, 207, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const item = worstSellersData[context.dataIndex];
                                return [
                                    'Đã bán: ' + item.total_sold + ' sản phẩm',
                                    'Doanh thu: ' + Number(item.total_revenue).toLocaleString('vi-VN') + '₫'
                                ];
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Top 10 Sản Phẩm Bán Kém Nhất',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Số lượng đã bán'
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    /* Đảm bảo container biểu đồ có chiều cao cố định */
    .card-body > div[style*="height"] {
        height: 400px !important;
        max-height: 400px !important;
        overflow: hidden;
    }
    
    .card-body > div[style*="height"] canvas {
        max-height: 100% !important;
        width: 100% !important;
    }
</style>
@endpush

