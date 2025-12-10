<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobSeeker>
 */
class JobSeekerFactory extends Factory
{
    protected static int $userIdCounter = 23;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        $userId = self::$userIdCounter++;
        
        return [
            'user_id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'profile_summary' => $this->faker->paragraphs(3, true),
        ];
    }
}
