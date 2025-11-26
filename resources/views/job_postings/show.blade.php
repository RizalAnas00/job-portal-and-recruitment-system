@extends('layouts.app')

@section('content')
<div class="container">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $jobPosting->job_title }}</h1>
        <div class="text-md text-gray-600 dark:text-gray-400 mt-2 mb-4">
            {{ $jobPosting->company?->company_name ?? '-' }} — {{ $jobPosting->location }} — {{ str_replace('_', ' ', $jobPosting->job_type) }}
        </div>

        <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 mb-6">
            {!! nl2br(e($jobPosting->job_description)) !!}
        </div>

        <div class="border-t dark:border-gray-700 pt-4">
            <div class="mb-2">
                <strong class="text-gray-900 dark:text-white">Skills:</strong> 
                <span class="text-gray-700 dark:text-gray-300">{{ $jobPosting->skills->pluck('skill_name')->join(', ') ?: '-' }}</span>
            </div>
            <div class="mb-2">
                <strong class="text-gray-900 dark:text-white">Gaji:</strong> 
                <span class="text-gray-700 dark:text-gray-300">{{ $jobPosting->salary_range ?? '-' }}</span>
            </div>
            <div class="mb-2">
                <strong class="text-gray-900 dark:text-white">Buka:</strong>
                <span class="text-gray-700 dark:text-gray-300">{{ optional($jobPosting->posted_date)->format('d F Y H:i') ?? '-' }}</span>
            </div>
            <div class="mb-2">
                <strong class="text-gray-900 dark:text-white">Tutup:</strong> 
                <span class="text-gray-700 dark:text-gray-300">{{ optional($jobPosting->closing_date)->format('d F Y H:i') ?? '-' }}</span>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('job-postings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection