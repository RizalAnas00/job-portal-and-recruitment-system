<?php

namespace App\Http\Controllers;

use App\Models\JobSeeker;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobSeekerSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $jobSeeker = $user->jobSeeker;

        if (!$jobSeeker) {
            $jobSeeker = JobSeeker::create(['user_id' => $user->id]);
        }

        $mySkills = $jobSeeker->skills()->orderBy('skill_name')->get();
        $allSkills = Skill::orderBy('skill_name')->get();
        $mySkillIds = $mySkills->pluck('id');

        return view('job_seekers.skills.index', compact('jobSeeker', 'mySkills', 'mySkillIds', 'allSkills'));
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

        return redirect()->route('user.skills.index')->with('success', 'Skill berhasil ditambahkan.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $jobSeeker = Auth::user()->jobSeeker;

        if ($jobSeeker) {
            $jobSeeker->skills()->detach($skill->id);
            return redirect()->route('user.skills.index')->with('success', 'Skill berhasil dihapus.');
        }

        return redirect()->route('user.skills.index')->with('error', 'Profil tidak ditemukan.');
    }

    /**
     * Update job seeker skills in bulk.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $jobSeeker = $user->jobSeeker ?? JobSeeker::create(['user_id' => $user->id]);

        $validated = $request->validate([
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $jobSeeker->skills()->sync($validated['skills'] ?? []);

        return redirect()->route('user.skills.index')->with('success', 'Daftar skill berhasil diperbarui.');
    }
}
