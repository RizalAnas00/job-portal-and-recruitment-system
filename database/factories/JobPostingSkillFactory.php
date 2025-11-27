<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPostingSkill>
 */
class JobPostingSkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skillIds = \App\Models\Skill::pluck('id')->toArray();
        $jobPostingIds = \App\Models\JobPosting::pluck('id')->toArray();

        return [
            'id_job_posting' => $this->faker->randomElement($jobPostingIds),
            'id_skill' => $this->faker->randomElement($skillIds),    
        ];
    }
}
