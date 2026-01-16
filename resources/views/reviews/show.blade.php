@extends('layouts.storefront')

@section('title', 'Chi tiết đánh giá')

@section('content')
@push('styles')
<style>
.review-content {
    line-height: 1.8;
}
.review-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 15px 0;
}
.review-content p {
    margin-bottom: 15px;
}
.review-content ul, .review-content ol {
    padding-left: 25px;
    margin-bottom: 15px;
}
.review-content h1, .review-content h2, .review-content h3 {
    margin-top: 20px;
    margin-bottom: 10px;
}

/* Fix button click area và hover effect - đơn giản hóa */
a.btn-outline-primary,
a.btn-outline-secondary {
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

a.btn-outline-secondary:hover {
    background-color: var(--bs-secondary);
    border-color: var(--bs-secondary);
    color: white;
    text-decoration: none;
    z-index: 10;
}
</style>
@endpush
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.show', $review->product->slug) }}">{{ $review->product->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reviews.index', $review->product) }}">Đánh giá</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-star me-2"></i>Chi tiết đánh giá
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; font-size: 1.5rem;">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $review->user->name }}</h5>
                            <div class="rating-display mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 1.2rem;"></i>
                                @endfor
                                <span class="ms-2 fw-bold">{{ $review->rating }}/5</span>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $review->created_at->format('d/m/Y H:i:s') }}
                            </small>
                        </div>
                    </div>

                    @if($review->comment)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Nhận xét:</h6>
                            <div class="review-content">
                                {!! $review->comment !!}
                            </div>
                        </div>
                    @endif

                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Sản phẩm:</strong> 
                                    <a href="{{ route('products.show', $review->product->slug) }}">
                                        {{ $review->product->name }}
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Đơn hàng:</strong> #{{ $review->order_id }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('reviews.index', $review->product) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách đánh giá
                        </a>
                        <a href="{{ route('products.show', $review->product->slug) }}" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Xem sản phẩm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
