<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Coupon::query();

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('code', 'like', "%{$search}%");
        }

        // Lọc theo type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
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

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['code', 'value', 'start_at', 'end_at', 'used_count', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 10);
        $coupons = $query->paginate($perPage)->withQueryString();

        // Thống kê
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', 1)->count(),
            'inactive' => Coupon::where('is_active', 0)->count(),
            'expired' => Coupon::where('end_at', '<', now())->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        $products = Product::active()->get();
        return view('admin.coupons.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'required|integer|min:1',
        ], [
            'code.required' => 'Vui lòng nhập mã coupon',
            'code.unique' => 'Mã coupon đã tồn tại',
            'type.required' => 'Vui lòng chọn loại giảm giá',
            'value.required' => 'Vui lòng nhập giá trị giảm',
            'start_at.required' => 'Vui lòng chọn ngày bắt đầu',
            'end_at.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'usage_per_user.required' => 'Vui lòng nhập giới hạn mỗi user',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'max_discount_amount' => $request->max_discount_amount,
            'min_order_amount' => $request->min_order_amount,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'usage_limit' => $request->usage_limit,
            'usage_per_user' => $request->usage_per_user,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'apply_to_category_id' => $request->apply_to_category_id,
            'apply_to_product_id' => $request->apply_to_product_id,
            'used_count' => 0,
        ]);

        return redirect()->route('admin.coupons.index')
                        ->with('success', 'Tạo mã giảm giá thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load('category', 'product', 'couponUsers.user');
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $categories = Category::active()->ordered()->get();
        $products = Product::active()->get();
        return view('admin.coupons.edit', compact('coupon', 'categories', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $coupon->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'max_discount_amount' => $request->max_discount_amount,
            'min_order_amount' => $request->min_order_amount,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'usage_limit' => $request->usage_limit,
            'usage_per_user' => $request->usage_per_user,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'apply_to_category_id' => $request->apply_to_category_id,
            'apply_to_product_id' => $request->apply_to_product_id,
        ]);

        return redirect()->route('admin.coupons.index')
                        ->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')
                        ->with('success', 'Xóa mã giảm giá thành công!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Coupon $coupon)
    {
        $coupon->update(['is_active' => $coupon->is_active ? 0 : 1]);
        
        $message = $coupon->is_active ? 'Đã kích hoạt mã giảm giá!' : 'Đã vô hiệu hóa mã giảm giá!';
        return back()->with('success', $message);
    }
}