@extends('layouts.storefront')

@section('title', 'Đánh giá sản phẩm: ' . $product->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></li>
                    <li class="breadcrumb-item active">Đánh giá</li>
                </ol>
            </nav>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">{{ $product->name }}</h3>
                            <div class="d-flex align-items-center mb-2">
                                <div class="rating-display me-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2 fw-bold">{{ number_format($averageRating, 1) }}</span>
                                </div>
                                <span class="text-muted">({{ $reviews->total() }} đánh giá)</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại sản phẩm
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Phân bổ đánh giá</h5>
                </div>
                <div class="card-body">
                    @for($i = 5; $i >= 1; $i--)
                        @php
                            $count = $ratingCounts[$i] ?? 0;
                            $totalReviews = $reviews->total() ?? 0;
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 80px;">
                                <a href="{{ route('reviews.index', [$product, 'rating' => $i]) }}" 
                                   class="text-decoration-none">
                                    {{ $i }} <i class="fas fa-star text-warning"></i>
                                </a>
                            </div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="ms-2" style="width: 60px; text-align: right;">
                                <small class="text-muted">{{ $count }}</small>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Filter and Sort -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reviews.index', $product) }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Lọc theo sao:</label>
                            <select name="rating" class="form-select">
                                <option value="">Tất cả</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                        {{ $i }} sao
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sắp xếp:</label>
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Sao cao nhất</option>
                                <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Sao thấp nhất</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Lọc
                            </button>
                            <a href="{{ route('reviews.index', $product) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Danh sách đánh giá ({{ $reviews->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($reviews->count() > 0)
                        @foreach($reviews as $review)
                            <div class="review-item border-bottom pb-4 mb-4 @if(!$loop->last) border-bottom @endif">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $review->user->name }}</h6>
                                                <div class="rating-display mb-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                                <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>
                                        
                                        @if($review->comment)
                                            <div class="mb-2 review-content">
                                                {!! $review->comment !!}
                                            </div>
                                        @endif

                                        <div class="mt-2">
                                            <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline-primary" style="position: relative; z-index: 10;">
                                                <i class="fas fa-eye me-1"></i>Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        @if($reviews->hasPages())
                            <div class="mt-4">
                                <div class="d-flex flex-column align-items-center">
                                    {{-- Results Info --}}
                                    <div class="mb-3">
                                        <p class="text-muted small mb-0">
                                            Hiển thị {{ $reviews->firstItem() }} đến {{ $reviews->lastItem() }} trong tổng số {{ $reviews->total() }} kết quả
                                        </p>
                                    </div>

                                    {{-- Pagination Navigation --}}
                                    <nav aria-label="Điều hướng phân trang">
                                        <ul class="pagination justify-content-center mb-0">
                                            {{-- Previous Page Link --}}
                                            @if ($reviews->onFirstPage())
                                                <li class="page-item disabled" aria-disabled="true">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-left"></i>
                                                        <span class="d-none d-sm-inline ms-1">Trước</span>
                                                    </span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $reviews->appends(request()->query())->previousPageUrl() }}" rel="prev" title="Trang trước">
                                                        <i class="fas fa-chevron-left"></i>
                                                        <span class="d-none d-sm-inline ms-1">Trước</span>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @php
                                                $currentPage = $reviews->currentPage();
                                                $lastPage = $reviews->lastPage();
                                                $startPage = max(1, $currentPage - 2);
                                                $endPage = min($lastPage, $currentPage + 2);
                                                
                                                // Show first page if not in range
                                                if ($startPage > 1) {
                                                    $startPage = 1;
                                                }
                                                
                                                // Show last page if not in range
                                                if ($endPage < $lastPage) {
                                                    $endPage = min($lastPage, $startPage + 4);
                                                }
                                            @endphp
                                            
                                            @if ($startPage > 1)
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $reviews->appends(request()->query())->url(1) }}" title="Trang 1">1</a>
                                                </li>
                                                @if ($startPage > 2)
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                @endif
                                            @endif
                                            
                                            @for ($page = $startPage; $page <= $endPage; $page++)
                                                @if ($page == $currentPage)
                                                    <li class="page-item active" aria-current="page">
                                                        <span class="page-link">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $reviews->appends(request()->query())->url($page) }}" title="Trang {{ $page }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endfor
                                            
                                            @if ($endPage < $lastPage)
                                                @if ($endPage < $lastPage - 1)
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                @endif
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $reviews->appends(request()->query())->url($lastPage) }}" title="Trang {{ $lastPage }}">{{ $lastPage }}</a>
                                                </li>
                                            @endif

                                            {{-- Next Page Link --}}
                                            @if ($reviews->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $reviews->appends(request()->query())->nextPageUrl() }}" rel="next" title="Trang sau">
                                                        <span class="d-none d-sm-inline me-1">Sau</span>
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="page-item disabled" aria-disabled="true">
                                                    <span class="page-link">
                                                        <span class="d-none d-sm-inline me-1">Sau</span>
                                                        <i class="fas fa-chevron-right"></i>
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.review-content {
    line-height: 1.6;
}
.review-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 10px 0;
}
.review-content p {
    margin-bottom: 10px;
}
.review-content ul, .review-content ol {
    padding-left: 20px;
    margin-bottom: 10px;
}

/* Fix button click area và hover effect - đơn giản hóa */
a.btn-outline-primary {
    cursor: pointer;
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    text-decoration: none;
    position: relative;
    z-index: 10;
    display: inline-block;
}

a.btn-outline-primary:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
    text-decoration: none;
    z-index: 10;
}

/* Đảm bảo không có link chồng lên nhau */
.review-item {
    position: relative;
}

.review-item a {
    position: relative;
    z-index: auto;
}

.review-item a.btn {
    z-index: 10 !important;
}
</style>
@endpush
