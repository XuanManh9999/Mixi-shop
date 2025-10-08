@extends('layouts.storefront')

@section('title', $product->name)

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
            <div class="fs-4 text-danger fw-bold mb-3">{{ $product->formatted_price }}
                @if($product->formatted_compare_price)
                    <span class="fs-6 text-muted text-decoration-line-through ms-2">{{ $product->formatted_compare_price }}</span>
                @endif
            </div>
            <p class="mb-3">{{ $product->description }}</p>
            <button class="btn btn-dark js-add-to-cart"
                data-id="{{ $product->id }}"
                data-slug="{{ $product->slug }}"
                data-name="{{ $product->name }}"
                data-price="{{ (float) $product->price }}"
                data-image="{{ $product->main_image }}"
                data-qty="1">
                <i class="fa-solid fa-cart-plus me-2"></i>Thêm vào giỏ
            </button>
        </div>
    </div>

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


