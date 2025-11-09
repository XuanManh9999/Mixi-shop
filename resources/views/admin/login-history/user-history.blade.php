@extends('layouts.admin')

@section('title', 'Lịch sử đăng nhập - ' . $user->name . ' - Admin MixiShop')
@section('page-title', 'Lịch sử đăng nhập: ' . $user->name)
@section('page-description', 'Chi tiết lịch sử đăng nhập của user')

@section('content')
<!-- User Info Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg bg-{{ $user->is_admin ? 'danger' : 'primary' }} rounded-circle d-flex align-items-center justify-content-center me-4">
                        <i class="fas fa-user text-white" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        @if($user->is_admin)
                            <span class="badge bg-danger">
                                <i class="fas fa-shield-alt me-1"></i>Admin
                            </span>
                        @else
                            <span class="badge bg-primary">
                                <i class="fas fa-user me-1"></i>User
                            </span>
                        @endif
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.login-history.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-history"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['total_logins']) }}</div>
            <div class="stats-label">Tổng lần đăng nhập</div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number">
                @if($stats['last_login'])
                    {{ $stats['last_login']->login_at->format('d/m/Y') }}
                @else
                    -
                @endif
            </div>
            <div class="stats-label">Lần đăng nhập gần nhất</div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stats-card {{ $stats['active_session'] ? 'danger' : 'secondary' }}">
            <div class="stats-icon text-{{ $stats['active_session'] ? 'danger' : 'secondary' }}">
                <i class="fas fa-{{ $stats['active_session'] ? 'circle' : 'times-circle' }}"></i>
            </div>
            <div class="stats-number">
                @if($stats['active_session'])
                    <span class="badge bg-success">Đang hoạt động</span>
                @else
                    <span class="badge bg-secondary">Không có</span>
                @endif
            </div>
            <div class="stats-label">Phiên hiện tại</div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.login-history.user', $user) }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- History Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Lịch Sử Đăng Nhập
                    <span class="badge bg-primary">{{ $loginHistories->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($loginHistories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Thời gian đăng nhập</th>
                                    <th>Thời gian đăng xuất</th>
                                    <th>Thời lượng</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loginHistories as $history)
                                <tr>
                                    <td><strong>#{{ $history->id }}</strong></td>
                                    <td>
                                        <code class="text-primary">{{ $history->ip_address }}</code>
                                    </td>
                                    <td>
                                        <small class="text-muted" title="{{ $history->user_agent }}">
                                            {{ \Illuminate\Support\Str::limit($history->user_agent, 60) }}
                                        </small>
                                    </td>
                                    <td>
                                        <div>{{ $history->login_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $history->login_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($history->logout_at)
                                            <div>{{ $history->logout_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $history->logout_at->format('H:i:s') }}</small>
                                        @else
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($history->logout_at)
                                            @php
                                                $duration = $history->login_at->diffInMinutes($history->logout_at);
                                                $hours = floor($duration / 60);
                                                $minutes = $duration % 60;
                                            @endphp
                                            @if($hours > 0)
                                                {{ $hours }}h {{ $minutes }}m
                                            @else
                                                {{ $minutes }}m
                                            @endif
                                        @else
                                            @php
                                                $duration = $history->login_at->diffInMinutes(now());
                                                $hours = floor($duration / 60);
                                                $minutes = $duration % 60;
                                            @endphp
                                            <span class="text-success">
                                                @if($hours > 0)
                                                    {{ $hours }}h {{ $minutes }}m
                                                @else
                                                    {{ $minutes }}m
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($history->logout_at)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-sign-out-alt me-1"></i>Đã đăng xuất
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-circle me-1"></i>Đang hoạt động
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Hiển thị {{ $loginHistories->firstItem() ?? 0 }} - {{ $loginHistories->lastItem() ?? 0 }} 
                            trong tổng số {{ $loginHistories->total() }} bản ghi
                        </div>
                        <div>
                            {{ $loginHistories->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có lịch sử đăng nhập</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-lg {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .stats-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    code {
        background-color: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.9em;
    }
</style>
@endpush

