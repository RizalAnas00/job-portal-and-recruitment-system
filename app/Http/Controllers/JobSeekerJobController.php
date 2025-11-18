<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobSeekerJobController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        abort_unless($user->hasRole('user'), 403);

        $jobSeeker = $user->jobSeeker;

        if (!$jobSeeker) {
            return redirect()->route('user.job-seekers.create')
                ->with('error', 'Lengkapi profil pencari kerja terlebih dahulu untuk melihat rekomendasi lowongan.');
        }

        JobPosting::refreshScheduledStatuses();
        $skillIds = $jobSeeker->skills()->pluck('skills.id');

        $query = JobPosting::with('company', 'skills')
            ->where('status', 'open');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                    ->orWhere('job_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->filled('job_type') && $request->job_type !== 'all') {
            $query->where('job_type', $request->job_type);
        }

        if ($skillIds->isNotEmpty()) {
            $query->whereHas('skills', function ($q) use ($skillIds) {
                $q->whereIn('skills.id', $skillIds);
            });
        }

        $jobPostings = $query->latest()->paginate(10)->withQueryString();

        $jobTypes = [
            'all' => 'Semua Tipe',
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'temporary' => 'Temporary',
            'freelance' => 'Freelance',
            'remote' => 'Remote',
        ];

        $filters = [
            'q' => $request->input('q'),
            'location' => $request->input('location'),
            'job_type' => $request->input('job_type', 'all'),
        ];

        return view('job_seekers.jobs.index', compact(
            'jobPostings',
            'jobSeeker',
            'skillIds',
            'jobTypes',
            'filters'
        ));
    }
}

