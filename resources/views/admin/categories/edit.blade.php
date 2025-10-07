@extends('layouts.admin')

@section('title', 'Chỉnh sửa Danh mục - Admin MixiShop')
@section('page-title', 'Chỉnh sửa Danh mục')
@section('page-description', 'Cập nhật thông tin danh mục: ' . $category->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Chỉnh Sửa Danh Mục</h5>
                <div>
                    @if($category->is_active)
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Kích hoạt</span>
                    @else
                        <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Vô hiệu</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Tên danh mục <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name', $category->name) }}" 
                                       placeholder="Nhập tên danh mục"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="parent_id" class="form-label">
                                    <i class="fas fa-sitemap me-1"></i>Danh mục cha
                                </label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" 
                                        id="parent_id" name="parent_id">
                                    <option value="">Không có (Danh mục gốc)</option>
                                    @foreach($parentCategories as $parentCategory)
                                        <option value="{{ $parentCategory->id }}" 
                                                {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                            {{ $parentCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">
                                    <i class="fas fa-sort me-1"></i>Vị trí sắp xếp
                                </label>
                                <input type="number" 
                                       class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" 
                                       value="{{ old('position', $category->position) }}" 
                                       min="0">
                                <small class="text-muted">Số càng nhỏ hiển thị càng trước</small>
                                @error('position')
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
                                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Kích hoạt danh mục
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Thông tin danh mục:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>ID:</strong> #{{ $category->id }}</li>
                            <li><strong>Slug:</strong> <code>{{ $category->slug }}</code></li>
                            <li><strong>Số sản phẩm:</strong> {{ $category->products_count }}</li>
                            <li><strong>Ngày tạo:</strong> {{ $category->created_at->format('d/m/Y H:i') }}</li>
                            <li><strong>Cập nhật cuối:</strong> {{ $category->updated_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-outline-info me-2">
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
