<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNote extends Model
{
    protected $fillable = ['grn_number', 'purchase_order_id', 'received_by', 'notes', 'received_at'];

    protected $casts = ['received_at' => 'datetime'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class, 'grn_id');
    }
}
