<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'birth_date',
        'points',
        'total_spent',
        'is_member',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_member' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function pointHistories()
    {
        return $this->hasMany(CustomerPointHistory::class);
    }

    public function getMemberTierAttribute(): string
    {
        $points = $this->points ?? 0;
        if ($points >= 5000) return 'Gold';
        if ($points >= 1000) return 'Silver';
        return 'Bronze';
    }

    public function getMemberTierColorAttribute(): string
    {
        return match ($this->member_tier) {
            'Gold' => '#F59E0B',
            'Silver' => '#94A3B8',
            default => '#CD7C2F',
        };
    }
}
