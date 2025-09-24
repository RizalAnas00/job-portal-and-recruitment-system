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
        return [
            'id_job_seeker' => JobSeeker::factory(),
            'id_company'    => Company::factory(),
            'message'       => $this->faker->sentence(8),
            'is_read'       => $this->faker->boolean(30),
            'link_url'      => $this->faker->url(),
            'created_at'    => now(),
        ];
    }
}
