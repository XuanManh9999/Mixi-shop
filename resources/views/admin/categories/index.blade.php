@extends('layouts.admin')

@section('title', 'Quản lý Danh mục - Admin MixiShop')
@section('page-title', 'Quản lý Danh mục')
@section('page-description', 'Danh sách tất cả danh mục sản phẩm')

@section('content')
<!-- Search & Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search me-2"></i>Tìm Kiếm & Lọc</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.categories.index') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Tên danh mục...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Kích hoạt</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Vô hiệu hóa</option>
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
                                @if(request()->hasAny(['search', 'status']))
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Reset
                                    </a>
                                @endif
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
    <div class="col-md-4">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stats-number">{{ $stats['total'] }}</div>
            <div class="stats-label">Tổng Danh Mục</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['active'] }}</div>
            <div class="stats-label">Đang Kích Hoạt</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['inactive'] }}</div>
            <div class="stats-label">Vô Hiệu Hóa</div>
        </div>
    </div>
</div>

<!-- Main Categories Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2"></i>Danh Sách Danh Mục 
                    <span class="badge bg-primary">{{ $categories->total() }}</span>
                </h5>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Thêm Danh Mục
                </a>
            </div>
            <div class="card-body">
                @if($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            Tên Danh Mục
                                            @if(request('sort_by') === 'name')
                                                <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Slug</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'position', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            Vị Trí
                                            @if(request('sort_by', 'position') === 'position')
                                                <i class="fas fa-sort-{{ request('sort_order', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort text-muted"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Số Sản Phẩm</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Tạo</th>
                                    <th style="width: 150px;">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td><strong>#{{ $category->id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-{{ $category->is_active ? 'success' : 'secondary' }} rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-tag text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $category->name }}</div>
                                                @if($category->parent)
                                                    <small class="text-muted">
                                                        <i class="fas fa-arrow-right me-1"></i>{{ $category->parent->name }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-muted">{{ $category->slug }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $category->position }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-cubes me-1"></i>{{ $category->products_count }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Kích hoạt
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i>Vô hiệu
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $category->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $category->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.show', $category) }}" 
                                               class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('admin.categories.toggle-active', $category) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $category->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}">
                                                    <i class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        title="Xóa danh mục"
                                                        onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
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
                            Hiển thị {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} 
                            trong tổng số {{ $categories->total() }} danh mục
                        </div>
                        <div>
                            {{ $categories->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có danh mục nào</h5>
                        <p class="text-muted">Hãy tạo danh mục đầu tiên</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm Danh Mục
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
    // Auto-submit search form on filter change
    const searchForm = document.getElementById('searchForm');
    const autoSubmitElements = ['status', 'per_page'];
    
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
});
</script>
@endpush
