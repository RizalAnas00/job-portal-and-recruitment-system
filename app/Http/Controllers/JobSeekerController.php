<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobSeekerController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        abort_unless($user->hasRole('user'), 403);
        if ($user->jobSeeker) {
            return redirect()->route('user.job-seekers.edit');
        }

        $skills = Skill::orderBy('skill_name')->get();
        $selectedSkillIds = collect();

        return view('job_seekers.create', compact('skills', 'selectedSkillIds'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole('user'), 403);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'profile_summary' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $skills = $data['skills'] ?? [];
        unset($data['skills']);

        $jobSeeker = $user->jobSeeker()->create($data);

        if (!empty($skills)) {
            $jobSeeker->skills()->sync($skills);
        }

        return redirect()->route('dashboard')->with('success', 'Profil pencari kerja berhasil dibuat.');
    }

    public function edit()
    {
        $user = Auth::user();
        abort_unless($user->hasRole('user') && $user->jobSeeker, 403);

        $skills = Skill::orderBy('skill_name')->get();
        $selectedSkillIds = $user->jobSeeker->skills()->pluck('skills.id');

        return view('job_seekers.edit', [
            'jobSeeker' => $user->jobSeeker,
            'skills' => $skills,
            'selectedSkillIds' => $selectedSkillIds,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole('user') && $user->jobSeeker, 403);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'profile_summary' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $skills = $data['skills'] ?? [];
        unset($data['skills']);

        $user->jobSeeker->update($data);
        $user->jobSeeker->skills()->sync($skills);

        return redirect()->route('user.job-seekers.edit')->with('success', 'Profil pencari kerja berhasil diperbarui.');
    }
}