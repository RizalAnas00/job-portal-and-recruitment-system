@extends('applications.layout')

@section('content')
    {{-- Notifikasi Accepted / Hired --}}
    @if ($application->status === 'accepted' || $application->status === 'hired')
        <div class="bg-green-100 dark:bg-green-900/30 text-center border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 mb-8 rounded-xl relative shadow-sm" role="alert">
            <strong class="font-bold">Selamat!</strong>
            <span class="block sm:inline"> Lamaran Anda telah diterima di {{ $application->jobPosting->company->company_name }}</span>
        </div>
    @endif
    
    {{-- Tombol Back --}}
    <div class="mb-6">
        <a href="{{ route('user.applications.index') }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg font-medium text-sm shadow-sm text-white 
                  bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('Back') }}
        </a>
    </div>

    <div class="max-w-screen-2xl mx-auto space-y-6 pb-10">

        {{-- Job Details Accordion --}}
        <x-accordion-1 title="{{ __('Job Details') }}" open="true">
            <x-job-detail :jobPosting="$application->jobPosting" />
        </x-accordion-1>

        {{-- Milestone / Progress Section --}}
        <x-accordion-1 title="{{ __('Status Lamaran') }}" open="true">
            @if(auth()->user()->hasRole('user'))
            
                @php
                    // 1. Definisi Steps (Struktur Baru: Pending sebelum Review, Offered dihapus)
                    $steps = [
                        'applied'   => ['label' => 'Applied',   'icon' => 'ionicon-send'],
                        'pending'   => ['label' => 'Pending',   'icon' => 'ionicon-time-outline'],     // Step Baru
                        'review'    => ['label' => 'Review',    'icon' => 'ionicon-search-outline'],
                        'interview' => ['label' => 'Interview', 'icon' => 'ionicon-people-outline'],
                        'accepted'  => ['label' => 'Accepted',  'icon' => 'ionicon-checkmark-circle-outline'], // Final Step
                    ];

                    $status = $application->status;

                    // 2. Tentukan Index Step Aktif (Logic Baru)
                    $activeStep = match(true) {
                        $status === 'applied' => 0,
                        
                        $status === 'pending' => 1, // Pending punya step sendiri sekarang
                        
                        // Grouping status Review
                        in_array($status, ['review', 'reviewed', 'under_review']) => 2,
                        
                        // Grouping status Interview (termasuk Offered jika masih ada di DB, dianggap tahap interview selesai)
                        in_array($status, ['interview_scheduled', 'interviewing', 'offered']) => 3,
                        
                        // Grouping status Final
                        in_array($status, ['accepted', 'hired']) => 4,
                        
                        $status === 'rejected' => -1,
                        default => 0,
                    };

                    // 3. Helper Warna Text Status
                    $statusColorClass = match(true) {
                        $status === 'applied' => 'text-gray-400',
                        
                        $status === 'pending' => 'text-primary-300', // Warna Pending
                        
                        in_array($status, ['review', 'reviewed', 'under_review']) => 'text-gray-500', // Warna Review
                        
                        in_array($status, ['interview_scheduled', 'interviewing', 'offered']) => 'text-primary-600', // Warna Interview
                        
                        in_array($status, ['accepted', 'hired']) => 'text-teal-500', // Warna Sukses
                        
                        $status === 'rejected' => 'text-red-500',
                        default => 'text-gray-400',
                    };
                    
                    // 4. Warna Border/Ring Lingkaran Aktif
                    $activeRingColor = match(true) {
                        in_array($status, ['accepted', 'hired']) => 'ring-teal-200 border-teal-500 text-teal-500',
                        
                        in_array($status, ['interview_scheduled', 'interviewing', 'offered']) => 'ring-primary-200 border-primary-500 text-primary-500',
                        
                        in_array($status, ['review', 'reviewed', 'under_review']) => 'ring-gray-200 border-gray-400 text-gray-500',
                        
                        $status === 'pending' => 'ring-primary-100 border-primary-300 text-primary-300',
                        
                        default => 'ring-gray-200 border-gray-400 text-gray-400',
                    };
                @endphp

                <div class="p-4 sm:p-6">
                    
                    {{-- KONDISI 1: REJECTED --}}
                    @if($activeStep === -1)
                        <div class="flex flex-col items-center justify-center py-10 bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-200 dark:border-red-800 border-dashed">
                            <div class="h-16 w-16 bg-red-100 dark:bg-red-800 text-red-500 rounded-full flex items-center justify-center mb-4 shadow-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-red-600 dark:text-red-400">Application Rejected</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-2 text-center max-w-md text-sm">
                                Terima kasih atas antusiasme Anda. Mohon maaf, kualifikasi Anda belum sesuai dengan kebutuhan kami saat ini.
                            </p>
                        </div>

                    {{-- KONDISI 2: PROGRESS NORMAL --}}
                    @else
                        <div class="relative">
                            {{-- Desktop Line (Horizontal) --}}
                            <div class="hidden md:block absolute top-5 left-[10%] right-[10%] h-1 bg-gray-200 dark:bg-gray-700 rounded-full -z-0">
                                <div class="h-full bg-teal-500 transition-all duration-1000 rounded-full" 
                                     style="width: {{ $activeStep * 25 }}%">
                                </div>
                            </div>

                            {{-- Steps Container --}}
                            <div class="flex flex-col md:grid md:grid-cols-5 gap-8 md:gap-0">
                                @php $i = 0; @endphp
                                @foreach($steps as $key => $step)
                                    @php
                                        $isCompleted = $i < $activeStep;
                                        $isCurrent   = $i === $activeStep;
                                        $i++;
                                    @endphp

                                    <div class="relative flex md:flex-col items-center md:justify-start group">
                                        
                                        {{-- Mobile Line (Vertical) --}}
                                        @if(!$loop->last)
                                            <div class="md:hidden absolute left-5 top-10 w-0.5 h-full bg-gray-200 dark:bg-gray-700 -z-0"></div>
                                        @endif

                                        {{-- Circle Icon --}}
                                        <div class="relative z-10 flex items-center justify-center w-10 h-10 rounded-full border-2 bg-white dark:bg-gray-800 transition-all duration-500
                                            @if($isCompleted)
                                                bg-teal-500 border-teal-500 text-white shadow-md
                                            @elseif($isCurrent)
                                                {{ $activeRingColor }} ring-4 shadow-lg scale-110
                                            @else
                                                border-gray-300 dark:border-gray-600 text-gray-300 dark:text-gray-600
                                            @endif
                                        ">
                                            @if($isCompleted)
                                                @svg('gmdi-check-o', 'w-6 h-6 text-white')
                                            @else
                                                @svg($step['icon'], $isCurrent ? 'w-5 h-5 ' . $statusColorClass : 'w-5 h-5 text-gray-400')
                                            @endif
                                        </div>

                                        {{-- Text Label --}}
                                        <div class="ml-4 md:ml-0 md:mt-4 md:text-center">
                                            <p class="text-sm font-bold uppercase tracking-wide
                                                {{ $isCompleted ? 'text-teal-600 dark:text-teal-400' : '' }}
                                                {{ $isCurrent ? $statusColorClass : 'text-gray-400 ' }}
                                            ">
                                                {{ $step['label'] }}
                                            </p>
                                            
                                            {{-- Subtext Status --}}
                                            @if($isCurrent)
                                                <span class="text-xs text-white font-semibold px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 mt-1 inline-block {{ $statusColorClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Informasi Detail Bawah --}}
                    <div class="mt-10 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Melamar</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold flex items-center">
                                    <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    {{ $application->created_at->format('d F Y, H:i') }} WIB
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Saat Ini</dt>
                                <dd class="mt-1 text-sm font-bold {{ $statusColorClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </dd>
                            </div>
                            @if($application->cover_letter)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Cover Letter</dt>
                                    <dd class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700 italic">
                                        "{!! nl2br(e($application->cover_letter)) !!}"
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                </div>

            @elseif(auth()->user()->hasRole('company'))
                @include('applications.components.company-detail', ['application' => $application])
            @endif
        </x-accordion-1>

    </div>
@endsection