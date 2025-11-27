<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\JobSeeker;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['jobseeker', 'company']);

        if ($type === 'jobseeker') {
            $recipientJobSeeker = JobSeeker::inRandomOrder()->first();

            return [
                'id_job_seeker' => $recipientJobSeeker?->id,
                'id_company'    => null,
                'message'       => $this->faker->sentence(8),
                'is_read'       => $this->faker->boolean(30),
                'link_url'      => $this->faker->boolean(70) ? '/notifications' : null,
                'created_at'    => $this->faker->dateTimeBetween('-30 days', 'now'),
            ];
        }

        $recipientCompany = Company::inRandomOrder()->first();

        return [
            'id_job_seeker' => null,
            'id_company'    => $recipientCompany?->id,
            'message'       => $this->faker->sentence(8),
            'is_read'       => $this->faker->boolean(30),
            'link_url'      => $this->faker->boolean(70) ? '/notifications' : null,
            'created_at'    => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
