<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'User with full access to the system',
                'is_active' => true,
            ],
            [
                'name' => 'user',
                'display_name' => 'Job Seeker',
                'description' => 'User who is looking for jobs',
                'is_active' => true,
            ],
            [
                'name' => 'company',
                'display_name' => 'Company',
                'description' => 'Company that posts jobs',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']], 
                $role
            );
        }
    }
}
