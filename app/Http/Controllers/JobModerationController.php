<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\Request;

class JobModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        // Logika menampilkan list lowongan untuk admin
        // Berbeda dengan index biasa, di sini admin bisa lihat yang statusnya 'pending'
        $jobs = JobPosting::with('company')
            ->orderByRaw("FIELD(moderation_status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->paginate(10);

        return view('admin.job_postings.index', compact('jobs'));
    }

    public function approve(JobPosting $jobPosting)
    {
        $jobPosting->update(['moderation_status' => 'approved']);
        return back()->with('success', 'Lowongan disetujui.');
    }

    public function reject(Request $request, JobPosting $jobPosting)
    {
        $request->validate(['rejection_reason' => 'required']);
        $jobPosting->update([
            'moderation_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);
        return back()->with('success', 'Lowongan ditolak.');
    }
}
