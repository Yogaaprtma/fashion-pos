<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'description'];

    const ADMIN = 'admin';
    const MANAJEMEN = 'manajemen';
    const SUPERVISOR = 'supervisor';
    const KASIR = 'kasir';
    const GUDANG = 'gudang';

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
