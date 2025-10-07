@extends('layouts.admin')

@section('title', 'Quản lý Users - Admin MixiShop')
@section('page-title', 'Quản lý Users')
@section('page-description', 'Danh sách tất cả users trong hệ thống')

@section('content')
<!-- Advanced Search & Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search me-2"></i>Tìm Kiếm & Lọc</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Tên, email, số điện thoại...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="role" class="form-label">Quyền hạn</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Tất cả</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Hiển thị</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
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
                                    @if(request()->hasAny(['search', 'role', 'date_from', 'date_to']))
                                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times me-1"></i>Xóa bộ lọc
                                        </a>
                                        <span class="text-muted ms-2">
                                            Tìm thấy {{ $users->total() }} kết quả
                                        </span>
                                    @endif
                                </div>
                                
                                <div>
                                    <a href="{{ route('admin.users.export', request()->query()) }}" 
                                       class="btn btn-success btn-sm me-2">
                                        <i class="fas fa-download me-1"></i>Export CSV
                                    </a>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Thêm User
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main Users Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Danh Sách Users 
                    <span class="badge bg-primary">{{ $users->total() }}</span>
                </h5>
                
                <!-- Bulk Actions -->
                <div class="d-flex align-items-center">
                    <form method="POST" action="{{ route('admin.users.bulk-action') }}" id="bulkForm" class="me-2">
                        @csrf
                        <div class="input-group input-group-sm">
                            <select class="form-select" name="action" id="bulkAction" style="max-width: 150px;">
                                <option value="">Chọn thao tác...</option>
                                <option value="make_admin">Cấp quyền Admin</option>
                                <option value="remove_admin">Bỏ quyền Admin</option>
                                <option value="delete">Xóa users</option>
                            </select>
                            <button type="submit" class="btn btn-outline-secondary" disabled id="bulkSubmit">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            Tên
                                            @if(request('sort_by') === 'name')
                                                <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            Email
                                            @if(request('sort_by') === 'email')
                                                <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Số điện thoại</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'is_admin', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            Quyền
                                            @if(request('sort_by') === 'is_admin')
                                                <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            Ngày tạo
                                            @if(request('sort_by', 'created_at') === 'created_at')
                                                <i class="fas fa-sort-{{ request('sort_order', 'desc') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 120px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" 
                                                   name="user_ids[]" value="{{ $user->id }}"
                                                   {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td><strong>#{{ $user->id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-{{ $user->is_admin ? 'danger' : 'primary' }} rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                @if($user->is_admin)
                                                    <small class="text-danger"><i class="fas fa-shield-alt me-1"></i>Admin</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?: '-' }}</td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-crown me-1"></i>Admin
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="fas fa-user me-1"></i>User
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($user->id !== Auth::id())
                                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-{{ $user->is_admin ? 'warning' : 'success' }}" 
                                                            title="{{ $user->is_admin ? 'Bỏ quyền Admin' : 'Cấp quyền Admin' }}"
                                                            onclick="return confirm('{{ $user->is_admin ? 'Bạn có chắc muốn bỏ quyền admin?' : 'Bạn có chắc muốn cấp quyền admin?' }}')">
                                                        <i class="fas fa-{{ $user->is_admin ? 'user-minus' : 'user-plus' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Xóa user"
                                                            onclick="return confirm('Bạn có chắc muốn xóa user này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="btn btn-sm btn-outline-secondary disabled" title="Không thể thao tác với chính mình">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Enhanced Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Hiển thị {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} 
                            trong tổng số {{ $users->total() }} users
                        </div>
                        <div>
                            {{ $users->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có user nào</h5>
                        <p class="text-muted">Hãy tạo user đầu tiên</p>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm User Mới
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Stats Cards -->
<div class="row mt-4">
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number">{{ $stats['total'] }}</div>
            <div class="stats-label">Tổng Users</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stats-number">{{ $stats['admins'] }}</div>
            <div class="stats-label">Admins</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-user"></i>
            </div>
            <div class="stats-number">{{ $stats['users'] }}</div>
            <div class="stats-label">Users Thường</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stats-number">{{ $stats['today'] }}</div>
            <div class="stats-label">Hôm nay</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stats-number">{{ $stats['this_week'] }}</div>
            <div class="stats-label">Tuần này</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stats-number">{{ $stats['this_month'] }}</div>
            <div class="stats-label">Tháng này</div>
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
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .search-highlight {
        background-color: #fff3cd;
        padding: 2px 4px;
        border-radius: 3px;
    }
    
    .table th a:hover {
        color: #ff6b6b !important;
    }
    
    .stats-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .form-check-input:checked {
        background-color: #ff6b6b;
        border-color: #ff6b6b;
    }
    
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox:not([disabled])');
    const bulkForm = document.getElementById('bulkForm');
    const bulkAction = document.getElementById('bulkAction');
    const bulkSubmit = document.getElementById('bulkSubmit');
    
    // Handle select all
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });
    
    // Handle individual checkboxes
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAll();
            updateBulkActions();
        });
    });
    
    function updateSelectAll() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        selectAllCheckbox.checked = checkedCount === userCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < userCheckboxes.length;
    }
    
    function updateBulkActions() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        bulkSubmit.disabled = checkedCount === 0 || bulkAction.value === '';
        
        // Update bulk action text
        if (checkedCount > 0) {
            bulkAction.parentElement.querySelector('.form-select').style.borderColor = '#ff6b6b';
        } else {
            bulkAction.parentElement.querySelector('.form-select').style.borderColor = '#dee2e6';
        }
    }
    
    // Handle bulk action selection
    bulkAction.addEventListener('change', function() {
        updateBulkActions();
    });
    
    // Handle bulk form submission
    bulkForm.addEventListener('submit', function(e) {
        const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
        const action = bulkAction.value;
        
        if (checkedCheckboxes.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một user!');
            return;
        }
        
        let confirmMessage = '';
        switch(action) {
            case 'delete':
                confirmMessage = `Bạn có chắc muốn xóa ${checkedCheckboxes.length} user đã chọn?`;
                break;
            case 'make_admin':
                confirmMessage = `Bạn có chắc muốn cấp quyền admin cho ${checkedCheckboxes.length} user đã chọn?`;
                break;
            case 'remove_admin':
                confirmMessage = `Bạn có chắc muốn bỏ quyền admin của ${checkedCheckboxes.length} user đã chọn?`;
                break;
        }
        
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return;
        }
        
        // Add loading state
        bulkSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        bulkSubmit.disabled = true;
        
        // Add checked user IDs to form
        checkedCheckboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_ids[]';
            input.value = checkbox.value;
            bulkForm.appendChild(input);
        });
    });
    
    // Auto-submit search form on filter change
    const searchForm = document.getElementById('searchForm');
    const autoSubmitElements = ['role', 'per_page', 'date_from', 'date_to'];
    
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
    
    // Real-time search with debounce
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
    
    // Stats cards click functionality
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('click', function() {
            const label = this.querySelector('.stats-label').textContent.trim();
            let roleFilter = '';
            
            switch(label) {
                case 'Admins':
                    roleFilter = 'admin';
                    break;
                case 'Users Thường':
                    roleFilter = 'user';
                    break;
            }
            
            if (roleFilter) {
                const url = new URL(window.location);
                url.searchParams.set('role', roleFilter);
                window.location.href = url.toString();
            }
        });
    });
    
    // Highlight search terms
    const searchTerm = '{{ request("search") }}';
    if (searchTerm) {
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        document.querySelectorAll('tbody td').forEach(td => {
            if (td.textContent.includes(searchTerm)) {
                td.innerHTML = td.innerHTML.replace(regex, '<span class="search-highlight">$1</span>');
            }
        });
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Show loading state on page navigation
    document.querySelectorAll('a[href*="admin/users"], .pagination a').forEach(link => {
        link.addEventListener('click', function() {
            document.body.classList.add('loading');
        });
    });
});
</script>
@endpush
