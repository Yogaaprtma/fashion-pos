<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiSyncLog extends Model
{
    protected $fillable = [
        'integration_id',
        'event_type',
        'payload',
        'status',
        'response_message',
    ];

    public function integration()
    {
        return $this->belongsTo(ApiIntegration::class, 'integration_id');
    }
}
