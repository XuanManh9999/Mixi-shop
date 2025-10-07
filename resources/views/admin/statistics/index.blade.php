@extends('layouts.admin')

@section('title', 'Thống kê Tổng Quan - Admin MixiShop')
@section('page-title', 'Thống kê Tổng Quan')
@section('page-description', 'Báo cáo và phân tích dữ liệu hệ thống')

@section('content')
<!-- Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.statistics.index') }}" class="row g-3">
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

<!-- Overview Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number">{{ number_format($overview['total_users']) }}</div>
            <div class="stats-label">Tổng Khách Hàng</div>
            <small class="text-success">+{{ $overview['new_users_today'] }} hôm nay</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="stats-number">{{ number_format($overview['total_products']) }}</div>
            <div class="stats-label">Tổng Sản Phẩm</div>
            <small class="text-muted">{{ $overview['active_products'] }} đang bán</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stats-number">{{ number_format($overview['total_orders']) }}</div>
            <div class="stats-label">Tổng Đơn Hàng</div>
            <small class="text-success">+{{ $overview['orders_today'] }} hôm nay</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stats-number">{{ number_format($overview['total_revenue'], 0, ',', '.') }}₫</div>
            <div class="stats-label">Tổng Doanh Thu</div>
            <small class="text-success">{{ number_format($overview['revenue_today'], 0, ',', '.') }}₫ hôm nay</small>
        </div>
    </div>
</div>

<!-- Comparison Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Doanh Thu Tháng Này</h6>
                <h2 class="text-success mb-2">{{ number_format($currentMonth['revenue'], 0, ',', '.') }}₫</h2>
                <div class="d-flex align-items-center">
                    @if($comparison['revenue_change'] >= 0)
                        <i class="fas fa-arrow-up text-success me-2"></i>
                        <span class="text-success fw-bold">+{{ number_format($comparison['revenue_change'], 1) }}%</span>
                    @else
                        <i class="fas fa-arrow-down text-danger me-2"></i>
                        <span class="text-danger fw-bold">{{ number_format($comparison['revenue_change'], 1) }}%</span>
                    @endif
                    <span class="text-muted ms-2">so với tháng trước</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Đơn Hàng Tháng Này</h6>
                <h2 class="text-primary mb-2">{{ number_format($currentMonth['orders']) }}</h2>
                <div class="d-flex align-items-center">
                    @if($comparison['orders_change'] >= 0)
                        <i class="fas fa-arrow-up text-success me-2"></i>
                        <span class="text-success fw-bold">+{{ number_format($comparison['orders_change'], 1) }}%</span>
                    @else
                        <i class="fas fa-arrow-down text-danger me-2"></i>
                        <span class="text-danger fw-bold">{{ number_format($comparison['orders_change'], 1) }}%</span>
                    @endif
                    <span class="text-muted ms-2">so với tháng trước</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Doanh Thu Theo Ngày</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Đơn Hàng Theo Trạng Thái</h5>
            </div>
            <div class="card-body">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Phương Thức Thanh Toán</h5>
            </div>
            <div class="card-body">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Đơn Hàng Theo Giờ (Hôm Nay)</h5>
            </div>
            <div class="card-body">
                <canvas id="hourlyOrdersChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Top 10 Sản Phẩm Bán Chạy</h5>
                <a href="{{ route('admin.statistics.products') }}" class="btn btn-sm btn-light">
                    Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Sản phẩm</th>
                                <th>Đã bán</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td><span class="badge bg-primary">{{ $item->total_sold }}</span></td>
                                <td class="text-success fw-bold">{{ number_format($item->total_revenue, 0, ',', '.') }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-crown me-2"></i>Top 10 Khách Hàng VIP</h5>
                <a href="{{ route('admin.statistics.customers') }}" class="btn btn-sm btn-light">
                    Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Khách hàng</th>
                                <th>Đơn hàng</th>
                                <th>Tổng chi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCustomers as $index => $customer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div>{{ $customer->name }}</div>
                                    <small class="text-muted">{{ $customer->email }}</small>
                                </td>
                                <td><span class="badge bg-info">{{ $customer->order_count }}</span></td>
                                <td class="text-success fw-bold">{{ number_format($customer->total_spent, 0, ',', '.') }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Categories -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Doanh Thu Theo Danh Mục</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryRevenueChart" height="60"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($revenueByDate);
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(d => d.date),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: revenueData.map(d => d.total),
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + '₫';
                        }
                    }
                }
            }
        }
    });

    // 2. Order Status Pie Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusData = @json($ordersByStatus);
    
    new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(orderStatusData),
            datasets: [{
                data: Object.values(orderStatusData),
                backgroundColor: [
                    '#ffc107',
                    '#17a2b8',
                    '#667eea',
                    '#28a745',
                    '#20c997',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // 3. Payment Methods Pie Chart
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    const paymentMethodData = @json($paymentMethods);
    
    new Chart(paymentMethodCtx, {
        type: 'pie',
        data: {
            labels: paymentMethodData.map(p => {
                const labels = {
                    'vnpay': 'VNPay',
                    'momo': 'MoMo',
                    'cash': 'Tiền mặt',
                    'bank_transfer': 'Chuyển khoản'
                };
                return labels[p.provider] || p.provider;
            }),
            datasets: [{
                data: paymentMethodData.map(p => p.total),
                backgroundColor: [
                    '#667eea',
                    '#e91e63',
                    '#28a745',
                    '#17a2b8'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(context.parsed);
                            return label + ': ' + value;
                        }
                    }
                }
            }
        }
    });

    // 4. Hourly Orders Bar Chart
    const hourlyCtx = document.getElementById('hourlyOrdersChart').getContext('2d');
    const hourlyData = @json($hourlyOrders);
    
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(hourlyData).map(h => h + ':00'),
            datasets: [{
                label: 'Số đơn hàng',
                data: Object.values(hourlyData),
                backgroundColor: '#ffa500',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // 5. Category Revenue Bar Chart
    const categoryCtx = document.getElementById('categoryRevenueChart').getContext('2d');
    const categoryData = @json($topCategories);
    
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: categoryData.map(c => c.category_name),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: categoryData.map(c => c.total_revenue),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(context.parsed.x);
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + '₫';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    .stats-card small {
        display: block;
        margin-top: 8px;
        font-size: 0.85rem;
    }
</style>
@endpush

