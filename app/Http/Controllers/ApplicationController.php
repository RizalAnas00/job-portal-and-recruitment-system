<?php

namespace App\Http\Controllers;

use App\Actions\SendJobSeekerNotification;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly SendJobSeekerNotification $sendJobSeekerNotification
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Application::with('jobSeeker.user', 'jobPosting.company');

        if ($user->hasRole('user')) {
            // User hanya melihat lamaran miliknya
            $query->where('job_seeker_id', $user->jobSeeker->id);
        } elseif ($user->hasRole('company')) {
            // Company melihat lamaran untuk semua lowongan miliknya
            $companyId = $user->company->id;
            $query->whereHas('jobPosting', function ($q) use ($companyId) {
                $q->where('id_company', $companyId);
            });
        }

        $applications = $query->latest()->paginate(10);
        return view('applications.index', compact('applications'));
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
        $user = Auth::user();
        if (!$user->hasRole('user') || !$user->jobSeeker) {
            abort(403, 'Hanya pencari kerja yang bisa melamar.');
        }

        $request->validate([
            'cover_letter' => 'nullable|string',
        ]);

        // Mencegah duplikat lamaran
        $existingApplication = Application::where('job_seeker_id', $user->jobSeeker->id)
            ->where('job_posting_id', $jobPosting->id)
            ->exists();

        if ($existingApplication) {
            return back()->with('error', 'Anda sudah melamar pada lowongan ini.');
        }

        Application::create([
            'job_seeker_id' => $user->jobSeeker->id,
            'job_posting_id' => $jobPosting->id, 
            'cover_letter' => $request->cover_letter,
            'status' => 'applied'
        ]);

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
        $user = Auth::user();

        if ($user->hasRole('user') && $user->jobSeeker?->id === $application->job_seeker_id) {
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
        $user = Auth::user();

        // Otorisasi: Hanya user pemilik atau admin yang bisa menghapus
        if (
            ($user->hasRole('user') && $user->jobSeeker?->id !== $application->job_seeker_id) &&
            !$user->hasRole('admin')
        ) {
            abort(403);
        }

        $application->delete();
        return redirect()->route('applications.index')->with('success', 'Lamaran berhasil dihapus.');
    }
}
