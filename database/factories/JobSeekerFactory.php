<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobSeeker>
 */
class JobSeekerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $emailName = strtolower($firstName . '.' . $lastName);
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $emailName . '@jobseeker.com',
            'password' => bcrypt('password'),
            'phone_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'profile_summary' => $this->faker->paragraphs(3, true),
        ];
    }
}
