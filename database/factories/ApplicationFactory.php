<?php

namespace Database\Factories;

use App\Models\JobSeeker;
use App\Models\JobPosting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'reviewed', 'accepted', 'rejected'];
        
        return [
            'id_job_seeker' => JobSeeker::inRandomOrder()->first()->id ?? JobSeeker::factory(),
            'id_job_posting' => JobPosting::inRandomOrder()->first()->id ?? JobPosting::factory(),
            'application_date' => $this->faker->dateTimeBetween('-15 days', 'now'),
            'status' => $this->faker->randomElement($statuses),
            'cover_letter' => $this->faker->paragraphs(3, true),
        ];
    }
}