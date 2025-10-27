@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold">Daftar Lowongan</h1>
        
        @if(Auth::user()->hasRole('company'))
            <a href="{{ route('company.job-postings.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Buat Lowongan Baru
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <ul class="list-unstyled mt-3 space-y-4">
    @forelse($jobPostings as $job)
        <li class="p-4 border rounded dark:border-gray-700">
            <a href="{{ route('job-postings.show', $job) }}" class="text-lg font-bold text-blue-600 dark:text-blue-400 hover:underline">
                {{ $job->job_title }}
            </a>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ $job->company?->name ?? '-' }} — {{ $job->location }} — {{ $job->job_type }}
            </div>
            <p class="mt-2 text-gray-700 dark:text-gray-300">
                {{ \Illuminate\Support\Str::limit($job->job_description, 200) }}
            </p>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span>Skills: {{ $job->skills->pluck('skill_name')->join(', ') ?: '-' }}</span> |
                <span>Gaji: {{ $job->salary_range ?? '-' }}</span> |
                <span>Tutup: {{ optional($job->closing_date)->format('Y-m-d') ?? '-' }}</span>
            </div>
        </li>
    @empty
        <li class="text-gray-900 dark:text-gray-100">Tidak ada lowongan yang tersedia saat ini.</li>
    @endforelse
    </ul>

    <div class="mt-3">
        {{ $jobPostings->links() }}
    </div>
</div>
@endsection