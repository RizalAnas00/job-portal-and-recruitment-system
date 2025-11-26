@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0">
        <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold">Daftar Lowongan</h1>
        <div class="flex items-center space-x-2">
            <form action="{{ route('job-postings.index') }}" method="GET" class="flex items-center space-x-2">
                <select name="status" id="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-md">Filter</button>
            </form>
            @if(Auth::user()->hasRole('company'))
                <a href="{{ route('company.job-postings.create') }}" class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-md">
                    Buat Lowongan Baru
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <ul class="list-unstyled mt-3 space-y-4">
    @forelse($jobPostings as $job)
        <li class="p-4 border rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-start">
                <div>
                    <a href="{{ route('job-postings.show', $job) }}" class="text-lg font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        {{ $job->job_title }}
                    </a>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $job->company?->company_name ?? '-' }} — {{ $job->location }} — {{ str_replace('_', ' ', $job->job_type) }}
                    </div>
                </div>
                <div class="flex-shrink-0">
                    @if($job->status === 'open')
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            Open
                        </span>
                    @else
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                            Closed
                        </span>
                    @endif
                </div>
            </div>
            <p class="mt-2 text-gray-700 dark:text-gray-300">
                {{ \Illuminate\Support\Str::limit($job->job_description, 200) }}
            </p>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span>Skills: {{ $job->skills->pluck('skill_name')->join(', ') ?: '-' }}</span> |
                <span>Gaji: {{ $job->salary_range ?? '-' }}</span> |
                <span>Buka: {{ optional($job->posted_date)->format('d M Y H:i') ?? '-' }}</span> |
                <span>Tutup: {{ optional($job->closing_date)->format('d M Y H:i') ?? '-' }}</span>
            </div>

            @if(Auth::user()->hasRole('company') && Auth::user()->company?->id === $job->id_company || Auth::user()->hasRole('admin'))
                <div class="mt-4 flex items-center space-x-2">
                    <a href="{{ route('company.job-postings.edit', $job) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                        Edit
                    </a>
                    <form action="{{ route('company.job-postings.destroy', $job) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this job posting?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-500 dark:hover:bg-red-600">
                            Delete
                        </button>
                    </form>
                    <a href="{{ route('company.job-postings.applications.index', $job) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        View Applicants
                    </a>
                    <form action="{{ route('company.job-postings.update-status', $job) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $job->status === 'open' ? 'closed' : 'open' }}">
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white {{ $job->status === 'open' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-400 dark:hover:bg-yellow-500">
                            {{ $job->status === 'open' ? 'Close Job' : 'Open Job' }}
                        </button>
                    </form>
                </div>
            @endif
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
