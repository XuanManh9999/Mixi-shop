@extends('layouts.admin')

@section('title', 'Chi tiết Sản phẩm - Admin MixiShop')
@section('page-title', 'Chi tiết Sản phẩm')
@section('page-description', $product->name)

@section('content')
<div class="row">
    <!-- Product Info -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ $product->main_image }}" class="img-fluid rounded mb-3" alt="{{ $product->name }}">
                
                <h4>{{ $product->name }}</h4>
                <p class="text-muted">{{ $product->category->name }}</p>
                
                <div class="mb-3">
                    <span class="fs-3 text-primary fw-bold">{{ $product->formatted_price }}</span>
                    @if($product->formatted_compare_price)
                        <div>
                            <small class="text-muted text-decoration-line-through">
                                {{ $product->formatted_compare_price }}
                            </small>
                            <span class="badge bg-success ms-2">-{{ $product->discount_percentage }}%</span>
                        </div>
                    @endif
                </div>
                
                <div class="d-flex justify-content-around mb-3">
                    <div>
                        @if($product->is_active)
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Kích hoạt</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Vô hiệu</span>
                        @endif
                    </div>
                    <div>
                        @if($product->in_stock)
                            <span class="badge bg-primary">Còn {{ $product->stock_qty }}</span>
                        @else
                            <span class="badge bg-danger">Hết hàng</span>
                        @endif
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    <form method="POST" action="{{ route('admin.products.toggle-active', $product) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $product->is_active ? 'eye-slash' : 'eye' }} me-2"></i>
                            {{ $product->is_active ? 'Vô hiệu' : 'Kích hoạt' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông Tin</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td>#{{ $product->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>SKU:</strong></td>
                        <td><code>{{ $product->sku }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Slug:</strong></td>
                        <td><code>{{ $product->slug }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Danh mục:</strong></td>
                        <td>
                            <a href="{{ route('admin.categories.show', $product->category) }}">
                                {{ $product->category->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Tạo:</strong></td>
                        <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Cập nhật:</strong></td>
                        <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Product Details -->
    <div class="col-md-8">
        <!-- Description -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Mô Tả Sản Phẩm</h5>
            </div>
            <div class="card-body">
                @if($product->description)
                    <p>{{ $product->description }}</p>
                @else
                    <p class="text-muted">Chưa có mô tả</p>
                @endif
            </div>
        </div>
        
        <!-- Images Gallery -->
        @if($product->images->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-images me-2"></i>Thư Viện Hình Ảnh
                    <span class="badge bg-primary">{{ $product->images->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($product->images as $image)
                    <div class="col-md-3 mb-3">
                        <img src="{{ $image->full_image_url }}" class="img-fluid rounded" alt="Product image">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <!-- Order History -->
        @if($product->orderItems->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-bag me-2"></i>Lịch Sử Đơn Hàng
                    <span class="badge bg-primary">{{ $product->orderItems->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Đơn hàng</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                                <th>Ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->orderItems->take(10) as $orderItem)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $orderItem->order) }}">
                                        #{{ $orderItem->order_id }}
                                    </a>
                                </td>
                                <td><span class="badge bg-info">{{ $orderItem->quantity }}</span></td>
                                <td>{{ $orderItem->formatted_unit_price }}</td>
                                <td class="text-success fw-bold">{{ $orderItem->formatted_total_price }}</td>
                                <td>{{ $orderItem->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($product->orderItems->count() > 10)
                    <div class="text-center mt-2">
                        <small class="text-muted">Và {{ $product->orderItems->count() - 10 }} đơn hàng khác...</small>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
            <div>
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                </a>
                @if($product->orderItems->count() == 0)
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                        <i class="fas fa-trash me-2"></i>Xóa
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
