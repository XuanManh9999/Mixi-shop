@extends('layouts.storefront')

@section('title', $product->name)

@push('styles')
<style>
/* Đảm bảo card review không có link chồng lên nhau */
.reviews-section .card {
    position: relative;
    overflow: visible;
}

.reviews-section .card-body {
    position: relative;
}

/* Đảm bảo button có z-index cao và không bị che */
.reviews-section .card a.btn {
    position: relative;
    z-index: 100 !important;
    pointer-events: auto !important;
    isolation: isolate;
}

/* Ngăn chặn mọi link khác trong card review */
.reviews-section .card a:not(.btn) {
    position: relative;
    z-index: 1;
}

/* Đảm bảo không có overlay link */
.reviews-section .card::before,
.reviews-section .card::after {
    display: none !important;
}

/* Đảm bảo button hoạt động đúng */
.reviews-section a.btn-outline-primary {
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}
</style>
@endpush

@section('content')
<div class="container">
    <nav class="small mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            @if($product->category)
            <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="ratio ratio-4x3 rounded-3 border overflow-hidden mb-3">
                <img src="{{ $product->main_image }}" class="w-100 h-100" style="object-fit:cover" alt="{{ $product->name }}">
            </div>
            @if($product->images && $product->images->count())
                <div class="d-flex gap-2">
                    @foreach($product->images->take(6) as $img)
                        <img src="{{ asset($img->image_url) }}" class="rounded border" style="width:70px;height:70px;object-fit:cover" alt="{{ $product->name }}">
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-lg-6">
            <h1 class="h3">{{ $product->name }}</h1>
            <div class="mb-2 text-muted">{{ optional($product->category)->name }}</div>
            
            <!-- Rating Display -->
            @php
                $avgRating = $product->average_rating ?? 0;
                $reviewsCount = $product->reviews_count ?? 0;
            @endphp
            @if($reviewsCount > 0)
                <div class="mb-2">
                    <div class="d-flex align-items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($avgRating) ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <span class="ms-2 fw-bold">{{ number_format($avgRating, 1) }}</span>
                        <a href="{{ route('reviews.index', $product) }}" class="ms-2 text-decoration-none" style="position: relative; z-index: 5;">
                            <small class="text-muted">({{ $reviewsCount }} đánh giá)</small>
                        </a>
                    </div>
                </div>
            @else
                <div class="mb-2">
                    <div class="d-flex align-items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-muted"></i>
                        @endfor
                        <span class="ms-2 text-muted">Chưa có đánh giá</span>
                    </div>
                </div>
            @endif
            
            <div class="fs-4 text-danger fw-bold mb-3">{{ $product->formatted_price }}
                @if($product->formatted_compare_price)
                    <span class="fs-6 text-muted text-decoration-line-through ms-2">{{ $product->formatted_compare_price }}</span>
                @endif
            </div>
            <p class="mb-3">{{ $product->description }}</p>
            <button
  class="btn btn-dark js-add-to-cart position-relative"
  type="button"
  style="z-index:3"
  data-id="{{ (string) $product->id }}"     {{-- để chắc là string --}}
  data-slug="{{ $product->slug }}"
  data-name="{{ $product->name }}"
  data-price="{{ is_numeric($product->price ?? null) ? $product->price : ($product->price_raw ?? '') }}"  {{-- GIÁ thô --}}
  data-image="{{ $product->main_image }}"
  data-qty="1">
  <i class="fa-solid fa-cart-plus me-2"></i>Thêm vào giỏ
</button>

        </div>
    </div>

    <!-- Reviews Section -->
    @php
        $topReviews = $product->approvedReviews()->with('user')->latest()->take(5)->get();
    @endphp
    @if($topReviews->count() > 0)
        <hr class="my-4">
        <div class="reviews-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Đánh giá sản phẩm</h2>
                <a href="{{ route('reviews.index', $product) }}" class="btn btn-outline-primary btn-sm" style="position: relative; z-index: 100;">
                    Xem tất cả đánh giá <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="row g-3">
            @foreach($topReviews as $review)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-2">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $review->user->name }}</h6>
                                    <div class="rating-display mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 0.8rem;"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                            @if($review->comment)
                                <div class="mb-2 small review-content">
                                    @php
                                        $textContent = strip_tags($review->comment);
                                        $preview = \Illuminate\Support\Str::limit($textContent, 150);
                                    @endphp
                                    {{ $preview }}
                                </div>
                            @endif
                            <div class="mt-2" style="position: relative; z-index: 100;">
                                <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline-primary" style="position: relative; z-index: 100; pointer-events: auto;">
                                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    @else
        <hr class="my-4">
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-star text-muted" style="font-size: 3rem;"></i>
            </div>
            <h3 class="h5 text-muted mb-2">Chưa có đánh giá nào</h3>
            <p class="text-muted mb-0">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
        </div>
    @endif

    @if($related->count())
        <hr class="my-4">
        <h2 class="h5 mb-3">Món tương tự</h2>
        <div class="row g-3">
            @foreach($related as $p)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card h-100">
                        <img src="{{ $p->main_image }}" class="product-img" alt="{{ $p->name }}">
                        <div class="p-3">
                            <div class="fw-semibold mb-1 text-truncate">{{ $p->name }}</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="price">{{ $p->formatted_price }}</span>
                            </div>
                            <a href="{{ route('products.show', $p->slug) }}" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection


