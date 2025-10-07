@extends('layouts.admin')

@section('title', 'Thêm Danh Mục - Admin MixiShop')
@section('page-title', 'Thêm Danh Mục')
@section('page-description', 'Tạo danh mục sản phẩm mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Thông Tin Danh Mục</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Tên danh mục <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name') }}" 
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
                                                {{ old('parent_id') == $parentCategory->id ? 'selected' : '' }}>
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
                                       value="{{ old('position', 0) }}" 
                                       min="0"
                                       placeholder="0">
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
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Kích hoạt danh mục
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Tên danh mục sẽ tự động tạo slug URL thân thiện</li>
                            <li>Vị trí sắp xếp giúp điều khiển thứ tự hiển thị</li>
                            <li>Danh mục con sẽ hiển thị dưới danh mục cha</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <button type="reset" class="btn btn-outline-warning me-2">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Tạo Danh Mục
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
    // Auto generate slug from name
    const nameInput = document.getElementById('name');
    nameInput.addEventListener('input', function() {
        // You can add slug preview here if needed
    });
});
</script>
@endpush
