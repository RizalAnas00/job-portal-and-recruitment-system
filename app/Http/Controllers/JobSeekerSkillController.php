<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\JobSeeker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobSeekerSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobSeeker = Auth::user()->jobSeeker;
        $mySkills = $jobSeeker ? $jobSeeker->skills()->get() : collect();
        $allSkills = Skill::all(); // Untuk dropdown/pilihan

        return view('job_seeker.skills.index', compact('mySkills', 'allSkills'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'skill_id' => 'required|exists:skills,id',
        ]);

        $jobSeeker = Auth::user()->jobSeeker;
        if (!$jobSeeker) {
            // Buat profil job seeker jika belum ada
            $jobSeeker = JobSeeker::create(['user_id' => Auth::id()]);
        }

        // syncWithoutDetaching untuk menambah tanpa menghapus yang sudah ada
        $jobSeeker->skills()->syncWithoutDetaching($request->skill_id);

        return redirect()->route('job-seeker-skills.index')->with('success', 'Skill berhasil ditambahkan.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $jobSeeker = Auth::user()->jobSeeker;

        if ($jobSeeker) {
            $jobSeeker->skills()->detach($skill->id);
            return redirect()->route('job-seeker-skills.index')->with('success', 'Skill berhasil dihapus.');
        }

        return redirect()->route('job-seeker-skills.index')->with('error', 'Profil tidak ditemukan.');
    }
}
