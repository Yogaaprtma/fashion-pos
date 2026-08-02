<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    protected $fillable = [
        'channel_name',
        'api_key',
        'webhook_secret',
        'sync_direction',
        'auto_deduct_stock',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'auto_deduct_stock' => 'boolean',
        'is_active'         => 'boolean',
        'last_synced_at'    => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(ApiSyncLog::class, 'integration_id');
    }
}
