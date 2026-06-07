<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'size', 'color', 'color_hex',
        'sku_variant', 'barcode_variant', 'buy_price', 'sell_price', 'stock_qty', 'is_active',
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function getProductNameAttribute(): string
    {
        return $this->product?->name ?? 'Unknown Product';
    }

    public function getEffectiveBuyPriceAttribute(): float
    {
        return $this->buy_price ?? $this->product->buy_price;
    }

    public function getEffectiveSellPriceAttribute(): float
    {
        return $this->sell_price ?? $this->product->sell_price;
    }

    public function getVariantLabelAttribute(): string
    {
        $parts = array_filter([$this->size, $this->color]);
        return implode(' / ', $parts) ?: '-';
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= ($this->product->min_stock ?? 5);
    }
}
