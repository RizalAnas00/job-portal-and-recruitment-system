<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Interview::with('application.jobPosting', 'application.jobSeeker.user')->latest();

        if ($user->hasRole('company')) {
            // Company hanya bisa melihat interview untuk lowongan mereka
            $companyId = $user->company->id;
            $query->whereHas('application.jobPosting', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        } elseif ($user->hasRole('user')) {
            // User (job seeker) hanya bisa melihat interview miliknya
            $jobSeekerId = $user->jobSeeker->id;
            $query->whereHas('application', function ($q) use ($jobSeekerId) {
                $q->where('job_seeker_id', $jobSeekerId);
            });
        }
        // Admin bisa melihat semua, jadi tidak perlu filter tambahan.

        $interviews = $query->paginate(10);

        return view('interviews.index', compact('interviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Application $application)
    {
        if (Auth::user()->company?->id !== $application->jobPosting->company_id) {
            abort(403, 'AKSES DITOLAK');
        }
        return view('interviews.create', compact('application'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_application' => 'required|exists:applications,id',
            'interviewer_name' => 'required|string|max:255',
            'interview_date' => 'required|date|after:now',
            'interview_type' => ['required', Rule::in(['online', 'offline', 'phone_screen'])],
            'location' => 'required|string|max:255', // Bisa berupa link meeting atau alamat fisik
            'notes' => 'nullable|string',
        ]);

        $application = Application::findOrFail($validated['id_application']);
        // Otorisasi: Pastikan company adalah pemilik lamaran ini
        if (Auth::user()->company?->id !== $application->jobPosting->company_id) {
            abort(403, 'AKSES DITOLAK');
        }

        $interview = Interview::create($validated);
        
        // Update status lamaran menjadi 'interviewing'
        $application->update(['status' => 'interviewing']);

        return redirect()->route('interviews.index')->with('success', 'Jadwal wawancara berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Interview $interview)
    {
        $user = Auth::user();

        // Otorisasi
        $canView = false;
        if ($user->hasRole('admin')) {
            $canView = true;
        } elseif ($user->hasRole('company') && $user->company?->id === $interview->application->jobPosting->company_id) {
            $canView = true;
        } elseif ($user->hasRole('user') && $user->jobSeeker?->id === $interview->application->job_seeker_id) {
            $canView = true;
        }

        if (!$canView) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('interviews.show', compact('interview'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Interview $interview)
    {
        $user = Auth::user();
        // Otorisasi: Hanya admin atau company pemilik yang bisa mengedit
        if (!$user->hasRole('admin') && $user->company?->id !== $interview->application->jobPosting->company_id) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('interviews.edit', compact('interview'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Interview $interview)
    {
        $user = Auth::user();
        // Otorisasi: Hanya admin atau company pemilik yang bisa memperbarui
        if (!$user->hasRole('admin') && $user->company?->id !== $interview->application->jobPosting->company_id) {
            abort(403, 'AKSES DITOLAK');
        }

        $validated = $request->validate([
            'interviewer_name' => 'required|string|max:255',
            'interview_date' => 'required|date|after:now',
            'interview_type' => ['required', Rule::in(['online', 'offline', 'phone_screen'])],
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $interview->update($validated);

        return redirect()->route('interviews.index')->with('success', 'Jadwal wawancara berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Interview $interview)
    {
        $user = Auth::user();
        // Otorisasi: Hanya admin atau company pemilik yang bisa menghapus
        if (!$user->hasRole('admin') && $user->company?->id !== $interview->application->jobPosting->company_id) {
            abort(403, 'AKSES DITOLAK');
        }

        $interview->delete();

        return redirect()->route('interviews.index')->with('success', 'Jadwal wawancara berhasil dibatalkan.');
    }
}
