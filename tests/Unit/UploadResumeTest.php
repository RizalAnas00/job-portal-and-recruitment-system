<?php

use App\Models\JobSeeker;
use App\Models\Resume;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {

    // Create role
    $this->role = Role::create([
        'name' => 'user',
        'display_name' => 'Job Seeker',
        'description' => 'job seekering',
    ]);

    // Create user
    $this->user = User::factory()->create([
        'role_id' => $this->role->id,
    ]);

    // Create job seeker
    $this->jobSeeker = JobSeeker::factory()->create([
        'user_id' => $this->user->id,
    ]);

    // Relations
    $this->user->setRelation('jobSeeker', $this->jobSeeker);

    // Login
    $this->actingAs($this->user);
});

it('only user can upload resume', function () {
    $response = $this->get(route('user.resume.index'));

    $response->d    ;
    expect($response->status())->toBe(200);
});

it('handle resume file and save to storage', function () {

    $sourcePath = storage_path('app/TestFiles/example-cv-file.pdf');
    expect(file_exists($sourcePath))->toBeTrue();

    $targetDir = storage_path('app/public/user/resume-files');

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetPath = $targetDir . '/copied-example.pdf';
    copy($sourcePath, $targetPath);

    expect(file_exists($targetPath))->toBeTrue();

    $resume = Resume::factory()->create([
        'job_seeker_id' => $this->jobSeeker->id,
        'resume_title' => 'Test Resume',
        'file_name' => 'copied-example.pdf',
        'file_path' => $targetPath,
        'upload_date' => now(),
        'parsed_text' => '',
    ]);

    expect($resume)->toBeInstanceOf(Resume::class);

    unlink($targetPath);
});
