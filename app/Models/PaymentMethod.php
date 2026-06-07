<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'type', 'icon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function transactionPayments()
    {
        return $this->hasMany(TransactionPayment::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'cash' => '💵',
            'debit' => '💳',
            'credit' => '💳',
            'ewallet' => '📱',
            'qris' => '📱',
            'transfer' => '🏦',
            default => '💰',
        };
    }
}
