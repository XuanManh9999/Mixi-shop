<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount_amount',
        'min_order_amount',
        'start_at',
        'end_at',
        'usage_limit',
        'usage_per_user',
        'used_count',
        'is_active',
        'apply_to_category_id',
        'apply_to_product_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship với CouponUser
     */
    public function couponUsers()
    {
        return $this->hasMany(CouponUser::class);
    }

    /**
     * Relationship với Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'apply_to_category_id');
    }

    /**
     * Relationship với Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'apply_to_product_id');
    }

    /**
     * Scope cho coupons active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope cho coupons valid (trong thời gian hiệu lực)
     */
    public function scopeValid($query)
    {
        return $query->where('start_at', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_at')
                          ->orWhere('end_at', '>=', now());
                    });
    }

    /**
     * Scope cho coupons available (còn lượt dùng)
     */
    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });
    }

    /**
     * Kiểm tra coupon còn hiệu lực không
     */
    public function isValid()
    {
        if (!$this->is_active) return false;
        
        $now = now();
        if ($this->start_at && $this->start_at > $now) return false;
        if ($this->end_at && $this->end_at < $now) return false;
        
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        
        return true;
    }

    /**
     * Kiểm tra user đã dùng coupon chưa
     */
    public function isUsedByUser($userId)
    {
        if (!$this->usage_per_user) return false;
        
        $userUsageCount = CouponUser::where('coupon_id', $this->id)
                                   ->where('user_id', $userId)
                                   ->count();
        
        return $userUsageCount >= $this->usage_per_user;
    }

    /**
     * Tính giá trị giảm giá
     */
    public function calculateDiscount($orderAmount)
    {
        if ($this->type === 'percentage') {
            $discount = ($orderAmount * $this->value) / 100;
            
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = $this->max_discount_amount;
            }
        } else {
            // fixed amount
            $discount = $this->value;
        }
        
        return min($discount, $orderAmount);
    }

    /**
     * Get formatted value
     */
    public function getFormattedValueAttribute()
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        } else {
            return number_format($this->value, 0, ',', '.') . '₫';
        }
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        if (!$this->is_active) return 'Vô hiệu';
        if (!$this->isValid()) return 'Hết hạn';
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return 'Hết lượt';
        return 'Hoạt động';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        if (!$this->is_active) return 'secondary';
        if (!$this->isValid()) return 'danger';
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return 'warning';
        return 'success';
    }
}
