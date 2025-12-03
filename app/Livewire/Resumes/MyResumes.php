<?php

namespace App\Livewire\Resumes;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyResumes extends Component
{
    public $resumes = [];
    public $selectedResume = null;

    public function mount()
    {
        $user = Auth::user();

        if (!$user || !$user->jobSeeker) {
            abort(404, 'Job Seeker tidak ditemukan.');
        }

        $this->resumes = $user->jobSeeker->resumes()->get();
    }

    public function openResume($resumeId)
    {
        if ($this->selectedResume && $this->selectedResume['id'] == $resumeId) {
            $this->selectedResume = null;
            return;
        }

        $this->selectedResume = collect($this->resumes)->firstWhere('id', $resumeId);
    }

    public function render()
    {
        return view('livewire.resumes.my-resumes');
    }
}
