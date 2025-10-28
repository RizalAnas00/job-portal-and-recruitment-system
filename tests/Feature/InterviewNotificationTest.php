<?php

use App\Models\Application;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\JobSeeker;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createRoles(): array
{
    $companyRole = Role::create([
        'name' => 'company',
        'display_name' => 'Company',
        'description' => 'Company role',
    ]);

    $userRole = Role::create([
        'name' => 'user',
        'display_name' => 'User',
        'description' => 'User role',
    ]);

    return [$companyRole, $userRole];
}

function createCompanyUser(Role $role): array
{
    $user = User::create([
        'email' => 'company@example.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
        'email_verified_at' => now(),
    ]);

    $company = Company::create([
        'user_id' => $user->id,
        'company_name' => 'Test Company',
        'phone_number' => '+6281234567890',
        'company_description' => 'Test company description',
        'website' => 'https://test.com',
        'industry' => 'Teknologi',
        'address' => 'Test Address',
    ]);

    return [$user, $company];
}

function createJobSeekerUser(Role $role): array
{
    $user = User::create([
        'email' => 'jobseeker@example.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
        'email_verified_at' => now(),
    ]);

    $jobSeeker = JobSeeker::create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone_number' => '+6281234567891',
        'address' => 'Test Address',
        'profile_summary' => 'Test profile',
    ]);

    return [$user, $jobSeeker];
}

it('sends notification when company marks application as hired', function () {
    [$companyRole, $userRole] = createRoles();
    [$companyUser, $company] = createCompanyUser($companyRole);
    [$jobSeekerUser, $jobSeeker] = createJobSeekerUser($userRole);

    $jobPosting = JobPosting::factory()->create([
        'id_company' => $company->id,
    ]);

    $application = Application::factory()->create([
        'id_job_seeker' => $jobSeeker->id,
        'id_job_posting' => $jobPosting->id,
        'status' => 'under_review',
    ]);

    $response = $this->actingAs($companyUser)
        ->patch(route('company.applications.update', $application), [
            'status' => 'hired',
        ]);

    $response->assertStatus(302);

    $application->refresh();

    expect($application->status)->toBe('hired');
    expect(Notification::count())->toBe(1);

    $notification = Notification::first();

    expect($notification->id_job_seeker)->toBe($jobSeeker->id);
    expect($notification->id_company)->toBe($company->id);
    expect($notification->message)->toContain('Selamat');
});

it('creates interview and notifies job seeker', function () {
    [$companyRole, $userRole] = createRoles();
    [$companyUser, $company] = createCompanyUser($companyRole);
    [$jobSeekerUser, $jobSeeker] = createJobSeekerUser($userRole);

    $jobPosting = JobPosting::factory()->create([
        'id_company' => $company->id,
    ]);

    $application = Application::factory()->create([
        'id_job_seeker' => $jobSeeker->id,
        'id_job_posting' => $jobPosting->id,
        'status' => 'under_review',
    ]);

    $payload = [
        'id_application' => $application->id,
        'interviewer_name' => 'Jane Doe',
        'interview_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'interview_type' => 'online',
        'location' => 'Zoom Meeting',
        'notes' => 'Silakan siapkan presentasi singkat.',
    ];

    $response = $this->actingAs($companyUser)
        ->post(route('interviews.store'), $payload);

    $response->assertStatus(302);

    $application->refresh();

    expect($application->status)->toBe('interviewing');
    expect(Notification::count())->toBe(1);

    $notification = Notification::first();

    expect($notification->id_job_seeker)->toBe($jobSeeker->id);
    expect($notification->id_company)->toBe($company->id);
    expect($notification->message)->toContain('dijadwalkan');
    expect($notification->link_url)->toBe(route('interviews.index'));
});


