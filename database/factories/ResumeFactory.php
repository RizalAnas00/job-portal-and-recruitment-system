<?php

namespace Database\Factories;

use App\Models\JobSeeker;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resume>
 */
class ResumeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_seeker_id' => JobSeeker::inRandomOrder()->first()->id,
            'resume_title' => $this->faker->sentence(3),
            'file_name' => $this->faker->word() . '.pdf',
            'file_path' => '/resumes/' . $this->faker->word() . '.pdf',
            'upload_date' => $this->faker->dateTimeThisYear(),
            'parsed_text' => $this->faker->paragraphs(3, true),
        ];
    }
}
