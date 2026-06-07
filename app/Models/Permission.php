<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'module', 'display_name'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
