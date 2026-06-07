<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierSession extends Model
{
    protected $fillable = [
        'user_id', 'opened_at', 'closed_at', 'opening_balance',
        'closing_balance', 'expected_balance', 'difference',
        'total_transactions', 'total_sales', 'notes', 'status',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
