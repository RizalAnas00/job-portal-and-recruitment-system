<?php

namespace App\Models\Role\Contracts;

use App\Models\Role as RoleModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoleServiceInterface
{
    public function create(array $data): RoleModel;

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?RoleModel;

    public function update(int $id, array $data): RoleModel;

    public function delete(int $id): bool;
}


