<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
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
     * Scope filter theo user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope filter theo khoảng thời gian
     */
    public function scopeBetweenDates($query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('login_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
    }

    /**
     * Scope lấy lần đăng nhập gần nhất
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('login_at', 'desc');
    }
}
