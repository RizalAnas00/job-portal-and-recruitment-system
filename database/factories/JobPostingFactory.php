<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
        $openAt = $this->faker->dateTimeBetween('-5 days', '+5 days');
        $closeAt = (clone $openAt)->modify('+' . rand(1, 15) . ' days');
        $status = Carbon::now()->between(Carbon::instance($openAt), Carbon::instance($closeAt)) ? 'open' : 'closed';
        
        return [
            'id_company' => Company::inRandomOrder()->first()->id ?? Company::factory(),
            'job_title' => $this->faker->jobTitle(),
            'job_description' => $this->faker->paragraphs(5, true),
            'location' => $this->faker->city() . ', ' . $this->faker->country(),
            'job_type' => $this->faker->randomElement($jobTypes),
            'min_salary' => $this->faker->numberBetween(5000000, 1000000),
            'max_salary' => $this->faker->numberBetween(1000000, 50000000),
            'posted_date' => $openAt,
            'closing_date' => $closeAt,
            'status' => $status,
        ];
    }
}