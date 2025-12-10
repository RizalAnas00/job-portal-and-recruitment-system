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

        foreach ($users as $user) {

            if (JobSeeker::where('user_id', $user->id)->exists()) {
                continue;
            }

            $first = fake()->firstName();
            $last = fake()->lastName();
            $fullName = "{$first} {$last}";

            // Random warna HEX 6 digit
            $bgColor = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
            $textColor = substr(str_shuffle('ABCDEF0123456789'), 0, 6);

            // Placeholder dengan nama job seeker
            $profilePicture = "https://placehold.co/640x640/{$bgColor}/{$textColor}?text=" . urlencode($fullName);

            $jobSeeker = JobSeeker::create([
                'user_id' => $user->id,
                'first_name' => $first,
                'last_name' => $last,
                'phone_number' => fake()->unique()->numerify('+62##########'),
                'address' => fake()->address(),
                'profile_summary' => fake()->paragraphs(3, true),
                'profile_picture_path' => $profilePicture,
            ]);

            // Assign 3–16 random unique skills
            if ($skills->isNotEmpty()) {
                $randomSkills = $skills->random(rand(3, 16))->pluck('id')->toArray();
                $jobSeeker->skills()->sync($randomSkills);
            }
        }
    }
}
