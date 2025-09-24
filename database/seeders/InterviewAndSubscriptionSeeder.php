<?php

namespace Database\Seeders;

use App\Models\CompanySubscription;
use App\Models\Interview;
use Illuminate\Database\Seeder;

class InterviewAndSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 interviews
        Interview::factory()
            ->count(20)
            ->create();

        // Create 10 company subscriptions
        CompanySubscription::factory()
            ->count(10)
            ->create();
    }
}