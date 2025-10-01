<?php

namespace App\Http\Controllers;

use App\Models\JobSeeker;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobSeeker = Auth::user()->jobSeeker;

        if (!$jobSeeker) {
            $resumes = collect();
        } else {
            $resumes = $jobSeeker->resumes()->latest()->paginate(10);
        }

        return view('resumes.index', compact('resumes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('resumes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'resume_title' => 'required|string|max:255',
            'resume_file' => 'required|file|mimes:pdf,doc,docx|max:2048', // Maksimal 2MB
        ]);

        $user = Auth::user();
        $jobSeeker = $user->jobSeeker;

        if (!$jobSeeker) {
            $jobSeeker = JobSeeker::create(['user_id' => $user->id]);
        }

        if ($request->hasFile('resume_file')) {
            $file = $request->file('resume_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            $filePath = $file->storeAs('resumes/' . $user->id, $fileName, 'private');

            Resume::create([
                'job_seeker_id' => $jobSeeker->id,
                'resume_title' => $request->resume_title,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'upload_date' => now(),
            ]);

            return redirect()->route('resumes.index')->with('success', 'Resume berhasil diunggah.');
        }

        return back()->with('error', 'Gagal mengunggah file. Silakan coba lagi.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resume $resume)
    {
        if (Auth::user()->jobSeeker?->id !== $resume->job_seeker_id) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('resumes.edit', compact('resume'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resume $resume)
    {
        if (Auth::user()->jobSeeker?->id !== $resume->job_seeker_id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'resume_title' => 'required|string|max:255',
        ]);

        $resume->update([
            'resume_title' => $request->resume_title,
        ]);

        return redirect()->route('resumes.index')->with('success', 'Judul resume berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resume $resume)
    {
        if (Auth::user()->jobSeeker?->id !== $resume->job_seeker_id) {
            abort(403, 'AKSES DITOLAK');
        }

        if (Storage::disk('private')->exists($resume->file_path)) {
            Storage::disk('private')->delete($resume->file_path);
        }

        $resume->delete();

        return redirect()->route('resumes.index')->with('success', 'Resume berhasil dihapus.');
    }
}
