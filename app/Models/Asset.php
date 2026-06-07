<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_category_id', 'name', 'asset_code', 'purchase_date',
        'purchase_price', 'current_value', 'depreciation_rate', 'condition', 'location', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function getConditionColorAttribute(): string
    {
        return match($this->condition) {
            'good' => 'emerald',
            'fair' => 'amber',
            'poor' => 'rose',
            'disposed' => 'gray',
            default => 'gray',
        };
    }
}
