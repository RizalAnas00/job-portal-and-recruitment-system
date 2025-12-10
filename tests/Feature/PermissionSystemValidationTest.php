<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionSystemValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run seeders
        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_roles_are_created_successfully()
    {
        $admin = Role::where('name', 'admin')->first();
        $user = Role::where('name', 'user')->first();
        $company = Role::where('name', 'company')->first();

        $this->assertNotNull($admin);
        $this->assertNotNull($user);
        $this->assertNotNull($company);
        
        $this->assertEquals('Administrator', $admin->display_name);
        $this->assertEquals('Job Seeker', $user->display_name);
        $this->assertEquals('Company', $company->display_name);
    }

    public function test_permissions_are_seeded_correctly()
    {
        $totalPermissions = Permission::count();
        $this->assertGreaterThan(0, $totalPermissions);
        
        // Test some key permissions exist
        $this->assertDatabaseHas('permissions', ['name' => 'user.read']);
        $this->assertDatabaseHas('permissions', ['name' => 'job_seeker.read.own']);
        $this->assertDatabaseHas('permissions', ['name' => 'company.read.own']);
    }

    public function test_admin_has_all_permissions()
    {
        $admin = Role::where('name', 'admin')->first();
        $totalPermissions = Permission::count();
        
        $this->assertEquals($totalPermissions, $admin->permissions->count());
        
        // Test admin has key management permissions
        $this->assertTrue($admin->hasPermission('user.read'));
        $this->assertTrue($admin->hasPermission('company.delete'));
        $this->assertTrue($admin->hasPermission('skill.create'));
    }

    public function test_user_has_correct_permissions()
    {
        $user = Role::where('name', 'user')->first();
        
        // Test user has basic permissions
        $this->assertTrue($user->hasPermission('job_seeker.read.own'));
        $this->assertTrue($user->hasPermission('resume.crud'));
        $this->assertTrue($user->hasPermission('application.create'));
        $this->assertTrue($user->hasPermission('company.read'));
        
        // Test user doesn't have admin permissions
        $this->assertFalse($user->hasPermission('user.delete'));
        $this->assertFalse($user->hasPermission('company.delete'));
    }

    public function test_company_has_correct_permissions()
    {
        $company = Role::where('name', 'company')->first();
        
        // Test company has business permissions
        $this->assertTrue($company->hasPermission('company.read.own'));
        $this->assertTrue($company->hasPermission('job_posting.crud.own'));
        $this->assertTrue($company->hasPermission('application.read.own'));
        $this->assertTrue($company->hasPermission('interview.crud.own'));
        $this->assertTrue($company->hasPermission('job_seeker.read'));
        
        // Test company doesn't have admin permissions
        $this->assertFalse($company->hasPermission('user.delete'));
        $this->assertFalse($company->hasPermission('skill.create'));
    }

    public function test_user_assignment_and_permissions()
    {
        $testUser = User::create([
            'email' => 'permission_test@example.com',
            'password' => bcrypt('password'),
            'role_id' => null
        ]);

        // Test admin assignment
        $testUser->assignRole('admin');
        $this->assertTrue($testUser->hasRole('admin'));
        $this->assertTrue($testUser->hasPermission('user.read'));
        $this->assertTrue($testUser->hasPermission('company.delete'));

        // Test role switching
        $testUser->assignRole('user');
        $this->assertTrue($testUser->hasRole('user'));
        $this->assertFalse($testUser->hasRole('admin'));
        $this->assertTrue($testUser->hasPermission('application.create'));
        $this->assertFalse($testUser->hasPermission('user.delete'));

        // Test company role
        $testUser->assignRole('company');
        $this->assertTrue($testUser->hasRole('company'));
        $this->assertTrue($testUser->hasPermission('job_posting.crud.own'));
        $this->assertFalse($testUser->hasPermission('user.delete'));
    }

    public function test_permission_structure_matches_requirements()
    {
        // Test Admin permissions structure
        $adminPermissions = Role::where('name', 'admin')->first()->permissions->pluck('name')->toArray();
        
        $requiredAdminPermissions = [
            'user.read', 'user.update', 'user.delete',
            'job_seeker.read', 'job_seeker.update', 'job_seeker.delete',
            'company.read', 'company.update', 'company.delete',
            'job_posting.read', 'job_posting.update', 'job_posting.delete',
            'skill.create', 'skill.read', 'skill.update', 'skill.delete',
            'notification.create', 'notification.read', 'notification.delete'
        ];

        foreach ($requiredAdminPermissions as $permission) {
            $this->assertContains($permission, $adminPermissions, "Admin missing permission: {$permission}");
        }

        // Test User permissions structure
        $userPermissions = Role::where('name', 'user')->first()->permissions->pluck('name')->toArray();
        
        $requiredUserPermissions = [
            'job_seeker.create', 'job_seeker.read.own', 'job_seeker.update.own', 'job_seeker.delete.own',
            'resume.crud', 'company.read', 'job_posting.read',
            'application.create', 'application.read.own', 'application.update.own',
            'interview.read.own'
        ];

        foreach ($requiredUserPermissions as $permission) {
            $this->assertContains($permission, $userPermissions, "User missing permission: {$permission}");
        }

        // Test Company permissions structure
        $companyPermissions = Role::where('name', 'company')->first()->permissions->pluck('name')->toArray();
        
        $requiredCompanyPermissions = [
            'company.create', 'company.read.own', 'company.update.own', 'company.delete.own',
            'job_posting.crud.own', 'application.read.own', 'application.update.own',
            'interview.crud.own', 'job_seeker.read'
        ];

        foreach ($requiredCompanyPermissions as $permission) {
            $this->assertContains($permission, $companyPermissions, "Company missing permission: {$permission}");
        }
    }
}