<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDebtPayment extends Model
{
    protected $fillable = ['customer_id', 'transaction_id', 'payment_method_id', 'amount', 'payment_date', 'notes', 'received_by'];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
