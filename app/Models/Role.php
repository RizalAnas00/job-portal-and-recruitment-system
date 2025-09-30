<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')->withTimestamps();
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission($permissionName)
    {
        return $this->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    /**
     * Check if role has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        return $this->permissions()
            ->whereIn('name', $permissions)
            ->exists();
    }

    /**
     * Check if role has all of the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $rolePermissions = $this->permissions()->pluck('name')->toArray();
        return count(array_intersect($permissions, $rolePermissions)) === count($permissions);
    }

    /**
     * Give permission to role
     */
    public function givePermissionTo($permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            throw new \Exception("Permission '{$permissionName}' not found");
        }

        // Check if role already has this permission
        if (!$this->hasPermission($permissionName)) {
            $this->permissions()->attach($permission->id);
        }
        
        return $this;
    }

    /**
     * Give multiple permissions to role
     */
    public function giveMultiplePermissions($permissions)
    {
        foreach ($permissions as $permissionName) {
            $this->givePermissionTo($permissionName);
        }
        
        return $this;
    }

    /**
     * Revoke permission from role
     */
    public function revokePermissionTo($permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        
        if ($permission && $this->hasPermission($permissionName)) {
            $this->permissions()->detach($permission->id);
        }
        
        return $this;
    }

    /**
     * Revoke multiple permissions from role
     */
    public function revokeMultiplePermissions($permissions)
    {
        foreach ($permissions as $permissionName) {
            $this->revokePermissionTo($permissionName);
        }
        
        return $this;
    }

    /**
     * Sync permissions (replace all permissions with new ones)
     */
    public function syncPermissions($permissions)
    {
        $permissionIds = [];
        
        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permissionIds[] = $permission->id;
            }
        }
        
        $this->permissions()->sync($permissionIds);
        return $this;
    }

    /**
     * Remove all permissions from role
     */
    public function revokeAllPermissions()
    {
        $this->permissions()->detach();
        return $this;
    }
}
