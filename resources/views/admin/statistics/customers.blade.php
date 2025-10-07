@extends('layouts.admin')

@section('title', 'Thống kê Khách hàng - Admin MixiShop')
@section('page-title', 'Thống kê Khách hàng')
@section('page-description', 'Phân tích chi tiết về khách hàng')

@section('content')
<!-- User Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number">{{ number_format($userStats['total']) }}</div>
            <div class="stats-label">Tổng Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stats-number">{{ number_format($userStats['with_orders']) }}</div>
            <div class="stats-label">Đã Mua Hàng</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stats-number">{{ number_format($userStats['new_this_month']) }}</div>
            <div class="stats-label">Mới Tháng Này</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stats-number">{{ number_format($userStats['admins']) }}</div>
            <div class="stats-label">Admin</div>
        </div>
    </div>
</div>

<!-- Top Customers -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-crown me-2"></i>Top 20 Khách Hàng VIP</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">Hạng</th>
                                <th width="30%">Khách hàng</th>
                                <th width="15%">Số điện thoại</th>
                                <th width="15%">Số đơn</th>
                                <th width="20%">Tổng chi tiêu</th>
                                <th width="15%">TB/đơn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCustomers as $index => $customer)
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
                                    <div>
                                        <div class="fw-semibold">{{ $customer->name }}</div>
                                        <small class="text-muted">{{ $customer->email }}</small>
                                    </div>
                                </td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info fs-6">{{ $customer->order_count }}</span>
                                </td>
                                <td class="text-success fw-bold">{{ number_format($customer->total_spent, 0, ',', '.') }}₫</td>
                                <td class="text-muted">{{ number_format($customer->avg_order_value, 0, ',', '.') }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Customers -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Khách Hàng Mới</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Ngày đăng ký</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($newCustomers as $customer)
                            <tr>
                                <td class="fw-semibold">{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td>
                                    <small class="text-muted">{{ $customer->created_at->format('d/m/Y H:i') }}</small>
                                    <br>
                                    <small class="text-success">{{ $customer->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users') }}?search={{ $customer->email }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="row mt-4">
    <div class="col-12">
        <a href="{{ route('admin.statistics.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay Lại Tổng Quan
        </a>
    </div>
</div>
@endsection

