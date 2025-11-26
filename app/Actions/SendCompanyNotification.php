<?php

namespace App\Actions;

use App\Models\Company;
use App\Models\Notification;

class SendCompanyNotification
{
    public function __invoke(?Company $company, string $message, ?string $linkUrl = null): void
    {
        if (!$company) {
            return;
        }

        Notification::create([
            'id_job_seeker' => null,
            'id_company' => $company->id,
            'message' => $message,
            'link_url' => $linkUrl,
        ]);
    }
}
