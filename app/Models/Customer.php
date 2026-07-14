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
}
