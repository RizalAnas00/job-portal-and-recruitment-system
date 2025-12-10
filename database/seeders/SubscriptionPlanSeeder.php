<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'plan_name' => 'Basic',
                'price' => 0,
                'duration_days' => 30,
                'job_post_limit' => 3,
                'allow_verified_badge' => false,
            ],
            [
                'plan_name' => 'Standard',
                'price' => 109000, // Rp 109.000
                'duration_days' => 90,
                'job_post_limit' => 10,
                'allow_verified_badge' => false,
            ],
            [
                'plan_name' => 'Premium',
                'price' => 199000, // Rp 199.000
                'duration_days' => 180,
                'job_post_limit' => 25,
                'allow_verified_badge' => true,
            ],
            [
                'plan_name' => 'Enterprise',
                'price' => 699000, // Rp 599.000
                'duration_days' => 365,
                'job_post_limit' => 999,
                'allow_verified_badge' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['plan_name' => $plan['plan_name']],
                $plan
            );
        }

        // SubscriptionPlan::factory()->count(5)->create();
    }
}
