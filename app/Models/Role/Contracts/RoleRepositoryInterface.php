<?php

namespace App\Models\Role\Contracts;

use App\Models\Role as RoleModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function createRole(array $data): RoleModel;

    public function getAllRoles(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getRoleById(int $id): ?RoleModel;

    public function updateRole(int $id, array $data): RoleModel;

    public function deleteRole(int $id): bool;
}


