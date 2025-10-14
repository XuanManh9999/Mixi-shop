<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'payment_method',
        'payment_status',
        'subtotal_amount',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'coupon_code',
        'ship_full_name',
        'ship_phone',
        'ship_address',
        'ship_city',
        'ship_district',
        'ship_ward',
        'note',
        'placed_at',
        'confirmed_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'placed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship với Address
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Relationship với OrderItems
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship với Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get latest payment
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Scope filter theo status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope filter theo payment status
     */
    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Scope filter theo user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'preparing' => 'Đang chuẩn bị',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'preparing' => 'primary',
            'shipping' => 'success',
            'delivered' => 'success',
            'cancelled' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'momo' => 'Ví MoMo',
            'vnpay' => 'VNPay'
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Format total amount
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', '.') . '₫';
    }

    /**
     * Tính tổng số items
     */
    public function getTotalItemsAttribute()
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * Kiểm tra đơn hàng VNPay đã hết hạn thanh toán chưa (15 phút)
     */
    public function isVNPayExpired()
    {
        if ($this->payment_method !== 'vnpay' || $this->payment_status !== 'unpaid') {
            return false;
        }
        
        $expireTime = $this->created_at->addMinutes(15);
        return now()->greaterThan($expireTime);
    }

    /**
     * Kiểm tra có thể thanh toán VNPay không
     */
    public function canPayVNPay()
    {
        return $this->payment_method === 'vnpay' 
            && $this->payment_status === 'unpaid' 
            && !$this->isVNPayExpired();
    }

    /**
     * Lấy thời gian còn lại để thanh toán VNPay (tính bằng giây)
     */
    public function getVNPayTimeLeftAttribute()
    {
        if ($this->payment_method !== 'vnpay' || $this->payment_status !== 'unpaid') {
            return 0;
        }
        
        $expireTime = $this->created_at->addMinutes(15);
        $timeLeft = $expireTime->diffInSeconds(now(), false);
        
        return max(0, -$timeLeft); // Trả về 0 nếu đã hết hạn
    }

    /**
     * Kiểm tra đơn hàng pending đã hết hạn xác nhận chưa (15 phút)
     */
    public function isPendingExpired()
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        $expireTime = $this->created_at->addMinutes(15);
        return now()->greaterThan($expireTime);
    }

    /**
     * Kiểm tra đơn hàng confirmed đã hết hạn thanh toán chưa (15 phút)
     */
    public function isConfirmedExpired()
    {
        if ($this->status !== 'confirmed' || $this->payment_status !== 'unpaid' || !$this->confirmed_at) {
            return false;
        }
        
        $expireTime = $this->confirmed_at->addMinutes(15);
        return now()->greaterThan($expireTime);
    }

    /**
     * Kiểm tra đơn hàng có cần tự động hủy không
     */
    public function shouldAutoCancel()
    {
        return $this->isPendingExpired() || $this->isConfirmedExpired();
    }

    /**
     * Lấy thời gian còn lại để xác nhận (tính bằng giây)
     */
    public function getPendingTimeLeftAttribute()
    {
        if ($this->status !== 'pending') {
            return 0;
        }
        
        $expireTime = $this->created_at->addMinutes(15);
        $timeLeft = $expireTime->diffInSeconds(now(), false);
        
        return max(0, -$timeLeft); // Trả về 0 nếu đã hết hạn
    }

    /**
     * Lấy thời gian còn lại để thanh toán sau khi confirmed (tính bằng giây)
     */
    public function getConfirmedTimeLeftAttribute()
    {
        if ($this->status !== 'confirmed' || $this->payment_status !== 'unpaid' || !$this->confirmed_at) {
            return 0;
        }
        
        $expireTime = $this->confirmed_at->addMinutes(15);
        $timeLeft = $expireTime->diffInSeconds(now(), false);
        
        return max(0, -$timeLeft); // Trả về 0 nếu đã hết hạn
    }

    /**
     * Tự động hủy đơn hàng
     */
    public function autoCancel($reason = 'Tự động hủy do hết thời gian')
    {
        $this->update([
            'status' => 'cancelled',
            'note' => ($this->note ? $this->note . ' | ' : '') . $reason
        ]);
        
        return true;
    }

    /**
     * Xác nhận đơn hàng và set confirmed_at
     */
    public function confirmOrder()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);
        
        return true;
    }

}