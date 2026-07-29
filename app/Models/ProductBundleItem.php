<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBundleItem extends Model
{
    protected $fillable = ['bundle_id', 'product_variant_id', 'quantity'];

    public function bundle()
    {
        return $this->belongsTo(ProductBundle::class, 'bundle_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
