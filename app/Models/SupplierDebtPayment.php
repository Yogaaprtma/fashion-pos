<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierDebtPayment extends Model
{
    protected $fillable = ['purchase_order_id', 'payment_method_id', 'amount', 'payment_date', 'notes', 'paid_by'];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
