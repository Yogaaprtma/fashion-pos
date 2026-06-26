<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionTarget extends Model
{
    protected $fillable = ['promotion_id', 'target_id'];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    // Target can refer to Product or Category depending on target_type on parent promotion
    public function product()
    {
        return $this->belongsTo(Product::class, 'target_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'target_id');
    }
}
