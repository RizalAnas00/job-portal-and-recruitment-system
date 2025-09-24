<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'email' => 'admin1@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role_id' => 1,
            ],
            [
                'email' => 'admin2@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role_id' => 1,
            ],
            [
                'email' => 'admin3@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role_id' => 1,
            ],
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }

        User::factory()->count(70)->create();
    }
}
