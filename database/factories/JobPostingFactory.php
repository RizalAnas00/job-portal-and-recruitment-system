<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPosting>
 */
class JobPostingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobTypes = ['full_time', 'part_time', 'contract', 'internship', 'temporary', 'freelance', 'remote'];
        $statuses = ['draft', 'open', 'paused', 'closed', 'archived'];
        
        return [
            'id_company' => Company::inRandomOrder()->first()->id ?? Company::factory(),
            'job_title' => $this->faker->jobTitle(),
            'job_description' => $this->faker->paragraphs(5, true),
            'location' => $this->faker->city() . ', ' . $this->faker->country(),
            'job_type' => $this->faker->randomElement($jobTypes),
            'salary_range' => $this->faker->numberBetween(30000000, 150000000) . ' - ' . $this->faker->numberBetween(50000000, 200000000),
            'posted_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'closing_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}