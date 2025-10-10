<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plan_name'           => fake()->randomElement(['Basic', 'Standard', 'Premium']),
            'price'               => fake()->randomElement([0, 49.99, 99.99, 199.99]),
            'duration_days'       => fake()->randomElement([30, 90, 180, 365]),
            'job_post_limit'      => fake()->numberBetween(5, 100),
            'allow_verified_badge'=> fake()->boolean(30),
            'created_at'          => now(),
        ];
    }
}
