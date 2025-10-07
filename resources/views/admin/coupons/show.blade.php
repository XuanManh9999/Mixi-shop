@extends('layouts.admin')

@section('title', 'Chi tiết Mã giảm giá - Admin MixiShop')
@section('page-title', 'Chi tiết Mã giảm giá')
@section('page-description', $coupon->code)

@section('content')
<div class="row">
    <!-- Coupon Info -->
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông Tin Coupon</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="coupon-display p-4 bg-gradient rounded-3" style="background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);">
                        <div class="text-white">
                            <div class="mb-2">
                                <i class="fas fa-ticket-alt fa-3x"></i>
                            </div>
                            <h3 class="mb-2 fw-bold">{{ $coupon->code }}</h3>
                            <h4 class="mb-0">{{ $coupon->formatted_value }}</h4>
                            <div class="mt-3">
                                <span class="badge bg-{{ $coupon->status_color }} fs-6">
                                    {{ $coupon->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td>#{{ $coupon->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Loại:</strong></td>
                        <td>
                            @if($coupon->type === 'percentage')
                                <span class="badge bg-info">Phần trăm</span>
                            @else
                                <span class="badge bg-primary">Cố định</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Giá trị:</strong></td>
                        <td class="text-success fw-bold">{{ $coupon->formatted_value }}</td>
                    </tr>
                    @if($coupon->max_discount_amount)
                    <tr>
                        <td><strong>Giảm tối đa:</strong></td>
                        <td>{{ number_format($coupon->max_discount_amount, 0, ',', '.') }}₫</td>
                    </tr>
                    @endif
                    @if($coupon->min_order_amount)
                    <tr>
                        <td><strong>Đơn tối thiểu:</strong></td>
                        <td>{{ number_format($coupon->min_order_amount, 0, ',', '.') }}₫</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Thời gian:</strong></td>
                        <td>
                            <div>Từ: {{ $coupon->start_at->format('d/m/Y H:i') }}</div>
                            @if($coupon->end_at)
                                <div>Đến: {{ $coupon->end_at->format('d/m/Y H:i') }}</div>
                            @else
                                <div class="text-info">Không giới hạn</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Sử dụng:</strong></td>
                        <td>
                            <span class="badge bg-primary">{{ $coupon->used_count }}</span>
                            @if($coupon->usage_limit)
                                / {{ $coupon->usage_limit }}
                            @else
                                / Không giới hạn
                            @endif
                        </td>
                    </tr>
                    @if($coupon->usage_per_user)
                    <tr>
                        <td><strong>Mỗi user:</strong></td>
                        <td>{{ $coupon->usage_per_user }} lần</td>
                    </tr>
                    @endif
                    @if($coupon->category)
                    <tr>
                        <td><strong>Danh mục:</strong></td>
                        <td>
                            <a href="{{ route('admin.categories.show', $coupon->category) }}" class="text-decoration-none">
                                <i class="fas fa-tag me-1"></i>{{ $coupon->category->name }}
                            </a>
                        </td>
                    </tr>
                    @endif
                    @if($coupon->product)
                    <tr>
                        <td><strong>Sản phẩm:</strong></td>
                        <td>
                            <a href="{{ route('admin.products.show', $coupon->product) }}" class="text-decoration-none">
                                <i class="fas fa-cube me-1"></i>{{ $coupon->product->name }}
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    <form method="POST" action="{{ route('admin.coupons.toggle-active', $coupon) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $coupon->is_active ? 'eye-slash' : 'eye' }} me-2"></i>
                            {{ $coupon->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Usage History -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Lịch Sử Sử Dụng
                    <span class="badge bg-primary">{{ $coupon->couponUsers->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($coupon->couponUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Số lần dùng</th>
                                    <th>Lần đầu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupon->couponUsers as $couponUser)
                                <tr>
                                    <td>{{ $couponUser->user->name }}</td>
                                    <td>{{ $couponUser->user->email }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $couponUser->used_times }}</span>
                                    </td>
                                    <td>{{ $couponUser->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có ai sử dụng</h5>
                        <p class="text-muted">Lịch sử sẽ hiển thị khi có người dùng</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Stats -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="stats-card success">
                    <div class="stats-icon text-success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number">{{ $coupon->couponUsers->count() }}</div>
                    <div class="stats-label">Users Đã Dùng</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card warning">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-redo"></i>
                    </div>
                    <div class="stats-number">{{ $coupon->used_count }}</div>
                    <div class="stats-label">Tổng Lượt Dùng</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card info">
                    <div class="stats-icon text-info">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stats-number">
                        @if($coupon->usage_limit)
                            {{ $coupon->usage_limit - $coupon->used_count }}
                        @else
                            ∞
                        @endif
                    </div>
                    <div class="stats-label">Còn Lại</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
            <div>
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                </a>
                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Bạn có chắc muốn xóa mã này?')">
                        <i class="fas fa-trash me-2"></i>Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
