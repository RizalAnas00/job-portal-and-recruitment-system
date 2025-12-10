@extends('applications.layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Pelamar
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Pekerjaan: <span class="font-semibold">{{ $jobPosting->job_title }}</span>
            </p>
        </div>
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
            ← {{ __('Back') }}
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 p-4 mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 dark:text-green-200">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="mb-8 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="{{ route('company.job-postings.applications.filter', $jobPosting) }}" method="GET" class="flex flex-wrap items-center gap-3">
            <label for="status-filter" class="font-medium text-gray-700 dark:text-gray-300">Filter by Status:</label>
            <div class="relative min-w-48">
                <x-select-field-one
                    id="status-filter"
                    name="status"
                    :options="[
                        'all' => 'Semua',
                        'pending' => 'Sedang Diproses',
                        'reviewed' => 'Ditinjau',
                        'interview_scheduled' => 'Wawancara Dijadwalkan',
                        'interviewing' => 'Sedang Wawancara',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                    ]"
                    :selected="request('status', 'all')"  
                />
                {{-- <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                    @svg('carbon-chevron-down', 'h-4 w-4')
                </div> --}}
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring ring-primary-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                Filter
            </button>
        </form>
    </div>

    {{-- Applicants Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($applications as $application)
            {{-- Individual Application Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                
                {{-- Card Header & Body (Grow to fill space) --}}
                <div class="p-4 flex-grow">
                    <div class="flex items-start space-x-4 mb-4">
                        {{-- Profile Photo with Fallback --}}
                        <div class="flex-shrink-0">
                            @if($application->jobSeeker->profile_picture_path)
                                <img class="h-16 w-16 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700 shadow-sm" 
                                     src="{{ asset($application->jobSeeker->profile_picture_path) }}" 
                                     alt="{{ $application->jobSeeker->first_name }}">
                            @else
                                {{-- Default Placeholder Icon if no photo exists --}}
                                <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-2 border-gray-300 dark:border-gray-600 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Applicant Name & Date --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 truncate">
                                {{ $application->jobSeeker->first_name ?? 'N/A' }} {{ $application->jobSeeker->last_name ?? '' }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Applied: {{ $application->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Cover Letter Preview --}}
                    <div class="mt-2">
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Cover Letter Draft</h4>
                        <p class="text-gray-700 dark:text-gray-300 text-sm line-clamp-5 italic bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700/50">
                            "{{ \Illuminate\Support\Str::limit($application->cover_letter, 100) ?: 'No cover letter provided.' }}"
                        </p>
                    </div>
                </div>

                {{-- Card Footer (Status & Action) --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center mt-auto">
                    
                    @php
                        $statusClass = [
                            'applied' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 border border-blue-200 dark:border-blue-800',
                            'reviewed' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800',
                            'interview_scheduled' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200 border border-purple-200 dark:border-purple-800',
                            'interviewing' => 'bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200 border border-primary-200 dark:border-primary-800',
                            'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200 border border-green-200 dark:border-green-800',
                            'hired' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-200 border border-teal-200 dark:border-teal-800',
                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200 border border-red-200 dark:border-red-800',
                        ][$application->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600';
                    @endphp
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                    </span>

                    {{-- Manage Button --}}
                    <a href="{{ route('company.applications.show', $application) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Manage Review
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl shadow-md p-12 text-center border border-gray-100 dark:border-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100">No Applicants Found</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-2">There are no applications matching your criteria at this time.</p>
                @if(request('status') && request('status') !== 'all')
                 <a href="{{ route('company.job-postings.applications.index', $jobPosting) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition-colors">
                    Clear Filters
                 </a>
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $applications->links() }}
    </div>
</div>
@endsection