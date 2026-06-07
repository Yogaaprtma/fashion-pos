<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_variant_id', 'type', 'quantity', 'stock_before', 'stock_after',
        'reference_type', 'reference_id', 'notes', 'user_id',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'in' => 'emerald',
            'out' => 'rose',
            'return' => 'amber',
            'adjustment' => 'sky',
            'opname' => 'purple',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'in' => 'Stok Masuk',
            'out' => 'Stok Keluar',
            'return' => 'Retur',
            'adjustment' => 'Penyesuaian',
            'opname' => 'Stock Opname',
            default => $this->type,
        };
    }
}
