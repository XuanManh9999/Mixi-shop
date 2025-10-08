<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category, Request $request)
    {
        $products = Product::query()
            ->active()->inStock()
            ->byCategory($category->id)
            ->latest()
            ->paginate(12);

        return view('storefront.categories.show', compact('category','products'));
    }
}
