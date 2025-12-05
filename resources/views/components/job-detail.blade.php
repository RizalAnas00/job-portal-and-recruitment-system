@php
    $userSkills = auth()->user()->jobSeeker?->skills->pluck('skill_name')->toArray() ?? [];

    $deadline = $jobPosting->closing_date;
    $isExpired = $deadline && now()->greaterThan($deadline);
    $hoursLeft = $deadline ? now()->diffInHours($deadline, false) : null;
    $isUrgent  = !$isExpired && $hoursLeft !== null && $hoursLeft <= 24;

    $matchedSkills = array_intersect($userSkills, $jobPosting->skills->pluck('skill_name')->toArray());
    $matchCount = count($matchedSkills);
@endphp

<div class="max-w-screen-2xl mx-auto py-10">

    <div class="rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-900 transition-all duration-300 relative">

        <div class="hidden lg:block absolute top-6 right-6 text-sm font-semibold tracking-wide
            @if($isExpired)      text-red-500 dark:text-red-400 italic
            @elseif($isUrgent)   text-yellow-400
            @else                text-green-500
            @endif">
            @if($isExpired)
                Sudah ditutup — {{ $deadline->format('d M Y H:i') }}
            @elseif($isUrgent)
                ⚠ Ditutup dalam {{ round($hoursLeft) }} jam — {{ $deadline->format('d M Y H:i') }}
            @else
                Dibuka hingga {{ $deadline?->format('d M Y H:i') ?? '-' }}
            @endif
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
            {{ $jobPosting->job_title }}
        </h1>

        {{-- deadline under title for smaller screen --}}
        <div class="lg:hidden mt-2 text-sm font-semibold tracking-wide 
            @if($isExpired)      text-red-500 dark:text-red-400 italic
            @elseif($isUrgent)   text-yellow-400
            @else                text-green-500
            @endif">
            @if($isExpired)
                Sudah ditutup — {{ $deadline->format('d M Y H:i') }}
            @elseif($isUrgent)
                ⚠ Ditutup dalam {{ round($hoursLeft) }} jam — {{ $deadline->format('d M Y H:i') }}
            @else
                Dibuka hingga {{ $deadline?->format('d M Y H:i') ?? '-' }}
            @endif
        </div>

        {{-- COMPANY & DETAILS --}}
        <div class="mt-3 flex flex-wrap gap-3 items-center">

            <span class="text-lg px-3 py-1 rounded-lg border font-semibold text-primary-600 dark:text-primary-200 
                         border-primary-400/50 bg-primary-400/10">
                {{ $jobPosting->company?->company_name ?? '-' }}
            </span>

            <span class="text-gray-700 dark:text-gray-300 flex items-center gap-1">
                📍 <span class="font-medium">{{ $jobPosting->location }}</span>
            </span>

            <span class="text-gray-400">•</span>

            <span class="italic text-gray-600 dark:text-gray-400 capitalize">
                {{ str_replace('_',' ', $jobPosting->job_type) }}
            </span>
        </div>

        {{-- SALARY --}}
        <div class="mt-6">
            <span class="text-2xl font-bold tracking-wide px-3 py-1 rounded-xl
                         border border-primary-500 dark:border-primary-400
                         text-primary-700 dark:text-primary-300">
                @salary($jobPosting->salary_range)
            </span>
        </div>

        {{-- DESCRIPTION --}}
        <div class="prose dark:prose-invert mt-7 leading-relaxed text-gray-800 dark:text-gray-200">
            {!! nl2br(e($jobPosting->job_description)) !!}
        </div>

        {{-- SKILLS --}}
        <div class="mt-10 space-y-6">
            <h3 class="font-bold pt-6 border-t border-gray-500 text-gray-900 dark:text-white mb-3 text-lg">Skills</h3>

            <div class="flex flex-wrap gap-2 mb-2">
                @foreach($jobPosting->skills as $skill)
                    @php $match = in_array($skill->skill_name, $userSkills); @endphp

                    <span class="px-4 py-1.5 rounded-full text-sm font-semibold border
                        @if($match)
                            dark:bg-primary-600/20 bg-primary-600 text-white border-primary-700 shadow
                        @else
                            bg-transparent dark:bg-gray-800 text-gray-800 dark:text-gray-200 
                            border-gray-300 dark:border-gray-600
                        @endif
                    ">
                        {{ $skill->skill_name }}
                    </span>
                @endforeach
            </div>

            @if($matchCount > 0)
                <p class="text-primary-700 dark:text-primary-100 italic text-sm">
                    Anda punya <strong>{{ $matchCount }}</strong> skill yang cocok dengan pekerjaan ini.
                </p>
            @endif

            <p class="text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white">Diunggah pada :</span>
                {{ optional($jobPosting->posted_date)->format('d F Y H:i') ?? '-' }}
            </p>
        </div>

        <div class="mt-10">
            <a href="{{ back()->getTargetUrl() }}" 
               class="px-5 py-2 rounded-lg font-semibold shadow-sm text-white 
                      bg-gray-700 hover:bg-gray-900 dark:bg-gray-600 dark:hover:bg-gray-500">
                ← {{ __('Back') }}
            </a>
        </div>

    </div>
</div>
