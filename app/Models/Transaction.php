<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'cashier_session_id',
        'invoice_number',
        'customer_id',
        'subtotal',
        'discount_amount',
        'discount_percent',
        'tax_amount',
        'tax_percent',
        'grand_total',
        'paid_amount',
        'change_amount',
        'status',
        'voided_by',
        'voided_at',
        'void_reason',
        'notes',
        'promotion_id',
        'promotion_discount',
        'points_earned',
        'points_used',
        'point_discount',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'voided_at' => 'datetime',
        'promotion_discount' => 'decimal:2',
        'point_discount' => 'decimal:2',
        'points_earned' => 'integer',
        'points_used' => 'integer',
    ];

    public function cashierSession()
    {
        return $this->belongsTo(CashierSession::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(TransactionPayment::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnTransaction::class);
    }

    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'emerald',
            'voided' => 'rose',
            'held' => 'amber',
            'partial_return' => 'sky',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Selesai',
            'voided' => 'Dibatalkan',
            'held' => 'Ditahan',
            'partial_return' => 'Retur Sebagian',
            default => $this->status,
        };
    }

    public function getGrossProfit(): float
    {
        return $this->items->sum(function ($item) {
            return ($item->unit_price - $item->buy_price) * $item->quantity - $item->discount_amount;
        });
    }
}
