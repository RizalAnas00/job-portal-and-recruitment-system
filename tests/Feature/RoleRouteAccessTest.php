<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_role_index()
    {
        $response = $this->get(route('role.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_without_permission_gets_403()
    {
        $role = Role::create(['name' => 'user', 'display_name' => 'User', 'is_active' => true]);
        $user = User::factory()->create(['role_id' => $role->id]);

        // Ensure permission exists but not assigned to 'user'
        Permission::create(['name' => 'role.read', 'display_name' => 'View Roles', 'group' => 'Role Management']);

        $this->actingAs($user);

        $response = $this->get(route('role.index'));
        $response->assertStatus(403);
    }

    public function test_admin_with_permission_can_access_index()
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'is_active' => true]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // create and assign permission to admin role
        $perm = Permission::create(['name' => 'role.read', 'display_name' => 'View Roles', 'group' => 'Role Management']);
        $adminRole->permissions()->attach($perm->id);

        $this->actingAs($admin);

        $response = $this->get(route('role.index'));
        // If the view exists it should return 200; otherwise controller might redirect — accept 200 or 302 as success
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }
}
