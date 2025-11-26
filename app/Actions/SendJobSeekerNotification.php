<?php

namespace App\Actions;

use App\Models\Company;
use App\Models\JobSeeker;
use App\Models\Notification;

class SendJobSeekerNotification
{
    public function __invoke(?JobSeeker $jobSeeker, ?Company $company, string $message, ?string $linkUrl = null): void
    {
        if (!$jobSeeker) {
            return;
        }

        Notification::create([
            'id_job_seeker' => $jobSeeker->id,
            'id_company' => null, // Job seeker notification - id_company should be null
            'message' => $message,
            'link_url' => $linkUrl,
        ]);
    }
}


