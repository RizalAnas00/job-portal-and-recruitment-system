<?php

namespace App\Models\Role\Repositories;

use App\Models\Role as RoleModel;
use App\Models\Role\Contracts\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleRepository implements RoleRepositoryInterface
{
    public function createRole(array $data): RoleModel
    {
        $newRole = RoleModel::create(
            [
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]
        );

        if (!empty($data['permissions'])) {
            $newRole->permissions()->sync($data['permissions']);
        }

        return $newRole->refresh();
    }

    public function getAllRoles(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = RoleModel::query();
        
        if (isset($filters['search']) && $filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('display_name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }
        
        if (isset($filters['sort']) && in_array($filters['sort'], ['name', 'display_name', 'created_at'])) {
            $direction = $filters['direction'] ?? 'asc';
            $query->orderBy($filters['sort'], $direction === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }
        
        // add count user per role
        $query->withCount('users');

        return $query->paginate($perPage);
    }

    public function getRoleById(int $id): ?RoleModel
    {
        $role = RoleModel::find($id);
        if (!$role) {
            return null;
        }

        $role->load('permissions');
        $role->loadCount('users');

        return $role;
    }

    public function updateRole(int $id, array $data): RoleModel
    {
        $role = RoleModel::find($id);
        if (!$role) {
            throw new ModelNotFoundException('Role not found');
        }
        
        if (isset($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        $role->update($data);
        return $role->refresh();
    }

    public function deleteRole(int $id): bool
    {
        $role = RoleModel::find($id);
        if (!$role) {
            return false;
        }
        return (bool) $role->delete();
    }
}


