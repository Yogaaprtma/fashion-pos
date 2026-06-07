<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id', 'product_variant_id', 'product_name', 'variant_info',
        'quantity', 'unit_price', 'buy_price', 'discount_amount', 'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'buy_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function getMarginAttribute(): float
    {
        return ($this->unit_price - $this->buy_price) * $this->quantity - $this->discount_amount;
    }

    public function getMarginPercentAttribute(): float
    {
        if ($this->unit_price == 0) return 0;
        return (($this->unit_price - $this->buy_price) / $this->unit_price) * 100;
    }
}
