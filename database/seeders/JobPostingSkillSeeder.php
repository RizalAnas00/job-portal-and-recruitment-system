<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobPostingSkillSeeder extends Seeder
{
    public function run()
    {
        $skills = Skill::pluck('id')->toArray();
        $jobPostings = JobPosting::pluck('id')->toArray();

        foreach ($jobPostings as $jobPostingId) {

            $randomSkills = collect($skills)->random(rand(3, 7));

            foreach ($randomSkills as $skillId) {

                // CEGAH DUPLIKAT
                DB::table('job_posting_skill')->updateOrInsert(
                    [
                        'id_job_posting' => $jobPostingId,
                        'id_skill' => $skillId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
