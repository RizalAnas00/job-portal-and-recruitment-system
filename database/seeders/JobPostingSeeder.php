<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobPostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 job postings
        JobPosting::factory(50)->create();
    }
}