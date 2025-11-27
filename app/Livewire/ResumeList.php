<?php

namespace App\Livewire;

use App\Models\JobPosting;
use App\Models\JobSeeker;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ResumeList extends Component
{
    public $jobPosting;
    public $applicants;

    public function mount($jobPostingId)
    {
        $this->jobPosting = JobPosting::findOrFail($jobPostingId);

        $this->applicants = $this->jobPosting->applications()->with('jobSeeker.resumes')->get();
    }

    public function render()
    {
        return view('livewire.resume-list');
    }
}
