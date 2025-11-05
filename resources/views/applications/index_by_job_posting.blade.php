@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Applicants for "{{ $jobPosting->job_title }}"</h1>
        <a href="{{ route('company.job-postings.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-md dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Back to Job Postings
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-4 flex items-center space-x-2">
        <form action="{{ route('company.job-postings.applications.filter', $jobPosting) }}" method="GET" class="flex items-center space-x-2">
            <label for="status-filter" class="text-gray-700 dark:text-gray-300">Filter by Status:</label>
            <select name="status" id="status-filter" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">Filter</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="p-4">
            @forelse($applications as $application)
                <div class="border-b dark:border-gray-700 last:border-b-0 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $application->jobSeeker->first_name ?? 'N/A' }} {{ $application->jobSeeker->last_name ?? '' }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Applied on: {{ $application->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            @php
                                $statusClass = [
                                    'applied' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                    'under_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                    'interview_scheduled' => 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100',
                                    'interviewing' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100',
                                    'offered' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                    'hired' => 'bg-teal-100 text-teal-800 dark:bg-teal-800 dark:text-teal-100',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                ][$application->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                            <a href="{{ route('company.applications.edit', $application) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                Manage
                            </a>
                        </div>
                    </div>
                    <p class="mt-2 text-gray-700 dark:text-gray-300 text-sm">
                        Cover Letter: {{ \Illuminate\Support\Str::limit($application->cover_letter, 150) ?: 'No cover letter provided.' }}
                    </p>
                </div>
            @empty
                <p class="text-gray-700 dark:text-gray-300 p-4">No applicants found for this job posting.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $applications->links() }}
    </div>
</div>
@endsection
