<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,vnpay',
            'shipping_method' => 'required|in:standard,express',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function() use ($validated) {
            $itemsInput = collect($validated['items']);
            $productIds = $itemsInput->pluck('id')->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
                'shipping_method' => $validated['shipping_method'],
                'shipping_address' => $validated['customer_address'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($itemsInput as $row) {
                $product = $products[$row['id']];
                $price = (float)$product->price;
                $qty = (int)$row['quantity'];
                $line = $price * $qty;
                $total += $line;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $price,
                    'quantity' => $qty,
                    'line_total' => $line,
                ]);
            }

            $order->update(['total_amount' => $total]);

            // Trả về trang cảm ơn; nếu vnpay sẽ redirect đến cổng thanh toán ở bước sau
            return redirect()->route('checkout.thankyou', ['order' => $order->id]);
        });
    }

    public function thankyou(Order $order)
    {
        return view('storefront.thankyou', compact('order'));
    }
}


