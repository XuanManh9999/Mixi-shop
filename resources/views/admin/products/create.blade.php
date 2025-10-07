@extends('layouts.admin')

@section('title', 'Thêm Sản phẩm - Admin MixiShop')
@section('page-title', 'Thêm Sản phẩm')
@section('page-description', 'Tạo sản phẩm mới cho MixiShop')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Thông Tin Sản Phẩm</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Thông tin cơ bản -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-utensils me-1"></i>Tên sản phẩm <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Nhập tên sản phẩm"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Mô tả sản phẩm
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4"
                                          placeholder="Nhập mô tả chi tiết sản phẩm">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Hình ảnh -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">
                                    <i class="fas fa-image me-1"></i>Hình ảnh chính
                                </label>
                                <input type="file" 
                                       class="form-control @error('thumbnail') is-invalid @enderror" 
                                       id="thumbnail" name="thumbnail" 
                                       accept="image/*">
                                <small class="text-muted">JPG, PNG, GIF (tối đa 2MB)</small>
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="images" class="form-label">
                                    <i class="fas fa-images me-1"></i>Hình ảnh bổ sung
                                </label>
                                <input type="file" 
                                       class="form-control @error('images.*') is-invalid @enderror" 
                                       id="images" name="images[]" 
                                       accept="image/*" multiple>
                                <small class="text-muted">Có thể chọn nhiều hình</small>
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                                {{ old('category_id', request('category')) == $category->id ? 'selected' : '' }}>
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
                                           value="{{ old('price') }}" 
                                           min="0" step="1000"
                                           placeholder="0"
                                           required>
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
                                           value="{{ old('compare_at_price') }}" 
                                           min="0" step="1000"
                                           placeholder="0">
                                    <span class="input-group-text">₫</span>
                                </div>
                                <small class="text-muted">Giá gốc để hiển thị giảm giá</small>
                                @error('compare_at_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="stock_qty" class="form-label">
                                    <i class="fas fa-boxes me-1"></i>Số lượng <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('stock_qty') is-invalid @enderror" 
                                       id="stock_qty" name="stock_qty" 
                                       value="{{ old('stock_qty', 0) }}" 
                                       min="0"
                                       required>
                                @error('stock_qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="sku" class="form-label">
                                    <i class="fas fa-barcode me-1"></i>SKU (Mã sản phẩm)
                                </label>
                                <input type="text" 
                                       class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" 
                                       value="{{ old('sku') }}" 
                                       placeholder="Để trống để tự động tạo">
                                <small class="text-muted">Tự động tạo nếu không nhập</small>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-eye me-1"></i>Kích hoạt sản phẩm
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Tên sản phẩm sẽ tự động tạo slug URL thân thiện</li>
                            <li>Hình ảnh chính sẽ hiển thị làm thumbnail</li>
                            <li>Giá so sánh giúp hiển thị % giảm giá</li>
                            <li>Sản phẩm vô hiệu sẽ không hiển thị cho khách hàng</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <button type="reset" class="btn btn-outline-warning me-2">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Tạo Sản Phẩm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate discount percentage
    const priceInput = document.getElementById('price');
    const comparePriceInput = document.getElementById('compare_price');
    
    function updateDiscountPreview() {
        const price = parseFloat(priceInput.value) || 0;
        const comparePrice = parseFloat(comparePriceInput.value) || 0;
        
        if (comparePrice > price && price > 0) {
            const discount = Math.round(((comparePrice - price) / comparePrice) * 100);
            // You can show discount preview here
        }
    }
    
    priceInput.addEventListener('input', updateDiscountPreview);
    comparePriceInput.addEventListener('input', updateDiscountPreview);
});
</script>
@endpush
