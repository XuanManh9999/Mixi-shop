<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'amount',
        'currency',
        'status',
        'vnp_TransactionNo',
        'vnp_BankCode',
        'vnp_CardType',
        'vnp_ResponseCode',
        'vnp_PayDate',
        'vnp_SecureHash',
        'raw_callback',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
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
     * Scope cho payments đã thanh toán
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope cho payments pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope cho payments failed
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', '.') . '₫';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền',
        ];

        return $labels[$this->status] ?? 'Không xác định';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get provider label
     */
    public function getProviderLabelAttribute()
    {
        $providers = [
            'vnpay' => 'VNPay',
            'momo' => 'MoMo',
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản',
        ];

        return $providers[$this->provider] ?? $this->provider;
    }

    /**
     * Check if payment is paid
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid($transactionData = [])
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'raw_callback' => json_encode($transactionData),
        ]);

        // Cập nhật order status
        $this->order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'raw_callback' => $reason ? json_encode(['reason' => $reason]) : null,
        ]);

        // Cập nhật order
        $this->order->update([
            'payment_status' => 'failed',
        ]);
    }
}
