<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            JobSeekerSeeder::class,
            CompanySeeder::class,
            ResumeSeeder::class,
            ApplicationSeeder::class,
            JobPostingSeeder::class,
            SkillSeeder::class,
            JobPostingSkillSeeder::class,
            SubscriptionPlanSeeder::class,
            NotificationSeeder::class,
            InterviewAndSubscriptionSeeder::class,
        ]);
    }
}
