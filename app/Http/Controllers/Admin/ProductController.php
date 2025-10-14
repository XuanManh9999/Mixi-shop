<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Services\CloudinaryService;
use App\Services\SimpleCloudinaryService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $cloudinaryService;

    public function __construct(SimpleCloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Lọc theo category
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', 1);
            } elseif ($status === 'inactive') {
                $query->where('is_active', 0);
            }
        }

        // Lọc theo stock
        if ($request->filled('stock')) {
            $stock = $request->get('stock');
            if ($stock === 'in_stock') {
                $query->where('stock_qty', '>', 0);
            } elseif ($stock === 'out_of_stock') {
                $query->where('stock_qty', 0);
            }
        }

        // Lọc theo giá
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->get('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->get('price_max'));
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['name', 'price', 'stock_qty', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage)->withQueryString();

        // Categories cho filter
        $categories = Category::active()->ordered()->get();

        // Thống kê
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', 1)->count(),
            'inactive' => Product::where('is_active', 0)->count(),
            'out_of_stock' => Product::where('stock_qty', 0)->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'price.required' => 'Vui lòng nhập giá sản phẩm',
            'stock_qty.required' => 'Vui lòng nhập số lượng',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'stock_qty' => $request->stock_qty,
            'sku' => $request->sku ?: 'MIXI-' . strtoupper(Str::random(6)),
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        // Upload thumbnail to Cloudinary - Using Facade
        if ($request->hasFile('thumbnail')) {
            try {
                $uploadResult = Cloudinary::upload($request->file('thumbnail')->getRealPath(), [
                    'folder' => 'mixishop/products/' . $data['slug'],
                    'public_id' => $data['slug'] . '_thumbnail_' . time(),
                    'transformation' => [
                        'width' => 400,
                        'height' => 400,
                        'crop' => 'fill'
                    ]
                ]);
                
                $data['thumbnail_url'] = $uploadResult->getSecurePath();
                $data['thumbnail_public_id'] = $uploadResult->getPublicId();
            } catch (\Exception $e) {
                \Log::error('Cloudinary upload error: ' . $e->getMessage());
            }
        }

        $product = Product::create($data);

        // Upload additional images to Cloudinary
        if ($request->hasFile('images')) {
            $position = 1;
            foreach ($request->file('images') as $image) {
                $uploadResult = $this->cloudinaryService->uploadProductImage(
                    $image, 
                    $product->slug, 
                    'gallery'
                );
                
                if ($uploadResult) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $uploadResult['secure_url'],
                        'public_id' => $uploadResult['public_id'],
                        'position' => $position++,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
                        ->with('success', 'Tạo sản phẩm thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'images', 'orderItems');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->ordered()->get();
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'stock_qty' => $request->stock_qty,
            'sku' => $request->sku ?: 'MIXI-' . strtoupper(Str::random(6)),
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        // Upload thumbnail mới to Cloudinary - Direct approach
        if ($request->hasFile('thumbnail')) {
            // Xóa thumbnail cũ từ Cloudinary
            if ($product->thumbnail_public_id) {
                try {
                    Cloudinary::destroy($product->thumbnail_public_id);
                } catch (\Exception $e) {
                    \Log::error('Cloudinary delete error: ' . $e->getMessage());
                }
            }

            try {
                $uploadResult = Cloudinary::upload($request->file('thumbnail')->getRealPath(), [
                    'folder' => 'mixishop/products/' . $data['slug'],
                    'public_id' => $data['slug'] . '_thumbnail_' . time(),
                    'transformation' => [
                        'width' => 400,
                        'height' => 400,
                        'crop' => 'fill'
                    ]
                ]);
                
                $data['thumbnail_url'] = $uploadResult->getSecurePath();
                $data['thumbnail_public_id'] = $uploadResult->getPublicId();
            } catch (\Exception $e) {
                \Log::error('Cloudinary upload error: ' . $e->getMessage());
            }
        }

        $product->update($data);

        // Upload images mới to Cloudinary
        if ($request->hasFile('images')) {
            // Xóa images cũ từ Cloudinary
            foreach ($product->images as $oldImage) {
                if ($oldImage->public_id) {
                    $this->cloudinaryService->delete($oldImage->public_id);
                }
                $oldImage->delete();
            }

            $position = 1;
            foreach ($request->file('images') as $image) {
                $uploadResult = $this->cloudinaryService->uploadProductImage(
                    $image, 
                    $product->slug, 
                    'gallery'
                );
                
                if ($uploadResult) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $uploadResult['secure_url'],
                        'public_id' => $uploadResult['public_id'],
                        'position' => $position++,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
                        ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Kiểm tra có trong đơn hàng không
        if ($product->orderItems()->count() > 0) {
            return back()->with('error', 'Không thể xóa sản phẩm đã có trong đơn hàng!');
        }

        // Xóa thumbnail
        if ($product->thumbnail_url && Storage::exists('public/' . str_replace('storage/', '', $product->thumbnail_url))) {
            Storage::delete('public/' . str_replace('storage/', '', $product->thumbnail_url));
        }

        // Xóa images
        foreach ($product->images as $image) {
            if (Storage::exists('public/' . str_replace('storage/', '', $image->image_url))) {
                Storage::delete('public/' . str_replace('storage/', '', $image->image_url));
            }
            $image->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                        ->with('success', 'Xóa sản phẩm thành công!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => $product->is_active ? 0 : 1]);
        
        $message = $product->is_active ? 'Đã kích hoạt sản phẩm!' : 'Đã vô hiệu hóa sản phẩm!';
        return back()->with('success', $message);
    }
}