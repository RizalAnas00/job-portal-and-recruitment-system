<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role_id', 3)->get();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {

            // Hindari duplikasi jika user sudah punya company
            if (Company::where('user_id', $user->id)->exists()) {
                continue;
            }

            $companyName = fake()->company();

            // Random warna HEX (6 digit)
            $bgColor = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
            $textColor = substr(str_shuffle('ABCDEF0123456789'), 0, 6);

            // Build URL placeholder
            $logoUrl = "https://placehold.co/640x640/{$bgColor}/{$textColor}?text=" . urlencode($companyName);

            Company::create([
                'user_id' => $user->id,
                'company_name' => $companyName,
                'phone_number' => fake()->unique()->numerify('+62##########'),
                'company_description' => fake()->paragraph(),
                'website' => fake()->url(),
                'industry' => fake()->randomElement([
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
                ]),
                'address' => fake()->address(),
                'is_verified' => fake()->boolean(20),
                'logo_path' => $logoUrl,
            ]);
        }
    }
}
