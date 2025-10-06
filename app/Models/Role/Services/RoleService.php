<?php

namespace App\Models\Role\Services;

use App\Models\Role as RoleModel;
use App\Models\Role\Contracts\RoleRepositoryInterface;
use App\Models\Role\Contracts\RoleServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleService implements RoleServiceInterface
{
    public function __construct(private readonly RoleRepositoryInterface $roles)
    {
    }

    public function create(array $data): RoleModel
    {
        if (!array_key_exists('is_active', $data)) {
            $data['is_active'] = true;
        }
        return $this->roles->createRole($data);
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->roles->getAllRoles($filters, $perPage);
    }

    public function find(int $id): ?RoleModel
    {
        return $this->roles->getRoleById($id);
    }

    public function update(int $id, array $data): RoleModel
    {
        return $this->roles->updateRole($id, $data);
    }

    public function delete(int $id): bool
    {
        $role = $this->roles->getRoleById($id);
        if (!$role) {
            return false;
        }
        if ($role->users()->exists()) {
            return false;
        }
        return $this->roles->deleteRole($id);
    }
}


