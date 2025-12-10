<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test permissions
        Permission::create(['name' => 'view_users', 'display_name' => 'View Users', 'group' => 'user_management']);
        Permission::create(['name' => 'create_users', 'display_name' => 'Create Users', 'group' => 'user_management']);
        Permission::create(['name' => 'edit_users', 'display_name' => 'Edit Users', 'group' => 'user_management']);
        Permission::create(['name' => 'delete_users', 'display_name' => 'Delete Users', 'group' => 'user_management']);
        
        // Create test roles
        Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access']);
        Role::create(['name' => 'user', 'display_name' => 'Regular User', 'description' => 'Limited access']);
    }

    public function test_role_can_give_permission()
    {
        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo('view_users');
        
        $this->assertTrue($role->hasPermission('view_users'));
    }

    public function test_role_can_revoke_permission()
    {
        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo('view_users');
        $role->revokePermissionTo('view_users');
        
        $this->assertFalse($role->hasPermission('view_users'));
    }

    public function test_user_can_be_assigned_role()
    {
        $user = User::create([
            'email' => 'test6@example.com',
            'password' => bcrypt('password'),
            'role_id' => null
        ]);
        $user->assignRole('admin');
        
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_user_role_is_updated_when_assigning_new_role()
    {
        $user = User::create([
            'email' => 'test4@example.com',
            'password' => bcrypt('password'),
            'role_id' => null
        ]);
        $user->assignRole('user');
        $this->assertTrue($user->hasRole('user'));
        
        // Assign new role - should replace the old one
        $user->assignRole('admin');
        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('user'));
    }

    public function test_user_can_revoke_role()
    {
        $user = User::create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => null
        ]);
        $user->assignRole('admin');
        $user->revokeRole('admin');
        
        $this->assertFalse($user->hasRole('admin'));
        $this->assertNull($user->getRoleName());
    }

    public function test_user_inherits_permissions_from_role()
    {
        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo('view_users');
        $role->givePermissionTo('create_users');
        
        $user = User::create([
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'role_id' => null
        ]);
        $user->assignRole('admin');
        
        $this->assertTrue($user->hasPermission('view_users'));
        $this->assertTrue($user->hasPermission('create_users'));
        $this->assertFalse($user->hasPermission('delete_users'));
    }

    public function test_user_has_any_permission()
    {
        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo('view_users');
        
        $user = User::create([
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
            'role_id' => null
        ]);
        $user->assignRole('admin');
        
        $this->assertTrue($user->hasAnyPermission(['view_users', 'delete_users']));
        $this->assertFalse($user->hasAnyPermission(['edit_users', 'delete_users']));
    }

    public function test_user_has_all_permissions()
    {
        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo('view_users');
        $role->givePermissionTo('create_users');
        
        $user = User::create([
            'email' => 'test5@example.com',
            'password' => bcrypt('password'),
            'role_id' => null
        ]);
        $user->assignRole('admin');
        
        $this->assertTrue($user->hasAllPermissions(['view_users', 'create_users']));
        $this->assertFalse($user->hasAllPermissions(['view_users', 'delete_users']));
    }
}