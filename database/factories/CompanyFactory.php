<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected static int $userIdCounter = 4;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyName = $this->faker->company();

        $industry = $this->faker->randomElement([
            'Teknologi',
            'Finansial',
            'Kesehatan',
            'Pendidikan',
            'Retail',
            'Manufaktur',
            'Energi',
            'Transportasi',
            'Konstruksi',
            'Hiburan',
            'Telekomunikasi',
            'Pariwisata',
            'Pertanian',
            'Real Estate',
            'Layanan Profesional',
            'Non-Profit',
            'Pemerintahan',
            'Lainnya',
        ]);

        $userId = self::$userIdCounter++;

        return [
            'user_id' => $userId,
            'company_name' => $companyName,
            'company_description' => $this->faker->paragraph(),
            'website' => $this->faker->url(),
            'industry' => $industry,
            'address' => $this->faker->address(),
            'is_verified' => $this->faker->boolean(20),
        ];
    }
}
