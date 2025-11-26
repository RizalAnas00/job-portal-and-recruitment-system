<?php

/**
 * Test script to verify notification system works correctly
 * 
 * Run this in tinker: php artisan tinker
 * Then paste the code below
 */

// Clear existing test notifications
\App\Models\Notification::query()->delete();

// Get test users
$jobSeeker = \App\Models\JobSeeker::first();
$company = \App\Models\Company::first();

if (!$jobSeeker || !$company) {
    echo "❌ Need at least one job seeker and one company in database\n";
    exit;
}

// Test 1: Send notification to job seeker
echo "\n=== Test 1: SendJobSeekerNotification ===\n";
$sendJobSeekerNotification = new \App\Actions\SendJobSeekerNotification();
$sendJobSeekerNotification(
    $jobSeeker,
    $company,
    "Test: Your application has been reviewed",
    "/test-url"
);

$jobSeekerNotification = \App\Models\Notification::latest()->first();
echo "Created notification ID: {$jobSeekerNotification->id}\n";
echo "id_job_seeker: " . ($jobSeekerNotification->id_job_seeker ?? 'NULL') . "\n";
echo "id_company: " . ($jobSeekerNotification->id_company ?? 'NULL') . "\n";

if ($jobSeekerNotification->id_job_seeker === $jobSeeker->id && $jobSeekerNotification->id_company === null) {
    echo "✅ PASS: Job seeker notification stored correctly\n";
} else {
    echo "❌ FAIL: Job seeker notification has wrong recipient fields\n";
}

// Test 2: Send notification to company
echo "\n=== Test 2: SendCompanyNotification ===\n";
$sendCompanyNotification = new \App\Actions\SendCompanyNotification();
$sendCompanyNotification(
    $company,
    "Test: New application received",
    "/test-url"
);

$companyNotification = \App\Models\Notification::latest()->first();
echo "Created notification ID: {$companyNotification->id}\n";
echo "id_job_seeker: " . ($companyNotification->id_job_seeker ?? 'NULL') . "\n";
echo "id_company: " . ($companyNotification->id_company ?? 'NULL') . "\n";

if ($companyNotification->id_company === $company->id && $companyNotification->id_job_seeker === null) {
    echo "✅ PASS: Company notification stored correctly\n";
} else {
    echo "❌ FAIL: Company notification has wrong recipient fields\n";
}

// Test 3: Query filtering
echo "\n=== Test 3: Query Filtering ===\n";

// Job seeker should see their notification
$jobSeekerNotifications = \App\Models\Notification::query()
    ->where('id_job_seeker', $jobSeeker->id)
    ->whereNull('id_company')
    ->get();

echo "Job seeker notifications found: {$jobSeekerNotifications->count()}\n";
if ($jobSeekerNotifications->count() > 0) {
    echo "✅ PASS: Job seeker can query their notifications\n";
} else {
    echo "❌ FAIL: Job seeker cannot find their notifications\n";
}

// Company should see their notification
$companyNotifications = \App\Models\Notification::query()
    ->where('id_company', $company->id)
    ->whereNull('id_job_seeker')
    ->get();

echo "Company notifications found: {$companyNotifications->count()}\n";
if ($companyNotifications->count() > 0) {
    echo "✅ PASS: Company can query their notifications\n";
} else {
    echo "❌ FAIL: Company cannot find their notifications\n";
}

// Test 4: Data integrity check
echo "\n=== Test 4: Data Integrity ===\n";

$invalidNotifications = \App\Models\Notification::query()
    ->where(function($q) {
        // Both null
        $q->whereNull('id_job_seeker')->whereNull('id_company');
    })
    ->orWhere(function($q) {
        // Both not null
        $q->whereNotNull('id_job_seeker')->whereNotNull('id_company');
    })
    ->get();

echo "Invalid notifications (both null or both set): {$invalidNotifications->count()}\n";
if ($invalidNotifications->count() === 0) {
    echo "✅ PASS: All notifications have exactly one recipient\n";
} else {
    echo "❌ FAIL: Found notifications with invalid recipient configuration\n";
}

echo "\n=== Summary ===\n";
echo "Total notifications created: " . \App\Models\Notification::count() . "\n";
echo "Job seeker notifications: " . \App\Models\Notification::whereNotNull('id_job_seeker')->count() . "\n";
echo "Company notifications: " . \App\Models\Notification::whereNotNull('id_company')->count() . "\n";
