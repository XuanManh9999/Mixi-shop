<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string)$request->get('q', ''));
        $categorySlug = $request->get('category');

        $categories = Category::query()
            ->active()
            ->ordered()
            ->get(['id','name','slug']);

        $productsQuery = Product::query()
            ->active()
            ->inStock()
            ->with('category')
            ->latest('created_at');

        if ($search !== '') {
            $productsQuery->search($search);
        }

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $productsQuery->byCategory($category->id);
            }
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        return view('storefront.home', compact('categories','products','search','categorySlug'));
    }

    public function suggest(Request $request)
    {
        $term = trim((string)$request->get('q', ''));
        if ($term === '') {
            return response()->json([]);
        }

        $products = Product::query()
            ->active()->inStock()
            ->search($term)
            ->with('category')
            ->limit(8)
            ->get();

        return response()->json($products->map(function ($p) {
            return [
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->formatted_price,
                'image' => $p->main_image,
                'category' => optional($p->category)->name,
                'url' => route('products.show', $p->slug),
            ];
        }));
    }
}


