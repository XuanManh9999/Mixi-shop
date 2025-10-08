<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
        $related = Product::query()
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        return view('storefront.products.show', compact('product','related'));
    }
}
