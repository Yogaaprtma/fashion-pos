<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'opname_number', 'category_id', 'status',
        'initiated_by', 'approved_by', 'started_at', 'completed_at', 'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }
}
