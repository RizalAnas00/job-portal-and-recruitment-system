@php
    $currentUser = auth()->user();
    $matchedSkills = $jobPosting->getMatchedSkillsWith($currentUser);
    $matchCount = count($matchedSkills);
@endphp

<div class="max-w-screen-2xl mx-auto py-10">

    <div class="rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-900 transition-all duration-300 relative">

        {{-- STATUS DEADLINE (DESKTOP) --}}
        {{-- Menggunakan Accessor: $jobPosting->is_expired --}}
        <div class="hidden lg:block absolute top-6 right-6 text-sm font-semibold tracking-wide
            @if($jobPosting->is_expired)     text-red-500 dark:text-red-400 italic
            @elseif($jobPosting->is_urgent)  text-yellow-400
            @else                            text-green-500
            @endif">

            @if($jobPosting->is_expired)
                Sudah ditutup — {{ $jobPosting->closing_date?->format('d M Y H:i') }}
            @elseif($jobPosting->is_urgent)
                {{-- Menggunakan Accessor: $jobPosting->hours_left --}}
                ⚠ Ditutup dalam {{ round($jobPosting->hours_left) }} jam — {{ $jobPosting->closing_date?->format('d M Y H:i') }}
            @else
                Dibuka hingga {{ $jobPosting->closing_date?->format('d M Y H:i') ?? '-' }}
            @endif
        </div>


        {{-- TITLE --}}
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight">
            {{ $jobPosting->job_title }}
        </h1>

        {{-- STATUS DEADLINE (MOBILE) --}}
        <div class="lg:hidden mt-2 text-sm font-semibold tracking-wide
            @if($jobPosting->is_expired)     text-red-500 dark:text-red-400 italic
            @elseif($jobPosting->is_urgent)  text-yellow-400
            @else                            text-green-500
            @endif">

            @if($jobPosting->is_expired)
                Sudah ditutup — {{ $jobPosting->closing_date?->format('d M Y H:i') }}
            @elseif($jobPosting->is_urgent)
                ⚠ Ditutup dalam {{ round($jobPosting->hours_left) }} jam — {{ $jobPosting->closing_date?->format('d M Y H:i') }}
            @else
                Dibuka hingga {{ $jobPosting->closing_date?->format('d M Y H:i') ?? '-' }}
            @endif
        </div>


        {{-- COMPANY / DETAILS --}}
        <div class="mt-3 flex flex-wrap gap-3 items-center">
            <span class="text-lg px-3 py-1 rounded-lg border font-semibold text-primary-600 dark:text-primary-200 
                         border-primary-400/50 bg-primary-400/10">
                {{ $jobPosting->company->company_name ?? '-' }}
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
            <span class="{{ !$jobPosting->min_salary && !$jobPosting->max_salary 
                ? 'text-gray-500 dark:text-gray-400 italic' 
                : 'text-2xl font-bold tracking-wide px-3 py-1 rounded-xl border border-primary-500 dark:border-primary-400 text-primary-700 dark:text-primary-300' 
            }}">

                @if (!$jobPosting->min_salary && !$jobPosting->max_salary)
                    Gaji Tidak Dilampirkan
                @else
                    Rp {{ number_format($jobPosting->min_salary, 0, ',', '.') }}
                    @if ($jobPosting->max_salary)
                        - Rp {{ number_format($jobPosting->max_salary, 0, ',', '.') }}
                    @endif
                @endif
            </span>
        </div>


        {{-- DESCRIPTION --}}
        <div class="prose dark:prose-invert mt-7 leading-relaxed text-gray-800 dark:text-gray-200">
            {!! nl2br(e($jobPosting->job_description)) !!}
        </div>

        {{-- APPLY SECTION --}}
        @can('application.create')
            {{-- Menggunakan Method: isOwnedBy($currentUser) --}}
            @if(auth()->check() && !$jobPosting->isOwnedBy($currentUser))
                <div class="max-w-screen-2xl mx-auto mt-14 border-t border-gray-500 pt-10">
        
                    @if($jobPosting->is_expired)
                        <p class="text-center text-red-500 font-semibold text-lg">⚠ Lowongan sudah ditutup.</p>
        
                    {{-- Menggunakan Method: hasApplicant($currentUser) --}}
                    @elseif($jobPosting->hasApplicant($currentUser))
                        <p class="text-center text-green-600 dark:text-green-300 font-semibold text-lg">
                            Anda sudah melamar pekerjaan ini
                        </p>
        
                    @else
                        <a href="{{ route('user.applications.create', $jobPosting) }}"
                           class="mx-auto block w-full max-w-screen-2xl text-center px-6 py-3 rounded-lg 
                                  bg-primary-600 hover:bg-primary-700 text-white font-semibold 
                                  shadow-md transition">
                            Lamar Pekerjaan
                        </a>
                    @endif
        
                </div>
            @endif
        @endcan

        {{-- SKILLS --}}
        <div class="mt-10 space-y-6">
            <h3 class="font-bold pt-6 border-t border-gray-500 text-gray-900 dark:text-white mb-3 text-lg">
                Skills
            </h3>

            <div class="flex flex-wrap gap-2 mb-2">
                @foreach($jobPosting->skills as $skill)
                    <span class="px-4 py-1.5 rounded-full text-sm font-semibold border
                        {{-- Menggunakan variable $matchedSkills yang sudah didefinisikan di @php atas --}}
                        @if(in_array($skill->skill_name, $matchedSkills))
                            bg-primary-600 dark:bg-primary-600/20 text-white border-primary-700 shadow
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
                {{ $jobPosting->posted_date?->format('d F Y H:i') ?? '-' }}
            </p>
        </div>


        <div class="mt-10 flex items-center justify-between">

            <a href="{{ url()->previous() }}" 
            class="px-5 py-2 rounded-lg font-semibold shadow-sm text-white 
                   bg-gray-700 hover:bg-gray-900 dark:bg-gray-600 dark:hover:bg-gray-500">
                ← Back
            </a>

            @if ($jobPosting->isOwnedBy($currentUser) && !$jobPosting->hasApplicants())
                <a href="{{ route('job-postings.edit', $jobPosting) }}"
                class="px-5 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white shadow-md
                       hover:bg-primary-700 transition">
                    {{ __('Edit Job Offer') }}
                </a>
            @elseif($jobPosting->isOwnedBy($currentUser) && $jobPosting->hasApplicants())
                
                <div class="flex flex-col items-end gap-3">
                    
                    <form method="POST" action="{{ route('company.job-postings.update-status', $jobPosting) }}" 
                        class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 p-1 rounded-lg border dark:border-gray-700">
                        @csrf
                        @method('PATCH')
                        
                        <label for="status" class="text-xs font-semibold text-gray-500 uppercase px-2">Status:</label>
                        
                        <x-select-field-one
                            name="status"
                            :options="[
                                'archived' => 'Arsipkan',
                                'draft' => 'Draft',
                                'open' => 'Buka',
                                'closed' => 'Tutup',
                                'paused' => 'Jeda',
                            ]"
                            :selected="$jobPosting->status"
                            spawn_in="up"
                            class="!py-1.5 !text-sm border-none focus:ring-0 bg-transparent min-w-[100px]"
                        />

                        <button type="submit"
                            class="px-4 py-1.5 text-xs font-bold rounded-md bg-primary-600 text-white hover:bg-primary-700 transition shadow-sm">
                            Update
                        </button>
                    </form>

                    <div class="flex flex-col items-end">
                        <button disabled class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500 opacity-75">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Edit Terkunci
                        </button>
                        
                        <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400 max-w-[250px] text-right leading-tight">
                            Lowongan dengan pelamar aktif <strong>(bukan accepted/rejected)</strong> tidak dapat diubah detailnya demi integritas data.
                        </p>
                    </div>

                </div>
            @endif

        </div>

    </div>
</div>