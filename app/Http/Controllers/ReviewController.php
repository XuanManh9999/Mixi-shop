<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    /**
     * Hiển thị form đánh giá cho đơn hàng
     */
    public function create(Order $order, Request $request)
    {
        // Kiểm tra user có quyền đánh giá đơn hàng này không
        if (auth()->id() !== $order->user_id) {
            abort(403, 'Bạn không có quyền đánh giá đơn hàng này');
        }

        // Kiểm tra đơn hàng đã được giao chưa
        if ($order->status !== 'delivered') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Chỉ có thể đánh giá đơn hàng đã được giao hàng');
        }

        // Lấy các sản phẩm trong đơn hàng chưa được đánh giá
        $orderItems = $order->orderItems()->with('product')->get();
        $reviewedProductIds = Review::where('order_id', $order->id)
            ->where('user_id', auth()->id())
            ->pluck('product_id')
            ->toArray();

        $orderItems = $orderItems->filter(function ($item) use ($reviewedProductIds) {
            return !in_array($item->product_id, $reviewedProductIds);
        });

        // Nếu có product_id trong query, chỉ hiển thị sản phẩm đó
        $selectedProductId = $request->get('product_id');
        if ($selectedProductId) {
            $orderItems = $orderItems->filter(function ($item) use ($selectedProductId) {
                return $item->product_id == $selectedProductId;
            });
        }

        return view('reviews.create', compact('order', 'orderItems', 'selectedProductId'));
    }

    /**
     * Lưu đánh giá
     */
    public function store(Request $request, Order $order)
    {
        // Kiểm tra quyền
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Chỉ có thể đánh giá đơn hàng đã được giao hàng');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_item_id' => 'nullable|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:10000', // Tăng lên để chứa HTML content
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm',
            'rating.required' => 'Vui lòng chọn số sao đánh giá',
            'rating.min' => 'Số sao phải từ 1 đến 5',
            'rating.max' => 'Số sao phải từ 1 đến 5',
        ]);

        // Kiểm tra đã đánh giá chưa
        if (Review::hasReviewed($order->id, $validated['product_id'], auth()->id())) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi');
        }

        // Kiểm tra sản phẩm có trong đơn hàng không
        $orderItem = null;
        if (!empty($validated['order_item_id'])) {
            $orderItem = $order->orderItems()
                ->where('id', $validated['order_item_id'])
                ->where('product_id', $validated['product_id'])
                ->first();
        }
        
        if (!$orderItem) {
            $orderItem = $order->orderItems()
                ->where('product_id', $validated['product_id'])
                ->first();
        }

        if (!$orderItem) {
            return back()->with('error', 'Sản phẩm không có trong đơn hàng này');
        }

        // Extract base64 image URLs from HTML comment (nếu có)
        $imageUrls = [];
        if (!empty($validated['comment'])) {
            // Extract base64 image data URLs from HTML
            preg_match_all('/<img[^>]+src=["\'](data:image\/[^"\']+)["\'][^>]*>/i', $validated['comment'], $matches);
            if (!empty($matches[1])) {
                $imageUrls = $matches[1];
            }
        }

        // Tạo review (HTML content với base64 images được lưu trực tiếp trong comment)
        $review = Review::create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null, // Lưu HTML với base64 images
            'images' => !empty($imageUrls) ? $imageUrls : null, // Lưu base64 URLs để tương thích
            'is_approved' => true, // Tự động duyệt (có thể đổi thành false nếu cần admin duyệt)
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Đánh giá của bạn đã được gửi thành công!');
    }

    /**
     * Hiển thị đánh giá của sản phẩm
     */
    public function index(Product $product, Request $request)
    {
        $query = $product->approvedReviews()->with('user');

        // Lọc theo rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'latest'); // latest, oldest, highest, lowest
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $reviews = $query->paginate(10)->withQueryString();

        // Thống kê rating
        $ratingCounts = Review::getRatingCounts($product->id);
        $averageRating = Review::getAverageRating($product->id);

        return view('reviews.index', compact('product', 'reviews', 'ratingCounts', 'averageRating'));
    }

    /**
     * Xem chi tiết đánh giá
     */
    public function show(Review $review)
    {
        // Chỉ hiển thị đánh giá đã được duyệt
        if (!$review->is_approved) {
            abort(404, 'Đánh giá này chưa được duyệt');
        }
        
        $review->load(['user', 'product', 'order']);
        return view('reviews.show', compact('review'));
    }

    /**
     * Xóa đánh giá (chỉ user tạo ra)
     */
    public function destroy(Review $review)
    {
        if (auth()->id() !== $review->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        // Images được lưu dưới dạng base64 trong database, không cần xóa từ Cloudinary
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá');
    }
}
