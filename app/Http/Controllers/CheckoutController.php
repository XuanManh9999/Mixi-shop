<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\CouponUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show()
    {
        return view('storefront.checkout');
    }

    public function place(Request $request)
    {
        // Chấp nhận items là JSON string hoặc mảng inputs
        if (is_string($request->input('items'))) {
            $decoded = json_decode($request->input('items'), true);
            if (is_array($decoded)) {
                $request->merge(['items' => $decoded]);
            }
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,vnpay',
            'shipping_method' => 'required|in:standard,express',
            'coupon_code' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function() use ($validated, $request) {
            $itemsInput = collect($validated['items']);
            $productIds = $itemsInput->pluck('id')->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Tính tạm tính
            $subtotal = 0;
            foreach ($itemsInput as $row) {
                $product = $products[$row['id']];
                $price = (float)$product->price;
                $qty = (int)$row['quantity'];
                $subtotal += $price * $qty;
            }

            // Phí ship
            $shippingFee = $validated['shipping_method'] === 'express' ? 60000 : 30000;

            // Giảm giá theo coupon (nếu có)
            $discount = 0;
            $couponCode = null;
            if (!empty($validated['coupon_code'])) {
                $coupon = Coupon::query()->where('code', strtoupper($validated['coupon_code']))->first();
                if ($coupon && $coupon->isValid()) {
                    $discount = (float)$coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                }
            }

            $total = max(0, $subtotal + $shippingFee - $discount);

            $order = Order::create([
                // Nếu chưa đăng nhập, gán 0 để tránh NULL (schema cột user_id không cho NULL)
                'user_id' => auth()->id() ?? 0,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $total,
                'coupon_code' => $couponCode,
                'ship_full_name' => $validated['customer_name'],
                'ship_phone' => $validated['customer_phone'],
                'ship_address' => $validated['customer_address'],
                // nhận thêm city/district/ward nếu có từ form
                'ship_city' => $request->input('ship_city'),
                'ship_district' => $request->input('ship_district'),
                'ship_ward' => $request->input('ship_ward'),
            ]);

            foreach ($itemsInput as $row) {
                $product = $products[$row['id']];
                $price = (float)$product->price;
                $qty = (int)$row['quantity'];
                $line = $price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit_price' => $price,
                    'quantity' => $qty,
                    'total_price' => $line,
                ]);
            }

            // Nếu có coupon hợp lệ: cập nhật used_count và đánh dấu user đã dùng
            if (!empty($couponCode) && isset($coupon)) {
                // Tăng used_count tổng
                $coupon->increment('used_count');
                // Ghi nhận user dùng (nếu đăng nhập)
                if (auth()->check()) {
                    $cu = CouponUser::firstOrCreate(
                        ['coupon_id' => $coupon->id, 'user_id' => auth()->id()],
                        ['used_times' => 0]
                    );
                    $cu->increment('used_times');
                }
            }

            // Trả về thông tin đơn hàng đã tạo (không redirect đi đâu)
            if ($request->expectsJson()) {
                // AJAX request
                return response()->json([
                    'success' => true,
                    'message' => 'Lên đơn hàng thành công!',
                    'order' => [
                        'id' => $order->id,
                        'total_amount' => $order->formatted_total,
                        'payment_method' => $order->payment_method_label,
                        'status' => $order->status_label,
                        'created_at' => $order->created_at->format('d/m/Y H:i'),
                        'items' => $order->orderItems->map(function($item) {
                            return [
                                'name' => $item->product_name,
                                'quantity' => $item->quantity,
                                'price' => number_format($item->unit_price, 0, ',', '.') . '₫',
                                'total' => number_format($item->total_price, 0, ',', '.') . '₫'
                            ];
                        })
                    ]
                ]);
            }

            // Fallback cho non-AJAX (giữ nguyên redirect cũ)
            return redirect()->route('checkout.thankyou', ['order' => $order->id])
                           ->with('success', 'Lên đơn hàng thành công!')
                           ->with('order_created', true);
        });
    }

    public function thankyou(Order $order)
    {
        return view('storefront.thankyou', compact('order'));
    }

    public function validateCoupon(Request $request)
    {
        $code = strtoupper(trim((string)$request->get('code', '')));
        $amount = (float)$request->get('amount', 0);
        if ($code === '' || $amount <= 0) {
            return response()->json(['ok' => false, 'message' => 'Mã hoặc số tiền không hợp lệ'], 422);
        }
        $coupon = Coupon::query()->where('code', $code)->first();
        if (!$coupon || !$coupon->isValid()) {
            return response()->json(['ok' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn'], 404);
        }
        $discount = $coupon->calculateDiscount($amount);
        return response()->json([
            'ok' => true,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float)$coupon->value,
            'discount' => (float)$discount,
            'message' => 'Áp dụng mã thành công',
        ]);
    }
}


