<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    protected $fillable = ['grn_id', 'purchase_order_item_id', 'product_variant_id', 'quantity_received', 'condition_notes'];

    public function grn()
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'grn_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
