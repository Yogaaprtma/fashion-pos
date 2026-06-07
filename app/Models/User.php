<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id', 'name', 'email', 'password', 'pin',
        'phone', 'avatar', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'pin', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function cashierSessions()
    {
        return $this->hasMany(CashierSession::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->name, $roles);
    }

    public function canAccess(string $permission): bool
    {
        if (!$this->role) return false;
        return $this->role->permissions()->where('name', $permission)->exists();
    }

    public function verifyPin(string $pin): bool
    {
        return $this->pin && Hash::check($pin, $this->pin);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Bersihkan nama dari tanda kurung seperti "(Owner)" atau "(Kasir)"
        $cleanName = trim(preg_replace('/\(.*?\)/', '', $this->name));
        $name = urlencode($cleanName);
        return "https://ui-avatars.com/api/?name={$name}&background=4F46E5&color=fff&size=128";
    }

    public function activeSession()
    {
        return $this->cashierSessions()->where('status', 'open')->latest()->first();
    }
}
