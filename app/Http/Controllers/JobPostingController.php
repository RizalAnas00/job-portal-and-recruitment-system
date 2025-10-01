<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $user = Auth::user();
        $query = JobPosting::with('company', 'skills')->latest();

        // Jika yang login adalah 'company', tampilkan hanya lowongan milik mereka.
        if ($user->hasRole('company') && $user->company) {
            $query->where('company_id', $user->company->id);
        }

        $jobPostings = $query->paginate(10);
        return view('job_postings.index', compact('jobPostings'));
    }

    public function show(JobPosting $jobPosting)
    {
        // Eager load relasi untuk ditampilkan di halaman detail
        $jobPosting->load('company', 'skills');
        return view('job_postings.show', compact('jobPosting'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $skills = Skill::orderBy('skill_name')->get();
        return view('job_postings.create', compact('skills'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('company') || !$user->company) {
            abort(403, 'AKSES DITOLAK: HANYA PERUSAHAAN YANG BISA MEMBUAT LOWONGAN.');
        }

        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'location' => 'required|string|max:255',
            'job_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'internship'])],
            'salary_range' => 'nullable|string|max:100',
            'posted_date' => 'required|date',
            'closing_date' => 'nullable|date|after_or_equal:posted_date',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id' // Validasi setiap ID skill
        ]);

        $jobPosting = $user->company->jobPostings()->create($validated);

        // Lampirkan skills ke job posting yang baru dibuat
        if ($request->has('skills')) {
            $jobPosting->skills()->attach($request->skills);
        }

        return redirect()->route('job-postings.index')->with('success', 'Lowongan berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobPosting $jobPosting)
    {
        $skills = Skill::orderBy('skill_name')->get();
        return view('job_postings.edit', compact('jobPosting', 'skills'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobPosting $jobPosting)
    {
        $user = Auth::user();

        // Otorisasi: Hanya admin atau pemilik perusahaan yang bisa mengupdate
        if (!$user->hasRole('admin') && !($user->hasRole('company') && $user->company?->id === $jobPosting->company_id)) {
            abort(403, 'AKSES DITOLAK');
        }

        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'location' => 'required|string|max:255',
            'job_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'internship'])],
            'salary_range' => 'nullable|string|max:100',
            'posted_date' => 'required|date',
            'closing_date' => 'nullable|date|after_or_equal:posted_date',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id'
        ]);

        $jobPosting->update($validated);

        // 'sync' akan memperbarui relasi: menghapus yang tidak dipilih dan menambah yang baru.
        if ($request->has('skills')) {
            $jobPosting->skills()->sync($request->skills);
        } else {
            // Jika tidak ada skill yang dikirim, hapus semua relasi skill
            $jobPosting->skills()->detach();
        }

        return redirect()->route('job-postings.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPosting $jobPosting)
    {
        $user = Auth::user();

        // Otorisasi: Hanya admin atau pemilik perusahaan yang bisa menghapus
        if (!$user->hasRole('admin') && !($user->hasRole('company') && $user->company?->id === $jobPosting->company_id)) {
            abort(403, 'AKSES DITOLAK');
        }

        $jobPosting->delete();
        return redirect()->route('job-postings.index')->with('success', 'Lowongan berhasil dihapus.');
    }
}
