<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JobSeeker;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class JobSeekerSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role_id', 2)->get();
        $skills = Skill::all();

        if ($users->isEmpty()) {
            return;
        }

        // if ($skills->isEmpty()) {
        // }

        foreach ($users as $user) {

            if (JobSeeker::where('user_id', $user->id)->exists()) {
                continue;
            }

            $jobSeeker = JobSeeker::create([
                'user_id' => $user->id,
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'phone_number' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'profile_summary' => fake()->paragraphs(3, true),
                'profile_picture_path' => fake()->imageUrl(),
            ]);

            // Assign 3–16 skill random unik
            if ($skills->isNotEmpty()) {
                $randomSkills = $skills->random(rand(3, 16))->pluck('id')->toArray();
                $jobSeeker->skills()->sync($randomSkills);
            }
        }
    }
}
