@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá - Admin MixiShop')
@section('page-title', 'Quản lý Đánh giá')
@section('page-description', 'Duyệt và quản lý đánh giá sản phẩm')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card primary">
            <div class="stats-icon text-primary">
                <i class="fas fa-star"></i>
            </div>
            <div class="stats-number">{{ $stats['total'] }}</div>
            <div class="stats-label">Tổng đánh giá</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card success">
            <div class="stats-icon text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['approved'] }}</div>
            <div class="stats-label">Đã duyệt</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card warning">
            <div class="stats-icon text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number">{{ $stats['pending'] }}</div>
            <div class="stats-label">Chờ duyệt</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" name="search" 
                       value="{{ request('search') }}" placeholder="Tên, email, sản phẩm...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Số sao</label>
                <select name="rating" class="form-select">
                    <option value="">Tất cả</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} sao
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Lọc
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reviews Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách đánh giá</h5>
        <div>
            <form method="POST" action="{{ route('admin.reviews.bulk-action') }}" id="bulkActionForm" class="d-inline">
                @csrf
                <input type="hidden" name="review_ids" id="bulkReviewIds">
                <input type="hidden" name="action" id="bulkAction">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('approve')">
                        <i class="fas fa-check me-1"></i>Duyệt
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('reject')">
                        <i class="fas fa-times me-1"></i>Từ chối
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                        <i class="fas fa-trash me-1"></i>Xóa
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        @if($reviews->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Khách hàng</th>
                            <th>Sản phẩm</th>
                            <th>Đánh giá</th>
                            <th>Nhận xét</th>
                            <th>Hình ảnh</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                        <tr>
                            <td>
                                <input type="checkbox" class="review-checkbox" value="{{ $review->id }}">
                            </td>
                            <td>
                                <div>{{ $review->user->name }}</div>
                                <small class="text-muted">{{ $review->user->email }}</small>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $review->product->slug) }}" target="_blank">
                                    {{ $review->product->name }}
                                </a>
                            </td>
                            <td>
                                <div class="rating-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                @if($review->comment)
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $review->comment }}">
                                        {{ $review->comment }}
                                    </div>
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td>
                                @if($review->images && count($review->images) > 0)
                                    <div class="d-flex gap-1">
                                        @foreach(array_slice($review->images, 0, 3) as $image)
                                            <a href="{{ $image }}" target="_blank">
                                                <img src="{{ $image }}" alt="Review" 
                                                     class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                            </a>
                                        @endforeach
                                        @if(count($review->images) > 3)
                                            <span class="badge bg-secondary">+{{ count($review->images) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($review->is_approved)
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @endif
                            </td>
                            <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if(!$review->is_approved)
                                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" title="Duyệt">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" title="Từ chối">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" 
                                          class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $reviews->appends(request()->query())->links('custom.admin-pagination') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                <p class="text-muted">Chưa có đánh giá nào</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.review-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Vui lòng chọn ít nhất một đánh giá!');
        return;
    }

    const reviewIds = Array.from(checkboxes).map(cb => cb.value);
    document.getElementById('bulkReviewIds').value = JSON.stringify(reviewIds);
    document.getElementById('bulkAction').value = action;

    let confirmMsg = '';
    switch(action) {
        case 'approve':
            confirmMsg = 'Bạn có chắc muốn duyệt ' + reviewIds.length + ' đánh giá?';
            break;
        case 'reject':
            confirmMsg = 'Bạn có chắc muốn từ chối ' + reviewIds.length + ' đánh giá?';
            break;
        case 'delete':
            confirmMsg = 'Bạn có chắc muốn xóa ' + reviewIds.length + ' đánh giá?';
            break;
    }

    if (confirm(confirmMsg)) {
        document.getElementById('bulkActionForm').submit();
    }
}
</script>
@endpush
