@props(['job'])

@php
    $applied = $job->hasApplied();
@endphp


<div class="rounded-xl shadow-md hover:shadow-lg transition overflow-hidden flex flex-col h-full
    border {{ $applied ? 'border-green-500 bg-green-50/60 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}">
    @if($applied)
        <span class="mx-2 mt-2 px-2 py-2 font-medium text-sm text-center rounded-lg bg-green-300 text-green-900 dark:bg-green-700/40 dark:text-green-300">
            Sudah Melamar
        </span>
    @endif

    <!-- Header -->
    <div class="flex flex-col border-b border-gray-100 dark:border-gray-700">
        <div class="px-4 pt-4 flex items-center gap-3">
            @if ($job->company && $job->company->logo_path)
                <img src="{{ $job->company->logo_path }}" alt="{{ $job->company->company_name }}" class="max-h-[88px] max-w-[88px] rounded-lg object-cover">
            @else
                <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400">
                    @svg('fluentui-building-20', 'w-6 h-6')
                </div>
            @endif
    
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $job->job_title }}</h3>
                @if (Auth::user()->hasRole('user'))                
                    <p class="text-sm text-gray-500 dark:text-gray-300">{{ $job->company->company_name ?? 'N/A' }}</p>
                @endif
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $job->location ?? 'N/A' }}</p>
                @if ($job->status === 'archived')
                    <span class="px-2 py-1 mt-2 min-w-28 justify-center inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-600/20 dark:border dark:border-red-400 dark:text-red-200">
                        Archived
                    </span>
                @elseif ($job->status === 'draft')
                    <span class="px-2 py-1 mt-2 min-w-28 justify-center inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-600/20 dark:border dark:border-yellow-400 dark:text-yellow-200">
                        Draft
                    </span>
                @endif
            </div>
        </div>
        <div class="px-4 my-2">
        @if (!$job->min_salary && !$job->max_salary)
            <span class="text-gray-500 dark:text-gray-400">Gaji Tidak Dilampirkan</span>
        @else
            <strong class="text-primary-600 dark:text-primary-400 text-xl">
                {{ __('Rp ') }}{{ number_format($job->min_salary, 0, ',', '.') }}
                @if ($job->max_salary)
                    - {{ __('Rp ') }}{{ number_format($job->max_salary, 0, ',', '.') }}
                @endif
            </strong>
        @endif
        </div>
    </div>

    <!-- Body -->
    <div class="p-4 space-y-3 flex-grow">
        <p class="text-gray-700 dark:text-gray-300 text-sm line-clamp-2">
            {{ Str::limit($job->job_description, 120) }}
        </p>

        <div class="flex flex-wrap gap-2 mt-2">
            @forelse ($job->skills->take(3) as $skill)
                <span class="bg-transparent border border-gray-200 dark:border-gray-600 text-primary-700 dark:text-gray-100 text-xs px-2 py-1 rounded-md">
                    {{ $skill->skill_name }}
                </span>
            @empty
                <span class="text-xs text-gray-500">{{ __('No skills listed') }}</span>
            @endforelse
            @if ($job->skills->count() > 3)
                <span class="text-xs text-gray-500">+{{ $job->skills->count() - 3 }} {{ __('more') }}</span>
            @endif
            
        </div>
    </div>

    <!-- Footer -->
    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/40 flex justify-between items-center text-sm border-t border-gray-100 dark:border-gray-700 mt-auto">

        <span class="text-gray-600 dark:text-gray-400">
            {{ ucfirst($job->type ?? 'Full-time') }}
        </span>

        <div class="flex items-center gap-3">

            @if(auth()->check() 
                && auth()->user()->hasRole('company') 
                && auth()->user()->company?->id === $job->company?->id)

                <a href="{{ route('job-postings.edit', $job) }}"
                    class="text-gray-600 dark:text-gray-400 font-normal hover:underline">
                    {{ __('Edit') }}
                </a>
            @endif

            <a href="{{ route('job-postings.show', $job) }}"
            class="text-primary-600 dark:text-primary-400 font-medium hover:underline">
                {{ __('View details') }} →
            </a>
        </div>

    </div>

</div>
