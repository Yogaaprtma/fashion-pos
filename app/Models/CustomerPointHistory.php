<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPointHistory extends Model
{
    protected $table = 'customer_point_histories';

    protected $fillable = [
        'customer_id',
        'transaction_id',
        'amount',
        'type',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'earn' => 'success',
            'redeem' => 'warning',
            'adjustment' => 'info',
            default => 'secondary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'earn' => 'Poin Masuk',
            'redeem' => 'Poin Ditukar',
            'adjustment' => 'Penyesuaian',
            default => $this->type,
        };
    }
}
