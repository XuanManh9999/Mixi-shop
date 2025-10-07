@extends('layouts.admin')

@section('title', 'Chỉnh sửa Mã giảm giá - Admin MixiShop')
@section('page-title', 'Chỉnh sửa Mã giảm giá')
@section('page-description', $coupon->code)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Chỉnh Sửa Mã Giảm Giá</h5>
                <span class="badge bg-{{ $coupon->status_color }}">{{ $coupon->status_label }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Mã Coupon <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control text-uppercase @error('code') is-invalid @enderror" 
                                       id="code" name="code" 
                                       value="{{ old('code', $coupon->code) }}" 
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
                                    <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>
                                        Phần trăm (%)
                                    </option>
                                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>
                                        Cố định (₫)
                                    </option>
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
                                       value="{{ old('value', $coupon->value) }}" 
                                       min="0" step="0.01" required>
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
                                           value="{{ old('min_order_amount', $coupon->min_order_amount) }}" 
                                           min="0" step="1000">
                                    <span class="input-group-text">₫</span>
                                </div>
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
                                           value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" 
                                           min="0" step="1000">
                                    <span class="input-group-text">₫</span>
                                </div>
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
                                       value="{{ old('start_at', $coupon->start_at?->format('Y-m-d\TH:i')) }}" 
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
                                       value="{{ old('end_at', $coupon->end_at?->format('Y-m-d\TH:i')) }}">
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
                                       value="{{ old('usage_limit', $coupon->usage_limit) }}" 
                                       min="1"
                                       placeholder="Không giới hạn">
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
                                       value="{{ old('usage_per_user', $coupon->usage_per_user) }}" 
                                       min="1"
                                       placeholder="Không giới hạn">
                                @error('usage_per_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6><i class="fas fa-filter me-2"></i>Áp dụng cho</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apply_to_category_id" class="form-label">
                                    <i class="fas fa-tags me-1"></i>Danh mục
                                </label>
                                <select class="form-select" id="apply_to_category_id" name="apply_to_category_id">
                                    <option value="">Tất cả</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('apply_to_category_id', $coupon->apply_to_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apply_to_product_id" class="form-label">
                                    <i class="fas fa-cube me-1"></i>Sản phẩm
                                </label>
                                <select class="form-select" id="apply_to_product_id" name="apply_to_product_id">
                                    <option value="">Tất cả</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                {{ old('apply_to_product_id', $coupon->apply_to_product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" 
                                       {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-eye me-1"></i>Kích hoạt mã giảm giá
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Thông tin mã:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>ID:</strong> #{{ $coupon->id }}</li>
                            <li><strong>Đã dùng:</strong> {{ $coupon->used_count }} lần</li>
                            <li><strong>Ngày tạo:</strong> {{ $coupon->created_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn btn-outline-info me-2">
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
