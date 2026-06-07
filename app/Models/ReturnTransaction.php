<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTransaction extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'transaction_id', 'return_number', 'total_refund',
        'reason', 'status', 'requested_by', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'total_refund' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
