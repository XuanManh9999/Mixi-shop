<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'user_id',
        'rating',
        'comment',
        'images',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship với Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship với OrderItem
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Relationship với Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope cho reviews đã được duyệt
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope cho reviews theo rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Lấy rating trung bình của sản phẩm
     */
    public static function getAverageRating($productId)
    {
        return static::where('product_id', $productId)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;
    }

    /**
     * Lấy số lượng reviews theo rating
     */
    public static function getRatingCounts($productId)
    {
        return static::where('product_id', $productId)
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();
    }

    /**
     * Format rating stars
     */
    public function getStarsAttribute()
    {
        return str_repeat('⭐', $this->rating);
    }

    /**
     * Kiểm tra user đã đánh giá sản phẩm trong đơn hàng này chưa
     */
    public static function hasReviewed($orderId, $productId, $userId)
    {
        return static::where('order_id', $orderId)
            ->where('product_id', $productId)
            ->where('user_id', $userId)
            ->exists();
    }
}
