<?php

namespace App\Http\Controllers;

use App\Models\JobSeeker;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Util\PHP\Job;

class JobSeekerController extends Controller
{
    public function create()
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        abort_unless($user->hasRole('user'), 403);

        if ($user->jobSeeker) {
            return redirect()->route('user.job-seekers.edit', $user->jobSeeker);
        }

        $skills = Skill::orderBy('skill_name')->get();
        $selectedSkillIds = collect();

        return view('job_seekers.create', compact('skills', 'selectedSkillIds'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User */
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
            'profile_picture' => 'nullable|image|max:2048', 
        ]);

        if ($request->hasFile('profile_picture')) {
            $photo_path = $request->file('profile_picture')->store('job_seekers/profile_pictures', 'public');
            $data['profile_picture_path'] = $photo_path;
        }

        unset($data['profile_picture']);

        $skills = $data['skills'] ?? [];
        unset($data['skills']);

        $jobSeeker = $user->jobSeeker()->create($data);

        if (!empty($skills)) {
            $jobSeeker->skills()->sync($skills);
        }

        return redirect()->route('dashboard')->with('success', 'Profil pencari kerja berhasil dibuat.');
    }

    public function edit(JobSeeker $jobSeeker)
    {   
        /** @var \App\Models\User */
        $user = Auth::user();

        $isOwner = $user->jobSeeker?->id === $jobSeeker->id;
        $isAdmin = $user->hasRole('admin');

        if (! $isOwner && ! $isAdmin) {
            abort(403);
        }

        $skills = Skill::orderBy('skill_name')->get();
        
        $selectedSkillIds = $jobSeeker->skills()->pluck('skills.id');

        return view('job_seekers.edit', [
            'jobSeeker' => $jobSeeker, 
            'skills' => $skills,
            'selectedSkillIds' => $selectedSkillIds,
        ]);
    }

    public function update(Request $request, JobSeeker $jobSeeker)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $isOwner = $user->jobSeeker?->id === $jobSeeker->id;
        $isAdmin = $user->hasRole('admin');

        if (! $isOwner && ! $isAdmin) {
            abort(403);
        }

        $request->validate([
            'profile_picture' => 'nullable|image|max:2048',
        ]);
        
        $photo_file = $request->file('profile_picture');
        if ($photo_file) {
            $photo_path = $photo_file->store('job_seekers/profile_pictures', 'public');
            $request->merge(['profile_picture_path' => $photo_path]);
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'profile_summary' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
            'profile_picture_path' => 'nullable|string|max:255',
        ]);
        

        $skills = $data['skills'] ?? [];
        unset($data['skills']);

        $user->jobSeeker->update($data);
        $user->jobSeeker->skills()->sync($skills);

        return redirect()->route('user.job-seekers.edit', $jobSeeker)->with('success', 'Profil pencari kerja berhasil diperbarui.');
    }
}