<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\JobSeeker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobSeeker = JobSeeker::inRandomOrder()->first();
        $company = Company::inRandomOrder()->first();
        
        return [
            'id_job_seeker' => $jobSeeker?->id,
            'id_company'    => $company?->id,
            'message'       => $this->faker->sentence(8),
            'is_read'       => $this->faker->boolean(30),
            'link_url'      => $this->faker->boolean(70) ? '/notifications' : null,
            'created_at'    => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
