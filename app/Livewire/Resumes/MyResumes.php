<?php

namespace App\Livewire\Resumes;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Actions\UploadResume;

class MyResumes extends Component
{
    use WithFileUploads;

    public $resumes = [];
    public $selectedResume = null;

    public $file;
    public $showUploadModal = false;

    private UploadResume $uploadResumeAction;

    public function boot(UploadResume $uploadResume)
    {
        $this->uploadResumeAction = $uploadResume;
    }

    public function mount()
    {
        $user = Auth::user();

        if (!$user || !$user->jobSeeker) {
            abort(404, 'Job Seeker tidak ditemukan.');
        }

        $this->resumes = $user->jobSeeker->resumes()->get();
    }

    public function openUploadModal()
    {
        $this->reset('file');
        $this->showUploadModal = true;
    }

    public function save()
    {
        $jobSeeker = Auth::user()->jobSeeker;

        $resume = ($this->uploadResumeAction)($jobSeeker, $this->file);

        $this->resumes = $jobSeeker->resumes()->get();

        $this->showUploadModal = false;

        session()->flash('success', 'Resume berhasil diupload!');
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
