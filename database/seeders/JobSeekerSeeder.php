<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JobSeeker;
use Illuminate\Database\Seeder;

class JobSeekerSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role_id', 2)->get();

        if ($users->isEmpty()) {
            echo "⚠️ Tidak ada user role_id = 2. Melewati JobSeekerSeeder...\n";
            return;
        }

        foreach ($users as $user) {
            // Cegah duplikasi JobSeeker untuk user yang sama
            if (JobSeeker::where('user_id', $user->id)->exists()) {
                continue;
            }

            JobSeeker::create([
                'user_id' => $user->id,
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'phone_number' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'profile_summary' => fake()->paragraphs(3, true),
            ]);
        }

    }
}
