<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $fillable = ['return_id', 'transaction_item_id', 'product_variant_id', 'quantity', 'refund_amount'];

    protected $casts = ['refund_amount' => 'decimal:2'];

    public function returnTransaction()
    {
        return $this->belongsTo(ReturnTransaction::class, 'return_id');
    }

    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
