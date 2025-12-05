<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // legacy role (optional)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public const ROLES = [
        'admin',
        'manager',
        'staff',
    ];

    /** ---------------------------------------
     *  Relationship: direct permissions
     * --------------------------------------*/
    public function permissions()
    {
        return $this->belongsToMany(
            \App\Models\Permission::class,
            'admin_permission',
            'admin_id',
            'permission_id'
        )->withTimestamps();
    }

    /** ---------------------------------------
     *  Relationship: roles (role groups)
     * --------------------------------------*/
    public function roles()
    {
        return $this->belongsToMany(
            \App\Models\Role::class,
            'admin_role',
            'admin_id',
            'role_id'
        )->withTimestamps();
    }

    /** ---------------------------------------
     *  Check if admin has a permission
     * --------------------------------------*/
    public function hasPermission(string $key): bool
    {
        // 1. Direct permission
        if ($this->permissions->where('key', $key)->isNotEmpty()) {
            return true;
        }

        // 2. Permission via role group
        if ($this->roles()->whereHas('permissions', fn($q) => $q->where('key', $key))->exists()) {
            return true;
        }

        return false;
    }

    /** ---------------------------------------
     *  Check role by key
     * --------------------------------------*/
    public function hasRole(string $roleKey): bool
    {
        // Legacy column support
        if (($this->role ?? null) === $roleKey) {
            return true;
        }

        return $this->roles()->where('key', $roleKey)->exists();
    }

    /** UUID settings */
    protected $keyType = 'string';
    public $incrementing = false;

    /** Auto-load permissions + roles to avoid N+1 */
    protected $with = ['permissions', 'roles'];
}
