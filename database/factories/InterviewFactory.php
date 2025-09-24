<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Interview;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterviewFactory extends Factory
{
    protected $model = Interview::class;

    public function definition(): array
    {
        return [
            'id_application' => Application::factory(),
            'interviewer_name' => $this->faker->name(),
            'interview_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'interview_type' => $this->faker->randomElement(['phone', 'video', 'in_person']),
            'location' => $this->faker->address(),
            'notes' => $this->faker->paragraph(),
        ];
    }
}