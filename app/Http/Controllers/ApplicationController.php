<?php

namespace App\Http\Controllers;

use App\Actions\SendCompanyNotification;
use App\Actions\SendJobSeekerNotification;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly SendJobSeekerNotification $sendJobSeekerNotification,
        private readonly SendCompanyNotification $sendCompanyNotification
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $query = Application::with('jobSeeker.user', 'jobPosting.company');

        if ($user->hasRole('user')) {
            // User hanya melihat lamaran miliknya
            $query->where('id_job_seeker', $user->jobSeeker->id);
        } elseif ($user->hasRole('company')) {
            // Company melihat lamaran untuk semua lowongan miliknya
            $companyId = $user->company->id;
            $query->whereHas('jobPosting', function ($q) use ($companyId) {
                $q->where('id_company', $companyId);
            });
        }

        // Log::info('Fetching applications for user ID: ' . $query->toSql());

        $applications = $query->latest()->paginate(10);

        if ($user->hasRole('user')) {
            return view('applications.user-applied-jobs', compact('applications'));
        } elseif ($user->hasRole('company')) {
            return view('applications.index_by_job_posting', compact('applications'));
        } else {
            abort(403, 'AKSES DITOLAK');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(JobPosting $jobPosting)
    {
        return view('applications.create', compact('jobPosting'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, JobPosting $jobPosting)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        if (!$user->hasRole('user') || !$user->jobSeeker) {
            abort(403, 'Hanya pencari kerja yang bisa melamar.');
        }

        $request->validate([
            'cover_letter' => 'nullable|string',
        ]);

        // Mencegah duplikat lamaran
        $existingApplication = Application::where('id_job_seeker', $user->jobSeeker->id)
            ->where('id_job_posting', $jobPosting->id)
            ->exists();

        if ($existingApplication) {
            return back()->with('error', 'Anda sudah melamar pada lowongan ini.');
        }

        Application::create([
            'id_job_seeker' => $user->jobSeeker->id,
            'id_job_posting' => $jobPosting->id, 
            'cover_letter' => $request->cover_letter,
            'status' => 'applied'
        ]);

        // Send notification to company about new application
        $jobPosting->loadMissing('company');
        if ($jobPosting->company) {
            $applicantName = $user->name ?? 'Pelamar baru';
            $message = "{$applicantName} telah melamar pada posisi {$jobPosting->job_title}.";
            ($this->sendCompanyNotification)(
                $jobPosting->company,
                $message,
                route('company.applications.index')
            );
        }

        return redirect()->route('applications.index')->with('success', 'Lamaran berhasil dikirim.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        return view('applications.edit', compact('application'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->hasRole('user') && $user->jobSeeker?->id === $application->id_job_seeker) {
            // User hanya boleh mengupdate cover letter
            $data = $request->validate(['cover_letter' => 'nullable|string']);
            $application->update($data);
        } elseif ($user->hasRole('company') && $user->company?->id === $application->jobPosting->id_company) {
            // Company hanya boleh mengupdate status
            $data = $request->validate([
                'status' => 'required|in:applied,under_review,interview_scheduled,interviewing,offered,hired,rejected',
            ]);

            $application->update($data);

            $application->loadMissing('jobSeeker', 'jobPosting.company');

            $this->maybeSendStatusNotification($application);
        } else {
            abort(403);
        }

        return back()->with('success', 'Lamaran berhasil diperbarui.');
    }

    private function maybeSendStatusNotification(Application $application): void
    {
        $jobSeeker = $application->jobSeeker;
        $company = $application->jobPosting?->company;

        if (!$jobSeeker) {
            return;
        }

        $messages = [
            'interviewing' => fn() => "Status lamaran Anda untuk posisi {$application->jobPosting?->job_title} di {$company?->company_name} kini berlanjut ke tahap interview.",
            'hired' => fn() => "Selamat! Anda diterima di {$company?->company_name} untuk posisi {$application->jobPosting?->job_title}.",
            'rejected' => fn() => "Maaf, lamaran Anda untuk posisi {$application->jobPosting?->job_title} di {$company?->company_name} belum dapat dilanjutkan.",
        ];

        $status = $application->status;

        if (!array_key_exists($status, $messages)) {
            return;
        }

        $message = $messages[$status]();

        ($this->sendJobSeekerNotification)(
            $jobSeeker,
            $company,
            $message,
            route('user.applications.index')
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Otorisasi: Hanya user pemilik atau admin yang bisa menghapus
        if (
            ($user->hasRole('user') && $user->jobSeeker?->id !== $application->id_job_seeker) &&
            !$user->hasRole('admin')
        ) {
            abort(403);
        }

        $application->delete();
        return redirect()->route('applications.index')->with('success', 'Lamaran berhasil dihapus.');
    }

    /**
     * Display a listing of applications for a specific job posting.
     */
    public function indexByJobPosting(JobPosting $jobPosting, Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Otorisasi: Hanya admin atau pemilik perusahaan dari job posting yang bisa melihat
        if (!$user->hasRole('admin') && !($user->hasRole('company') && $user->company?->id === $jobPosting->id_company)) {
            abort(403, 'AKSES DITOLAK');
        }

        $query = $jobPosting->applications()->with('jobSeeker.user');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(10);
        $statuses = ['pending', 'reviewed', 'accepted', 'rejected']; // Define available statuses for filtering

        return view('applications.index_by_job_posting', compact('jobPosting', 'applications', 'statuses'));
    }

    /**
     * Filter applications for a specific job posting by status.
     */
    public function filterByStatus(JobPosting $jobPosting, Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Otorisasi: Hanya admin atau pemilik perusahaan dari job posting yang bisa melihat
        if (!$user->hasRole('admin') && !($user->hasRole('company') && $user->company?->id === $jobPosting->id_company)) {
            abort(403, 'AKSES DITOLAK');
        }

        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(['all', 'pending', 'reviewed', 'accepted', 'rejected'])],
        ]);

        $query = $jobPosting->applications()->with('jobSeeker.user');

        if (isset($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        $applications = $query->latest()->paginate(10);
        $statuses = ['pending', 'reviewed', 'accepted', 'rejected']; // Define available statuses for filtering

        return view('applications.index_by_job_posting', compact('jobPosting', 'applications', 'statuses'));
    }
}
