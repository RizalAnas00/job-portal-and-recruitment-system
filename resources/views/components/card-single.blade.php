@props(['job'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col h-full">
    <!-- Header -->
    <div class="flex flex-col border-b border-gray-100 dark:border-gray-700">
        <div class="px-4 pt-4 flex items-center gap-3">
            @if ($job->company && $job->company->logo_path)
                <img src="{{ $job->company->logo_path }}" alt="{{ $job->company->company_name }}" class="w-14 h-14 rounded-lg object-cover">
            @else
                <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400">
                    @svg('fluentui-building-20', 'w-6 h-6')
                </div>
            @endif
    
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $job->job_title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300">{{ $job->company->company_name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $job->location ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="px-4 my-2">
        @if (!$job->salary_range)
            <span class="text-gray-500 dark:text-gray-400">Gaji Tidak Dilampirkan</span>
        @else
            <strong class="text-primary-600 dark:text-primary-400 text-xl">
                @salary($job->salary_range)
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
        <a href="{{ route('job-postings.show', $job) }}"
           class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
            {{ __('View details') }} →
        </a>
    </div>
</div>
