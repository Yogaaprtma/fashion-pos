<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    protected $fillable = ['transaction_id', 'payment_method_id', 'amount', 'reference_number'];

    protected $casts = ['amount' => 'decimal:2'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
