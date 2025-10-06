<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Role\Contracts\RoleServiceInterface;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        // Memanggil fungsi list()
        $roles = $this->roleService->list();
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        return view('role.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
        ]);

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
        return view('role.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
        ]);

        // Menggabungkan data ke dalam satu array
        $dataToUpdate = $validatedData;
        $dataToUpdate['is_active'] = $request->has('is_active');

        // Memanggil fungsi update() dengan ID dan satu array
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
        return view('role.show', compact('role'));
    }
}