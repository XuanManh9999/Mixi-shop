@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm - Admin MixiShop')
@section('page-title', 'Quản lý Sản phẩm')
@section('page-description', 'Danh sách tất cả sản phẩm trong hệ thống')

@section('content')
<!-- Search & Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search me-2"></i>Tìm Kiếm & Lọc</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.products.index') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Tên sản phẩm...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="category" class="form-label">Danh mục</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Tất cả</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Kích hoạt</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Vô hiệu</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="stock" class="form-label">Tồn kho</label>
                            <select class="form-select" id="stock" name="stock">
                                <option value="">Tất cả</option>
                                <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                                <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Hiển thị</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                                <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
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
                                    @if(request()->hasAny(['search', 'category', 'status', 'stock']))
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times me-1"></i>Xóa bộ lọc
                                        </a>
                                        <span class="text-muted ms-2">
                                            Tìm thấy {{ $products->total() }} sản phẩm
                                        </span>
                                    @endif
                                </div>
                                
                                <div>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Thêm Sản Phẩm
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

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="stats-number">{{ $stats['total'] }}</div>
            <div class="stats-label">Tổng Sản Phẩm</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['active'] }}</div>
            <div class="stats-label">Đang Bán</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stats-number">{{ $stats['out_of_stock'] }}</div>
            <div class="stats-label">Hết Hàng</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon text-info">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['inactive'] }}</div>
            <div class="stats-label">Vô Hiệu</div>
        </div>
    </div>
</div>

<!-- Products Grid -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cubes me-2"></i>Danh Sách Sản Phẩm 
                    <span class="badge bg-primary">{{ $products->total() }}</span>
                </h5>
                
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="view_mode" id="grid_view" checked>
                        <label class="btn btn-outline-secondary" for="grid_view">
                            <i class="fas fa-th"></i>
                        </label>
                        
                        <input type="radio" class="btn-check" name="view_mode" id="list_view">
                        <label class="btn btn-outline-secondary" for="list_view">
                            <i class="fas fa-list"></i>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($products->count() > 0)
                    <!-- Grid View -->
                    <div id="gridView" class="row">
                        @foreach($products as $product)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 product-card">
                                <div class="position-relative">
                                    <img src="{{ $product->main_image }}" class="card-img-top" 
                                         style="height: 200px; object-fit: cover;" alt="{{ $product->name }}">
                                    
                                    <!-- Status Badges -->
                                    <div class="position-absolute top-0 end-0 m-2">
                                        @if(!$product->is_active)
                                            <span class="badge bg-danger mb-1 d-block">Vô hiệu</span>
                                        @endif
                                        @if($product->stock_quantity == 0)
                                            <span class="badge bg-warning mb-1 d-block">Hết hàng</span>
                                        @endif
                                        @if($product->is_on_sale)
                                            <span class="badge bg-success d-block">-{{ $product->discount_percentage }}%</span>
                                        @endif
                                    </div>
                                    
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-info">{{ $product->category->name }}</span>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <h6 class="card-title mb-2">{{ Str::limit($product->name, 40) }}</h6>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="text-primary fw-bold fs-5">{{ $product->formatted_price }}</span>
                                            @if($product->formatted_compare_price)
                                                <small class="text-muted text-decoration-line-through ms-1">
                                                    {{ $product->formatted_compare_price }}
                                                </small>
                                            @endif
                                        </div>
                                        <small class="text-muted">Stock: {{ $product->stock_qty }}</small>
                                    </div>
                                    
                                    @if($product->description)
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($product->description, 60) }}
                                        </p>
                                    @endif
                                    
                                    <div class="d-grid gap-2">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.products.show', $product) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.products.toggle-active', $product) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }} btn-sm">
                                                    <i class="fas fa-{{ $product->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- List View (Hidden by default) -->
                    <div id="listView" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Tồn kho</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $product->main_image }}" 
                                                     class="rounded me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                                     alt="{{ $product->name }}">
                                                <div>
                                                    <div class="fw-semibold">{{ $product->name }}</div>
                                                    <small class="text-muted">{{ $product->slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $product->category->name }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary">{{ $product->formatted_price }}</div>
                                            @if($product->formatted_compare_price)
                                                <small class="text-muted text-decoration-line-through">
                                                    {{ $product->formatted_compare_price }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->stock_qty > 0)
                                                <span class="badge bg-success">{{ $product->stock_qty }}</span>
                                            @else
                                                <span class="badge bg-danger">Hết hàng</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">Kích hoạt</span>
                                            @else
                                                <span class="badge bg-secondary">Vô hiệu</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.show', $product) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Hiển thị {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
                            trong tổng số {{ $products->total() }} sản phẩm
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có sản phẩm nào</h5>
                        <p class="text-muted">Hãy tạo sản phẩm đầu tiên</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm Sản Phẩm
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View mode toggle
    const gridViewBtn = document.getElementById('grid_view');
    const listViewBtn = document.getElementById('list_view');
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    
    gridViewBtn.addEventListener('change', function() {
        if (this.checked) {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
        }
    });
    
    listViewBtn.addEventListener('change', function() {
        if (this.checked) {
            listView.classList.remove('d-none');
            gridView.classList.add('d-none');
        }
    });
    
    // Auto-submit search form
    const searchForm = document.getElementById('searchForm');
    const autoSubmitElements = ['category', 'status', 'stock', 'per_page'];
    
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
