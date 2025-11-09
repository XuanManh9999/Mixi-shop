@extends('layouts.admin')

@section('title', 'Lịch sử đăng nhập - Admin MixiShop')
@section('page-title', 'Lịch sử đăng nhập')
@section('page-description', 'Theo dõi lịch sử đăng nhập của tất cả users trong hệ thống')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-history"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['total']) }}</div>
            <div class="stats-label">Tổng lần đăng nhập</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['today']) }}</div>
            <div class="stats-label">Hôm nay</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['this_week']) }}</div>
            <div class="stats-label">Tuần này</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['this_month']) }}</div>
            <div class="stats-label">Tháng này</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card danger">
            <div class="stats-icon text-danger">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['active_sessions']) }}</div>
            <div class="stats-label">Phiên đang hoạt động</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number">{{ number_format($stats['unique_users_today']) }}</div>
            <div class="stats-label">Users đăng nhập hôm nay</div>
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
                <form method="GET" action="{{ route('admin.login-history.index') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Tên, email user...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Tất cả</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="ip_address" class="form-label">IP Address</label>
                            <input type="text" class="form-control" id="ip_address" name="ip_address" 
                                   value="{{ request('ip_address') }}" 
                                   placeholder="192.168.1.1">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="logged_out" {{ request('status') === 'logged_out' ? 'selected' : '' }}>Đã đăng xuất</option>
                            </select>
                        </div>
                        
                        <div class="col-md-1">
                            <label for="date_from" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-1">
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
                                    @if(request()->hasAny(['search', 'user_id', 'ip_address', 'status', 'date_from', 'date_to']))
                                        <a href="{{ route('admin.login-history.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times me-1"></i>Xóa bộ lọc
                                        </a>
                                        <span class="text-muted ms-2">
                                            Tìm thấy {{ $loginHistories->total() }} kết quả
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Danh Sách Lịch Sử Đăng Nhập 
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
                                    <th>User</th>
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
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-{{ $history->user->is_admin ? 'danger' : 'primary' }} rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    <a href="{{ route('admin.login-history.user', $history->user) }}" class="text-decoration-none">
                                                        {{ $history->user->name }}
                                                    </a>
                                                </div>
                                                <small class="text-muted">{{ $history->user->email }}</small>
                                                @if($history->user->is_admin)
                                                    <small class="badge bg-danger ms-1">
                                                        <i class="fas fa-shield-alt me-1"></i>Admin
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-primary">{{ $history->ip_address }}</code>
                                    </td>
                                    <td>
                                        <small class="text-muted" title="{{ $history->user_agent }}">
                                            {{ \Illuminate\Support\Str::limit($history->user_agent, 50) }}
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
                        <h5 class="text-muted">Chưa có lịch sử đăng nhập nào</h5>
                        <p class="text-muted">Lịch sử đăng nhập sẽ được ghi lại khi users đăng nhập vào hệ thống</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 16px;
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit search form on filter change
    const searchForm = document.getElementById('searchForm');
    const autoSubmitElements = ['user_id', 'status', 'date_from', 'date_to'];
    
    autoSubmitElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('change', function() {
                searchForm.submit();
            });
        }
    });
    
    // Clear search functionality
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('search').value = '';
        searchForm.submit();
    });
});
</script>
@endpush

