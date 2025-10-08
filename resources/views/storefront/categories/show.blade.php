@extends('layouts.storefront')

@section('title', $category->name)

@section('content')
<div class="container">
    <h1 class="h4 mb-3">{{ $category->name }}</h1>
    <div class="row g-3">
        @foreach($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <img src="{{ $product->main_image }}" class="product-img" alt="{{ $product->name }}">
                    <div class="p-3">
                        <div class="fw-semibold mb-1 text-truncate">{{ $product->name }}</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="price">{{ $product->formatted_price }}</span>
                            @if($product->formatted_compare_price)
                                <span class="compare">{{ $product->formatted_compare_price }}</span>
                            @endif
                        </div>
                        <a href="{{ route('products.show', $product->slug) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection


