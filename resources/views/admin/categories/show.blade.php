@extends('layouts.admin')

@section('title', 'Chi tiết Danh mục - Admin MixiShop')
@section('page-title', 'Chi tiết Danh mục')
@section('page-description', $category->name)

@section('content')
<div class="row">
    <!-- Category Info -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông Tin Danh Mục</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-lg bg-{{ $category->is_active ? 'success' : 'secondary' }} rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="fas fa-tag fa-2x text-white"></i>
                    </div>
                    <h4>{{ $category->name }}</h4>
                    @if($category->is_active)
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Kích hoạt</span>
                    @else
                        <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Vô hiệu</span>
                    @endif
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td>#{{ $category->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Slug:</strong></td>
                        <td><code>{{ $category->slug }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Vị trí:</strong></td>
                        <td><span class="badge bg-info">{{ $category->position }}</span></td>
                    </tr>
                    @if($category->parent)
                    <tr>
                        <td><strong>Danh mục cha:</strong></td>
                        <td>
                            <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-decoration-none">
                                {{ $category->parent->name }}
                            </a>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Sản phẩm:</strong></td>
                        <td><span class="badge bg-primary">{{ $category->products_count }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Ngày tạo:</strong></td>
                        <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Cập nhật:</strong></td>
                        <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    <form method="POST" action="{{ route('admin.categories.toggle-active', $category) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }} me-2"></i>
                            {{ $category->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Subcategories -->
        @if($category->children()->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>Danh Mục Con</h5>
            </div>
            <div class="card-body">
                @foreach($category->children as $child)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <a href="{{ route('admin.categories.show', $child) }}" class="text-decoration-none">
                                {{ $child->name }}
                            </a>
                            @if($child->is_active)
                                <span class="badge bg-success ms-2">Active</span>
                            @else
                                <span class="badge bg-secondary ms-2">Inactive</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $child->products_count }} sản phẩm</small>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <!-- Products in Category -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cubes me-2"></i>Sản Phẩm Trong Danh Mục
                    <span class="badge bg-primary">{{ $products->total() }}</span>
                </h5>
                <a href="{{ route('admin.products.create', ['category' => $category->id]) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Thêm Sản Phẩm
                </a>
            </div>
            <div class="card-body">
                @if($products->count() > 0)
                    <div class="row">
                        @foreach($products as $product)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="{{ $product->main_image }}" class="card-img-top" 
                                         style="height: 150px; object-fit: cover;" alt="{{ $product->name }}">
                                    @if(!$product->is_active)
                                        <span class="position-absolute top-0 end-0 badge bg-danger m-2">
                                            Vô hiệu
                                        </span>
                                    @endif
                                    @if($product->stock_quantity == 0)
                                        <span class="position-absolute top-0 start-0 badge bg-warning m-2">
                                            Hết hàng
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">{{ Str::limit($product->name, 30) }}</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-primary fw-bold">{{ $product->formatted_price }}</span>
                                        <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                                    </div>
                                    <div class="d-grid gap-1">
                                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Xem
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-cubes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có sản phẩm nào</h5>
                        <p class="text-muted">Hãy thêm sản phẩm đầu tiên cho danh mục này</p>
                        <a href="{{ route('admin.products.create', ['category' => $category->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm Sản Phẩm
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
            <div>
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                </a>
                @if($category->products_count == 0)
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                        <i class="fas fa-trash me-2"></i>Xóa
                    </button>
                </form>
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
        font-size: 24px;
    }
</style>
@endpush
