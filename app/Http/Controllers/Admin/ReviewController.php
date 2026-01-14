<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Danh sách reviews
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'order']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái duyệt
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Lọc theo rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->get('rating'));
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'rating', 'is_approved'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 20);
        $reviews = $query->paginate($perPage)->withQueryString();

        // Thống kê
        $stats = [
            'total' => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending' => Review::where('is_approved', false)->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Duyệt review
     */
    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Đã duyệt đánh giá!');
    }

    /**
     * Từ chối review
     */
    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);
        return back()->with('success', 'Đã từ chối đánh giá!');
    }

    /**
     * Xóa review
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Đã xóa đánh giá!');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $action = $request->get('action');
        $reviewIdsInput = $request->get('review_ids', '[]');
        
        // Decode JSON nếu là string
        if (is_string($reviewIdsInput)) {
            $reviewIds = json_decode($reviewIdsInput, true) ?? [];
        } else {
            $reviewIds = $reviewIdsInput;
        }

        if (empty($reviewIds) || !is_array($reviewIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một đánh giá!');
        }

        switch ($action) {
            case 'approve':
                Review::whereIn('id', $reviewIds)->update(['is_approved' => true]);
                return back()->with('success', 'Đã duyệt ' . count($reviewIds) . ' đánh giá!');
                
            case 'reject':
                Review::whereIn('id', $reviewIds)->update(['is_approved' => false]);
                return back()->with('success', 'Đã từ chối ' . count($reviewIds) . ' đánh giá!');
                
            case 'delete':
                Review::whereIn('id', $reviewIds)->delete();
                return back()->with('success', 'Đã xóa ' . count($reviewIds) . ' đánh giá!');
                
            default:
                return back()->with('error', 'Hành động không hợp lệ!');
        }
    }
}
