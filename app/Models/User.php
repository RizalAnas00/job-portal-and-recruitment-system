<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
        
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function jobSeeker()
    {
        return $this->hasOne(JobSeeker::class, 'user_id');
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permissionName)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        if (!$this->role) {
            return false;
        }

        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        return $this->role->permissions()
            ->whereIn('name', $permissions)
            ->exists();
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        if (!$this->role) {
            return false;
        }

        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $userPermissions = $this->role->permissions()->pluck('name')->toArray();
        return count(array_intersect($permissions, $userPermissions)) === count($permissions);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(Array $roleNames)
    {
        if (is_string($roleNames)) {
            $roleNames = [$roleNames];
        }

        return $this->role && in_array($this->role->name, $roleNames);
    }

    /**
     * Get user's role name
     */
    public function getRoleName()
    {
        return $this->role ? $this->role->name : null;
    }

    /**
     * Get user's permissions
     */
    public function getPermissions()
    {
        if (!$this->role) {
            return collect();
        }

        return $this->role->permissions;
    }

    /**
     * Assign role to user
     * If user already has a role, it will be updated/replaced
     */
    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            throw new \Exception("Role '{$roleName}' not found");
        }

        // Update the role_id regardless if user already has a role or not
        $this->update(['role_id' => $role->id]);
        
        // Refresh the model to load the new relationship
        $this->load('role');
        
        return $this;
    }

    /**
     * Revoke role from user
     */
    public function revokeRole($roleName)
    {
        if ($this->hasRole($roleName)) {
            $this->update(['role_id' => null]);
            // Refresh the model to reflect the change
            $this->load('role');
        }
        
        return $this;
    }

    /**
     * Remove all roles from user
     */
    public function revokeAllRoles()
    {
        $this->update(['role_id' => null]);
        return $this;
    }
}
