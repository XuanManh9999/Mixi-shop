<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'compare_at_price',
        'stock_qty',
        'is_active',
        'thumbnail_url',
        'thumbnail_public_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship với Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship với OrderItems
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship với ProductImages
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Scope cho products active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope cho products có stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_qty', '>', 0);
    }

    /**
     * Scope tìm kiếm
     */
    public function scopeSearch($query, $term)
    {
        // Nhóm điều kiện tìm kiếm để không phá vỡ các where trước đó (active, inStock, byCategory, ...)
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('slug', 'like', "%{$term}%");
        });
    }

    /**
     * Scope filter theo category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Lấy URL của product
     */
    public function getUrlAttribute()
    {
        return route('products.show', $this->slug);
    }

    /**
     * Lấy main image
     */
    public function getMainImageAttribute()
    {
        if ($this->thumbnail_url) {
            // Nếu là URL đầy đủ (https:// hoặc http://), trả về trực tiếp (S3, Cloudinary, etc.)
            if (str_starts_with($this->thumbnail_url, 'https://') || str_starts_with($this->thumbnail_url, 'http://')) {
                return $this->thumbnail_url;
            }
            // Nếu thumbnail_url đã chứa 'storage/', sử dụng asset() trực tiếp
            if (str_starts_with($this->thumbnail_url, 'storage/')) {
                return asset($this->thumbnail_url);
            }
            // Nếu không, thêm storage/ vào đầu
            return asset('storage/' . $this->thumbnail_url);
        }
        return asset('images/no-image.svg');
    }

    /**
     * Kiểm tra có giảm giá không
     */
    public function getIsOnSaleAttribute()
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }

    /**
     * Tính % giảm giá
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->is_on_sale) {
            return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
        }
        return 0;
    }

    /**
     * Kiểm tra còn hàng
     */
    public function getInStockAttribute()
    {
        return $this->stock_qty > 0;
    }

    /**
     * Format price
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . '₫';
    }

    /**
     * Format compare price
     */
    public function getFormattedComparePriceAttribute()
    {
        if ($this->compare_at_price) {
            return number_format($this->compare_at_price, 0, ',', '.') . '₫';
        }
        return null;
    }
}