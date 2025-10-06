<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->slug(2);
        return [
            'name' => $name,
            'display_name' => ucwords(str_replace('-', ' ', $name)),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}


