<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_requirement_type',
        'min_requirement_value',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'is_active',
        'description',
        'target_type'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_requirement_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'used_count' => 'integer'
    ];

    public function targets()
    {
        return $this->hasMany(PromotionTarget::class);
    }

    public function isValid()
    {
        $now = now();
        return $this->is_active &&
            $this->start_date <= $now &&
            $this->end_date >= $now &&
            (is_null($this->usage_limit) || $this->used_count < $this->usage_limit);
    }
}
