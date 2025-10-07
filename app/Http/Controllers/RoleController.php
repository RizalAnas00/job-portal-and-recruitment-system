<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Role\Contracts\RoleServiceInterface;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
        // Additional layer: enforce permission middleware per action
        $this->middleware('permission:role.read')->only(['index', 'show']);
        $this->middleware('permission:role.create')->only(['create', 'store']);
        $this->middleware('permission:role.update')->only(['edit', 'update']);
        $this->middleware('permission:role.delete')->only(['destroy']);
    }

    public function index()
    {
        // Memanggil fungsi list()
        $roles = $this->roleService->list();
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::getAllPermissions();
        return view('role.create' , compact('permissions'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description'  => 'nullable|string|max:255',
            'is_active'    => 'sometimes|boolean',
            'permissions'  => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);
        
        $postDisplayName = formatRoleName($validatedData['name']);
        $validatedData['name'] = $postDisplayName;

        // Menggabungkan data ke dalam satu array
        $dataToCreate = $validatedData;
        $dataToCreate['is_active'] = $request->has('is_active');

        // Memanggil fungsi create() dengan satu array
        $this->roleService->create($dataToCreate);
        
        return redirect()->route('role.index')
                         ->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::getAllPermissions();
        return view('role.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name'  => 'required|string|max:255',
            'description'   => 'nullable|string|max:255',
            'is_active'     => 'sometimes|boolean',
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        // Format nama agar konsisten dengan helper
        $postDisplayName = formatRoleName($validatedData['name']);
        $validatedData['name'] = $postDisplayName;

        // Gabungkan semua data ke satu array
        $dataToUpdate = $validatedData;
        $dataToUpdate['is_active'] = $request->has('is_active');

        // Panggil service untuk update role
        $this->roleService->update($role->id, $dataToUpdate);

        return redirect()->route('role.index')
                        ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        // Memanggil fungsi delete() dengan ID
        $this->roleService->delete($role->id);

        return redirect()->route('role.index')
                         ->with('success', 'Role berhasil dihapus.');
    }

    public function show(Role $role)
    {
        $role = $this->roleService->find($role->id);

        return view('role.show', compact('role'));
    }
}