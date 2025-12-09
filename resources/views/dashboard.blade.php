<x-app-layout>
    <x-slot name="breadcrumb">
        Dashboard
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('error'))
                <div class="bg-red-100 dark:bg-red-600/20 border border-red-400 dark:border-red-400 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>  
            @endif

            <div class="bg-gradient-to-r from-primary-500 to-primary-700 rounded-2xl shadow-lg p-8 text-white">
                @php
                    $user = Auth::user();
                    if ($user->hasRole('company') && $user->company) {
                        $displayName = $user->company->company_name;
                    } elseif ($user->hasRole('job_seeker') && $user->jobSeeker) {
                        $displayName = $user->jobSeeker->name;
                    } else {
                        $displayName = $user->email;
                    }
                @endphp

                <h1 class="text-3xl font-bold">
                    Selamat Datang, {{ $displayName }} 👋
                </h1>
                <p class="mt-2 text-primary-100">
                    @if (Auth::user()->hasRole('company') && Auth::user()->company)
                        Kelola lowongan, pantau pelamar, dan lihat performa rekrutmen perusahaan Anda di satu tempat.
                    @elseif (Auth::user()->hasRole('admin'))
                        Anda login sebagai <span class="font-semibold">{{ strtoupper(Auth::user()->getRoleName()) }}</span>
                        dengan email <span class="font-semibold">{{ Auth::user()->email }}</span>.
                    @elseif (Auth::user()->hasRole('user'))
                        Anda login sebagai <span class="font-semibold">{{ strtoupper(Auth::user()->getRoleName()) }}</span>
                        dengan email <span class="font-semibold">{{ Auth::user()->email }}</span>.
                        @if (!Auth::user()->jobSeeker)
                            <a href="{{ route('user.job-seekers.create') }}"
                                class="inline-flex items-center mt-4 px-4 py-2 bg-white text-primary-600 font-bold rounded-lg shadow hover:bg-primary-50 transition">
                                Lengkapi Profil Job Seeker
                            </a>
                        @endif
                    @endif
                </p>
            </div>

            {{-- ============ COMPANY DASHBOARD ============ --}}
            @if (Auth::user()->hasRole('company'))
                <x-dashboards.company 
                    :jobPostingsCount="$jobPostingsCount"
                    :totalApplicantsCount="$totalApplicantsCount"
                    :hiredCandidatesCount="$hiredCandidatesCount"
                    :jobSeekerApplyAt="$jobSeekerApplyAt"
                    :activeJobPostingsCount="$activeJobPostingsCount"
                    :chartData="$chartData ?? []"
                />
            @endif
            {{-- ============ END COMPANY DASHBOARD ============ --}}


            {{-- ============ USER DASHBOARD ============ --}}
            @if (Auth::user()->hasRole('user'))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-xl mt-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                            Kelola Profil Job Seeker
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                            Pastikan profil Anda selalu ter-update agar perusahaan dapat mengenal Anda lebih baik.
                        </p>

                        <div class="flex flex-wrap gap-4">
                            @if (Auth::user()->jobSeeker)
                                <a href="{{ route('user.job-seekers.edit') }}"
                                    class="bg-primary-700 hover:bg-primary-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                                    Edit Profil Job Seeker
                                </a>
                            @else
                                <a href="{{ route('user.job-seekers.create') }}"
                                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                                    Lengkapi Profil Job Seeker
                                </a>
                            @endif

                            <a href="{{ route('user.applications.index') }}"
                                class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2.5 px-4 rounded-lg transition">
                                Lihat Lamaran Saya
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            {{-- ============ END USER DASHBOARD ============ --}}

        </div>
    </div>
</x-app-layout>
