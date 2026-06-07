<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'barcode',
        'description', 'brand', 'buy_price', 'sell_price', 'min_stock', 'is_active',
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('stock_qty');
    }

    public function isLowStock(): bool
    {
        return $this->getTotalStockAttribute() <= $this->min_stock;
    }

    public function getImageUrlAttribute(): string
    {
        $primary = $this->images()->where('is_primary', true)->first();
        if ($primary) {
            return asset('storage/' . $primary->image_path);
        }
        return asset('images/no-product.png');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(5);
            }
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(3)) . '-' . date('ymd') . '-' . rand(100, 999);
            }
        });
    }
}
