@extends('layouts.admin')

@section('title', 'Quản lý Mã giảm giá - Admin MixiShop')
@section('page-title', 'Quản lý Mã giảm giá')
@section('page-description', 'Danh sách tất cả mã giảm giá trong hệ thống')

@section('content')
<!-- Search & Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search me-2"></i>Tìm Kiếm & Lọc</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.coupons.index') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm mã</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Nhập mã coupon...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="type" class="form-label">Loại giảm</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tất cả</option>
                                <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Phần trăm</option>
                                <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Cố định</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Vô hiệu</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Hiển thị</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Tìm
                                </button>
                                @if(request()->hasAny(['search', 'type', 'status']))
                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Reset
                                    </a>
                                @endif
                                <a href="{{ route('admin.coupons.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i>Tạo Mã
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
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stats-number">{{ $stats['total'] }}</div>
            <div class="stats-label">Tổng Mã</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['active'] }}</div>
            <div class="stats-label">Đang Hoạt Động</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['inactive'] }}</div>
            <div class="stats-label">Vô Hiệu</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card danger">
            <div class="stats-icon text-danger">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div class="stats-number">{{ $stats['expired'] }}</div>
            <div class="stats-label">Hết Hạn</div>
        </div>
    </div>
</div>

<!-- Coupons Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-ticket-alt me-2"></i>Danh Sách Mã Giảm Giá 
                    <span class="badge bg-primary">{{ $coupons->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($coupons->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã Coupon</th>
                                    <th>Loại</th>
                                    <th>Giá trị</th>
                                    <th>Đơn tối thiểu</th>
                                    <th>Thời gian</th>
                                    <th>Sử dụng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupons as $coupon)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success rounded d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-ticket-alt text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-primary">{{ $coupon->code }}</div>
                                                @if($coupon->category)
                                                    <small class="text-muted">
                                                        <i class="fas fa-tag me-1"></i>{{ $coupon->category->name }}
                                                    </small>
                                                @elseif($coupon->product)
                                                    <small class="text-muted">
                                                        <i class="fas fa-cube me-1"></i>{{ $coupon->product->name }}
                                                    </small>
                                                @else
                                                    <small class="text-info">Toàn bộ đơn hàng</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($coupon->type === 'percentage')
                                            <span class="badge bg-info">Phần trăm</span>
                                        @else
                                            <span class="badge bg-primary">Cố định</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ $coupon->formatted_value }}</strong>
                                        @if($coupon->max_discount_amount && $coupon->type === 'percentage')
                                            <br><small class="text-muted">Tối đa: {{ number_format($coupon->max_discount_amount, 0, ',', '.') }}₫</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->min_order_amount)
                                            {{ number_format($coupon->min_order_amount, 0, ',', '.') }}₫
                                        @else
                                            <span class="text-muted">Không</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div><small>Từ: {{ $coupon->start_at->format('d/m/Y') }}</small></div>
                                        @if($coupon->end_at)
                                            <div><small>Đến: {{ $coupon->end_at->format('d/m/Y') }}</small></div>
                                        @else
                                            <div><small class="text-info">Không giới hạn</small></div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            @php
                                                $percentage = $coupon->usage_limit ? ($coupon->used_count / $coupon->usage_limit) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-{{ $percentage > 80 ? 'danger' : 'success' }}" 
                                                 style="width: {{ $percentage }}%">
                                                {{ $coupon->used_count }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $coupon->status_color }}">
                                            {{ $coupon->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.coupons.show', $coupon) }}" 
                                               class="btn btn-sm btn-outline-info" title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.coupons.toggle-active', $coupon) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $coupon->is_active ? 'Vô hiệu' : 'Kích hoạt' }}">
                                                    <i class="fas fa-{{ $coupon->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        title="Xóa"
                                                        onclick="return confirm('Bạn có chắc muốn xóa mã này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Hiển thị {{ $coupons->firstItem() ?? 0 }} - {{ $coupons->lastItem() ?? 0 }} 
                            trong tổng số {{ $coupons->total() }} mã giảm giá
                        </div>
                        <div>
                            {{ $coupons->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có mã giảm giá nào</h5>
                        <p class="text-muted">Hãy tạo mã giảm giá đầu tiên</p>
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo Mã Giảm Giá
                        </a>
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
    const autoSubmitElements = ['type', 'status', 'per_page'];
    
    autoSubmitElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('change', function() {
                searchForm.submit();
            });
        }
    });
    
    // Clear search
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('search').value = '';
        searchForm.submit();
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
});
</script>
@endpush
