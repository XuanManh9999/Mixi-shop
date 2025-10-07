@extends('layouts.admin')

@section('title', 'Tạo Mã Giảm Giá - Admin MixiShop')
@section('page-title', 'Tạo Mã Giảm Giá')
@section('page-description', 'Thêm mã giảm giá mới cho khách hàng')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Thông Tin Mã Giảm Giá</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.coupons.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Mã Coupon <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control text-uppercase @error('code') is-invalid @enderror" 
                                       id="code" name="code" 
                                       value="{{ old('code') }}" 
                                       placeholder="VD: MIXISHIP50"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type" class="form-label">
                                    <i class="fas fa-percent me-1"></i>Loại giảm giá <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Chọn loại giảm giá</option>
                                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Cố định (₫)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="value" class="form-label">
                                    <i class="fas fa-dollar-sign me-1"></i>Giá trị <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" 
                                       value="{{ old('value') }}" 
                                       min="0" step="0.01"
                                       placeholder="0"
                                       required>
                                <small class="text-muted">% hoặc số tiền tùy loại</small>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_order_amount" class="form-label">
                                    <i class="fas fa-shopping-cart me-1"></i>Đơn hàng tối thiểu
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('min_order_amount') is-invalid @enderror" 
                                           id="min_order_amount" name="min_order_amount" 
                                           value="{{ old('min_order_amount') }}" 
                                           min="0" step="1000"
                                           placeholder="0">
                                    <span class="input-group-text">₫</span>
                                </div>
                                <small class="text-muted">Để trống nếu không giới hạn</small>
                                @error('min_order_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_discount_amount" class="form-label">
                                    <i class="fas fa-hand-holding-usd me-1"></i>Giảm tối đa
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('max_discount_amount') is-invalid @enderror" 
                                           id="max_discount_amount" name="max_discount_amount" 
                                           value="{{ old('max_discount_amount') }}" 
                                           min="0" step="1000"
                                           placeholder="0">
                                    <span class="input-group-text">₫</span>
                                </div>
                                <small class="text-muted">Áp dụng cho loại %</small>
                                @error('max_discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_at" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Ngày bắt đầu <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" 
                                       class="form-control @error('start_at') is-invalid @enderror" 
                                       id="start_at" name="start_at" 
                                       value="{{ old('start_at', now()->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('start_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_at" class="form-label">
                                    <i class="fas fa-calendar-times me-1"></i>Ngày kết thúc
                                </label>
                                <input type="datetime-local" 
                                       class="form-control @error('end_at') is-invalid @enderror" 
                                       id="end_at" name="end_at" 
                                       value="{{ old('end_at') }}">
                                <small class="text-muted">Để trống nếu không giới hạn</small>
                                @error('end_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">
                                    <i class="fas fa-users me-1"></i>Giới hạn số lần dùng
                                </label>
                                <input type="number" 
                                       class="form-control @error('usage_limit') is-invalid @enderror" 
                                       id="usage_limit" name="usage_limit" 
                                       value="{{ old('usage_limit') }}" 
                                       min="1"
                                       placeholder="Không giới hạn">
                                <small class="text-muted">Tổng số lần toàn bộ users</small>
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usage_per_user" class="form-label">
                                    <i class="fas fa-user me-1"></i>Giới hạn mỗi user
                                </label>
                                <input type="number" 
                                       class="form-control @error('usage_per_user') is-invalid @enderror" 
                                       id="usage_per_user" name="usage_per_user" 
                                       value="{{ old('usage_per_user') }}" 
                                       min="1"
                                       placeholder="Không giới hạn">
                                <small class="text-muted">Số lần mỗi user được dùng</small>
                                @error('usage_per_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6><i class="fas fa-filter me-2"></i>Áp dụng cho (Tùy chọn)</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apply_to_category_id" class="form-label">
                                    <i class="fas fa-tags me-1"></i>Áp dụng cho danh mục
                                </label>
                                <select class="form-select @error('apply_to_category_id') is-invalid @enderror" 
                                        id="apply_to_category_id" name="apply_to_category_id">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('apply_to_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('apply_to_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apply_to_product_id" class="form-label">
                                    <i class="fas fa-cube me-1"></i>Áp dụng cho sản phẩm
                                </label>
                                <select class="form-select @error('apply_to_product_id') is-invalid @enderror" 
                                        id="apply_to_product_id" name="apply_to_product_id">
                                    <option value="">Tất cả sản phẩm</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                {{ old('apply_to_product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('apply_to_product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-eye me-1"></i>Kích hoạt mã giảm giá
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Hướng dẫn:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Mã coupon:</strong> Tự động chuyển thành chữ hoa</li>
                            <li><strong>Phần trăm:</strong> Nhập 10 = giảm 10%, có thể giới hạn tối đa</li>
                            <li><strong>Cố định:</strong> Nhập 50000 = giảm 50.000₫</li>
                            <li><strong>Áp dụng:</strong> Để trống = áp dụng toàn bộ đơn hàng</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <button type="reset" class="btn btn-outline-warning me-2">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Tạo Mã
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
    // Generate random coupon code
    function generateCouponCode() {
        const prefix = 'MIXI';
        const random = Math.random().toString(36).substring(2, 8).toUpperCase();
        return prefix + random;
    }
    
    // Auto uppercase code
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Toggle max discount based on type
    const typeSelect = document.getElementById('type');
    const maxDiscountInput = document.getElementById('max_discount_amount');
    
    typeSelect.addEventListener('change', function() {
        if (this.value === 'percentage') {
            maxDiscountInput.parentElement.parentElement.classList.remove('d-none');
        } else {
            maxDiscountInput.parentElement.parentElement.classList.add('d-none');
        }
    });
});
</script>
@endpush
