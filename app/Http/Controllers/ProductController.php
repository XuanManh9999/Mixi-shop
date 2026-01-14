<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->active()
            ->inStock()
            ->latest()
            ->paginate(12);
        return view('storefront.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        // Load relationships
        $product->load(['category', 'images', 'approvedReviews.user']);
        
        $related = Product::query()
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        // Lấy reviews và thống kê rating
        $reviews = $product->reviews()->where('is_approved', true)->with('user')->latest()->paginate(5);
        $averageRating = $product->average_rating;
        $ratingCounts = Review::getRatingCounts($product->id);

        return view('storefront.products.show', compact('product', 'related', 'reviews', 'averageRating', 'ratingCounts'));
    }
}
