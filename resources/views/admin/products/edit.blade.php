@extends('layouts.admin')

@section('title', 'Chỉnh sửa Sản phẩm - Admin MixiShop')
@section('page-title', 'Chỉnh sửa Sản phẩm')
@section('page-description', $product->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Chỉnh Sửa Sản Phẩm</h5>
                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                    {{ $product->is_active ? 'Kích hoạt' : 'Vô hiệu' }}
                </span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-utensils me-1"></i>Tên sản phẩm <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name', $product->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Mô tả
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Hình ảnh hiện tại</label>
                                <div class="border rounded p-2">
                                    <img src="{{ $product->main_image }}" class="img-fluid rounded" alt="{{ $product->name }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">
                                    <i class="fas fa-image me-1"></i>Thay đổi hình
                                </label>
                                <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                       id="thumbnail" name="thumbnail" accept="image/*">
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="images" class="form-label">
                                    <i class="fas fa-images me-1"></i>Hình bổ sung
                                </label>
                                <input type="file" class="form-control" 
                                       id="images" name="images[]" accept="image/*" multiple>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">
                                    <i class="fas fa-tags me-1"></i>Danh mục <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="price" class="form-label">
                                    <i class="fas fa-dollar-sign me-1"></i>Giá bán <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" 
                                           value="{{ old('price', $product->price) }}" 
                                           min="0" step="1000" required>
                                    <span class="input-group-text">₫</span>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="compare_at_price" class="form-label">
                                    <i class="fas fa-percentage me-1"></i>Giá so sánh
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('compare_at_price') is-invalid @enderror" 
                                           id="compare_at_price" name="compare_at_price" 
                                           value="{{ old('compare_at_price', $product->compare_at_price) }}" 
                                           min="0" step="1000">
                                    <span class="input-group-text">₫</span>
                                </div>
                                @error('compare_at_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="stock_qty" class="form-label">
                                    <i class="fas fa-boxes me-1"></i>Tồn kho <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('stock_qty') is-invalid @enderror" 
                                       id="stock_qty" name="stock_qty" 
                                       value="{{ old('stock_qty', $product->stock_qty) }}" 
                                       min="0" required>
                                @error('stock_qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">
                                    <i class="fas fa-barcode me-1"></i>SKU
                                </label>
                                <input type="text" 
                                       class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" 
                                       value="{{ old('sku', $product->sku) }}" 
                                       placeholder="Mã sản phẩm">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i>Trạng thái
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" 
                                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Kích hoạt sản phẩm
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Thông tin:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>ID:</strong> #{{ $product->id }}</li>
                            <li><strong>Slug:</strong> <code>{{ $product->slug }}</code></li>
                            <li><strong>Tạo:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</li>
                            <li><strong>Cập nhật:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info me-2">
                                <i class="fas fa-eye me-2"></i>Xem chi tiết
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
