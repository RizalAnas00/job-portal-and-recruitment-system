<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\SendCompanyNotification;
use App\Actions\SendJobSeekerNotification;
use App\Models\Company;
use App\Models\JobSeeker;
use App\Models\Notification;
use Illuminate\Http\Request;

class BroadcastNotificationController extends Controller
{
    public function __construct(
        private readonly SendJobSeekerNotification $sendJobSeekerNotification,
        private readonly SendCompanyNotification $sendCompanyNotification
    ) {
        $this->middleware('role:admin');
    }

    /**
     * Show the form for creating a broadcast notification.
     */
    public function create()
    {
        return view('admin.broadcast-notifications.create');
    }

    /**
     * Store a newly created broadcast notification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'link_url' => 'nullable|url|max:255',
            'recipients' => 'required|array',
            'recipients.*' => 'in:job_seekers,companies,all',
        ]);

        $recipients = $validated['recipients'];
        $message = $validated['message'];
        $linkUrl = $validated['link_url'] ?? null;

        $count = 0;

        // Send to job seekers
        if (in_array('job_seekers', $recipients) || in_array('all', $recipients)) {
            $jobSeekers = JobSeeker::with('user')->get();
            foreach ($jobSeekers as $jobSeeker) {
                if ($jobSeeker->user && $jobSeeker->user->is_active) {
                    ($this->sendJobSeekerNotification)($jobSeeker, null, $message, $linkUrl);
                    $count++;
                }
            }
        }

        // Send to companies
        if (in_array('companies', $recipients) || in_array('all', $recipients)) {
            $companies = Company::with('user')->get();
            foreach ($companies as $company) {
                if ($company->user && $company->user->is_active) {
                    ($this->sendCompanyNotification)($company, $message, $linkUrl);
                    $count++;
                }
            }
        }

        return redirect()->route('admin.broadcast-notifications.create')
            ->with('success', "Notifikasi berhasil dikirim ke {$count} penerima.");
    }
}
