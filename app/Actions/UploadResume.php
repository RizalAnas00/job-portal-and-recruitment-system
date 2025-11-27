<?php

namespace App\Actions;

use App\Models\JobSeeker;
use Illuminate\Http\Request;

class UploadResume
{
    public function __invoke(JobSeeker $jobSeeker, Request $request ): void
    {
        $basePath = 'resumes/';

        $jobSeeker->resume_path = $request->file('resume')->storeAs(
            $basePath,
            'resume_' . $jobSeeker->id . '.' . $request->file('resume')->getClientOriginalExtension(),
            'public'
        );
        
        $jobSeeker->save();
    }
}