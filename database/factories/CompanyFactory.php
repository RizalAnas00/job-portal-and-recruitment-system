<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyName = $this->faker->company();
        $emailName = strtolower(
            preg_replace('/[^a-zA-Z0-9]/', '', $companyName)
        );

        $industry = $this->faker->randomElement([
            'Teknnologi',
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

        return [
            'company_name' => $companyName,
            'email' => $emailName . '@company.com',
            'password' => bcrypt('password'),
            'company_description' => $this->faker->paragraph(),
            'website' => $this->faker->url(),
            'industry' => $industry,
            'address' => $this->faker->address(),
            'is_verified' => $this->faker->boolean(20),
        ];
    }

}
