<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Single Admin with optional env overrides
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');
        $adminName = env('ADMIN_NAME', 'Administrator');

        $adminRoleId = optional(Role::where('name', 'admin')->first())->id ?? 1;

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => bcrypt($adminPassword),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role_id' => $adminRoleId,
            ]
        );

        $companies = [
            [
                'name' => 'Tech Corp',
                'email' => 'contact@techcorp.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role_id' => optional(Role::where('name', 'company')->first())->id ?? 2,
            ],
            [
                'name' => 'Innovate Inc',
                'email' => 'hr@innovateinc.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role_id' => optional(Role::where('name', 'company')->first())->id ?? 2,
            ],
        ];

        foreach ($companies as $company) {
            User::firstOrCreate(
                ['email' => $company['email']],
                $company
            );
        }

        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role_id' => optional(Role::where('name', 'user')->first())->id ?? 3,
            ],
            [
                'name' => 'Citra Lestari',
                'email' => 'citra.lestari@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role_id' => optional(Role::where('name', 'user')->first())->id ?? 3,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        User::factory()->count(70)->create();
    }
}
