<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'group',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')->withTimestamps();
    }

    public static function getAllPermissions()
    {
        return Permission::all();
    }

    /**
     * Check if permission is assigned to a specific role
     */
    public function isAssignedToRole($roleName)
    {
        return $this->roles()
            ->where('name', $roleName)
            ->exists();
    }

    /**
     * Get users who have this permission through their roles
     */
    public function users()
    {
        return User::whereHas('role.permissions', function ($query) {
            $query->where('permissions.id', $this->id);
        });
    }

    /**
     * Scope: Filter by group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope: Filter by name pattern
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}
