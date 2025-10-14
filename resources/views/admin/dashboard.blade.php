@extends('layouts.admin')

@section('title', 'Admin Dashboard - MixiShop')
@section('page-title', 'Dashboard')
@section('page-description', 'Tổng quan hệ thống MixiShop')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number">{{ $totalUsers }}</div>
            <div class="stats-label">Tổng số Users</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stats-number">{{ $totalAdmins }}</div>
            <div class="stats-label">Admins</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stats-number">{{ $ordersToday }}</div>
            <div class="stats-label">Đơn hàng hôm nay</div>
            <div class="stats-change">
                @if($ordersChange > 0)
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+{{ number_format($ordersChange, 1) }}%</span>
                @elseif($ordersChange < 0)
                    <i class="fas fa-arrow-down text-danger"></i>
                    <span class="text-danger">{{ number_format($ordersChange, 1) }}%</span>
                @else
                    <i class="fas fa-minus text-muted"></i>
                    <span class="text-muted">0%</span>
                @endif
                <small class="text-muted ms-1">so với hôm qua</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stats-number">{{ number_format($revenueToday, 0, ',', '.') }}₫</div>
            <div class="stats-label">Doanh thu hôm nay</div>
            <div class="stats-change">
                @if($revenueChange > 0)
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+{{ number_format($revenueChange, 1) }}%</span>
                @elseif($revenueChange < 0)
                    <i class="fas fa-arrow-down text-danger"></i>
                    <span class="text-danger">{{ number_format($revenueChange, 1) }}%</span>
                @else
                    <i class="fas fa-minus text-muted"></i>
                    <span class="text-muted">0%</span>
                @endif
                <small class="text-muted ms-1">so với hôm qua</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Users -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Users Mới Nhất</h5>
                <a href="{{ route('admin.users') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-eye me-1"></i>Xem tất cả
                </a>
            </div>
            <div class="card-body">
                @if($recentUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>Quyền</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-danger">Admin</span>
                                        @else
                                            <span class="badge bg-primary">User</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có user nào</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Thao Tác Nhanh</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Tạo User Mới
                    </a>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>Quản Lý Users
                    </a>
                   
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông Tin Hệ Thống</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h6 class="text-primary">Laravel</h6>
                            <small class="text-muted">{{ app()->version() }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-success">PHP</h6>
                        <small class="text-muted">{{ phpversion() }}</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Server Time: {{ now()->format('d/m/Y H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row (Placeholder for future)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Biểu Đồ Thống Kê</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Biểu đồ thống kê sẽ được thêm vào sau</h5>
                    <p class="text-muted">Doanh thu, đơn hàng, khách hàng theo thời gian</p>
                </div>
            </div>
        </div>
    </div>
</div> -->
@endsection

@push('styles')
<style>
    .avatar-sm {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
    
    .stats-card .stats-number {
        color: #1e293b;
    }
    
    .card-header .btn-light {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .card-header .btn-light:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
</style>
@endpush
