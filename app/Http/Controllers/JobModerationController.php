<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\JobPosting;
use App\Models\Skill;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class JobModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = JobPosting::with(['company', 'skills', 'location']);

        // Filter by moderation status
        if ($request->filled('moderation_status')) {
            $query->where('moderation_status', $request->moderation_status);
        } else {
            // Default: show pending first
            $query->orderByRaw("FIELD(moderation_status, 'pending', 'approved', 'rejected')");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('job_description', 'like', "%{$search}%")
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $jobs = $query->latest()->paginate(15);

        $stats = [
            'total' => JobPosting::count(),
            'pending' => JobPosting::where('moderation_status', 'pending')->count(),
            'approved' => JobPosting::where('moderation_status', 'approved')->count(),
            'rejected' => JobPosting::where('moderation_status', 'rejected')->count(),
        ];

        return view('admin.job_postings.index', compact('jobs', 'stats'));
    }

    public function show(JobPosting $jobPosting)
    {
        $jobPosting->load(['company.user', 'skills', 'location', 'applications']);
        return view('admin.job_postings.show', compact('jobPosting'));
    }

    public function edit(JobPosting $jobPosting)
    {
        $jobPosting->load(['company', 'skills', 'location']);
        $skills = Skill::orderBy('skill_name')->get();
        $locations = Location::active()->orderBy('name')->get();
        $selectedSkillIds = $jobPosting->skills()->pluck('skills.id');
        
        return view('admin.job_postings.edit', compact('jobPosting', 'skills', 'locations', 'selectedSkillIds'));
    }

    public function update(Request $request, JobPosting $jobPosting)
    {
        $oldValues = $jobPosting->toArray();

        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'job_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'internship', 'temporary', 'freelance', 'remote'])],
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'posted_date' => 'required|date',
            'closing_date' => 'required|date|after:posted_date',
            'status' => ['required', Rule::in(['draft', 'open', 'paused', 'closed', 'archived'])],
            'moderation_status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'rejection_reason' => 'nullable|string|max:500',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $openAt = Carbon::parse($validated['posted_date']);
        $closeAt = Carbon::parse($validated['closing_date']);
        
        // Auto-update status based on dates if moderation is approved
        if ($validated['moderation_status'] === 'approved') {
            $validated['status'] = JobPosting::statusForSchedule($openAt, $closeAt);
        }

        $jobPosting->update($validated);

        // Sync skills
        if (isset($validated['skills'])) {
            $jobPosting->skills()->sync($validated['skills']);
        } else {
            $jobPosting->skills()->detach();
        }

        AuditLog::log(
            'job_posting.updated_by_admin',
            $jobPosting,
            "Job posting '{$jobPosting->job_title}' updated by admin",
            $oldValues,
            $jobPosting->fresh()->toArray()
        );

        return redirect()->route('admin.jobs.moderation.index')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function approve(JobPosting $jobPosting)
    {
        $oldStatus = $jobPosting->moderation_status;
        $jobPosting->update(['moderation_status' => 'approved']);

        // Auto-update status if dates are set
        if ($jobPosting->posted_date && $jobPosting->closing_date) {
            $status = JobPosting::statusForSchedule($jobPosting->posted_date, $jobPosting->closing_date);
            $jobPosting->update(['status' => $status]);
        }

        AuditLog::log(
            'job_posting.approved',
            $jobPosting,
            "Job posting '{$jobPosting->job_title}' approved by admin"
        );

        return back()->with('success', 'Lowongan disetujui.');
    }

    public function reject(Request $request, JobPosting $jobPosting)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        
        $jobPosting->update([
            'moderation_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'status' => 'closed',
        ]);

        AuditLog::log(
            'job_posting.rejected',
            $jobPosting,
            "Job posting '{$jobPosting->job_title}' rejected by admin. Reason: {$request->rejection_reason}"
        );

        return back()->with('success', 'Lowongan ditolak.');
    }

    public function destroy(JobPosting $jobPosting)
    {
        $jobTitle = $jobPosting->job_title;
        $jobPosting->delete();

        AuditLog::log(
            'job_posting.deleted',
            $jobPosting,
            "Job posting '{$jobTitle}' deleted by admin"
        );

        return redirect()->route('admin.jobs.moderation.index')
            ->with('success', "Lowongan '{$jobTitle}' berhasil dihapus.");
    }
}
