<?php

namespace App\Actions;

use App\Models\JobSeeker;
use App\Models\Resume;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class UploadResume
{
    public function __invoke(JobSeeker $jobSeeker, $file): Resume
    {
        if (!$file) {
            throw new \Exception('File resume tidak ditemukan.');
        }

        validator(['resume' => $file], [
            'resume' => 'required|mimes:pdf|max:2048',
        ])->validate();

        $basePath = 'resumes/user/' . str_replace(' ', '_', strtolower($jobSeeker->first_name . '_' . $jobSeeker->last_name));

        $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $fullPath = $file->storeAs($basePath, $fileName, 'public');

        $parser = new Parser();
        $pdf = $parser->parseFile(Storage::disk('public')->path($fullPath));
        $parsedText = $pdf->getText();

        return Resume::create([
            'job_seeker_id' => $jobSeeker->id,
            'resume_title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_path' => $fullPath,
            'parsed_text' => $parsedText,
        ]);
    }
}
