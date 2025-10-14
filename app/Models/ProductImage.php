<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
        'public_id',
        'position',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship với Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope sắp xếp theo position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Lấy full URL của image
     */
    public function getFullImageUrlAttribute()
    {
        // Nếu là URL Cloudinary (https://), trả về trực tiếp
        if (str_starts_with($this->image_url, 'https://')) {
            return $this->image_url;
        }
        
        return asset($this->image_url);
    }
}
