<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    protected $fillable = [
        'name', 'sku', 'description', 'bundle_price', 'normal_total', 'is_active', 'category_id',
    ];

    protected $casts = [
        'bundle_price' => 'decimal:2',
        'normal_total' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(ProductBundleItem::class, 'bundle_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Recalculate normal_total from component variants
     */
    public function recalculateNormalTotal(): void
    {
        $total = $this->items->sum(function ($item) {
            return $item->productVariant->selling_price * $item->quantity;
        });
        $this->update(['normal_total' => $total]);
    }

    public function getDiscountPercentAttribute(): float
    {
        if ($this->normal_total <= 0) return 0;
        return round((($this->normal_total - $this->bundle_price) / $this->normal_total) * 100, 1);
    }
}
