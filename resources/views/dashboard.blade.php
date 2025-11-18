<x-app-layout>
    <x-slot name="breadcrumb">
        Dashboard
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-8 text-white">
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
                <p class="mt-2 text-blue-100">
                    @if (Auth::user()->hasRole('company'))
                        Kelola lowongan, pantau pelamar, dan lihat performa rekrutmen perusahaan Anda di satu tempat.
                    @elseif (Auth::user()->hasRole('admin'))
                        Anda login sebagai <span class="font-semibold">{{ strtoupper(Auth::user()->getRoleName()) }}</span>
                        dengan email <span class="font-semibold">{{ Auth::user()->email }}</span>.
                    @elseif (Auth::user()->hasRole('user'))
                        Anda login sebagai <span class="font-semibold">{{ strtoupper(Auth::user()->getRoleName()) }}</span>
                        dengan email <span class="font-semibold">{{ Auth::user()->email }}</span>.
                        @if (!Auth::user()->jobSeeker)
                            <a href="{{ route('user.job-seekers.create') }}"
                                class="inline-flex items-center mt-4 px-4 py-2 bg-white text-blue-600 font-bold rounded-lg shadow hover:bg-blue-50 transition">
                                Lengkapi Profil Job Seeker
                            </a>
                        @endif
                    @endif
                </p>
            </div>

            {{-- ============ COMPANY DASHBOARD ONLY ============ --}}
            @if (Auth::user()->hasRole('company'))

                {{-- Statistik Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
                        <p class="text-gray-500 text-sm">Total Lowongan</p>
                        <h3 class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                            @if ($jobPostingsCount)
                                {{ $jobPostingsCount }}
                            @else
                                -
                            @endif
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Lowongan yang sudah diposting</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
                        <p class="text-gray-500 text-sm">Total Pelamar</p>
                        <h3 class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                            @if ($totalApplicantsCount)
                                {{ $totalApplicantsCount }}
                            @else
                                -
                            @endif
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Pelamar dari semua lowongan</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
                        <p class="text-gray-500 text-sm">Diterima</p>
                        <h3 class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">
                            @if ($hiredCandidatesCount)
                                {{ $hiredCandidatesCount }}
                            @else
                                -
                            @endif
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Pelamar yang sudah direkrut</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
                        <p class="text-gray-500 text-sm">Lowongan Aktif</p>
                        <h3 class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-2">
                            @if ($activeJobPostingsCount)
                                {{ $activeJobPostingsCount }}
                            @else
                                -
                            @endif
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Masih terbuka untuk pelamar</p>
                    </div>
                </div>

                {{-- Grafik Statistik --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Statistik Pelamar Bulanan</h3>
                    <div id="chart"></div>
                </div>

                {{-- Aksi Perusahaan --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-xl mt-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                            Langkah Selanjutnya
                        </h3>

                        @if (Auth::user()->company)
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('company.job-postings.index') }}"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                                    Kelola Lowongan
                                </a>
                                <a href="{{ route('company.job-postings.create') }}"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                                    Tambah Lowongan Baru
                                </a>
                            </div>
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                Lihat, buat, atau edit lowongan pekerjaan yang diposting oleh perusahaan Anda.
                            </p>
                        @else
                            <a href="{{ route('companies.create') }}"
                                class="inline-block bg-orange-500 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                                Buat Profil Perusahaan
                            </a>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Lengkapi profil perusahaan Anda untuk mulai memposting lowongan pekerjaan.
                            </p>
                        @endif
                    </div>
                </div>

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
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
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

    {{-- ApexCharts Script (untuk company saja) --}}
    @if (Auth::user()->hasRole('company'))
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
                const options = {
                    chart: {
                        type: 'line',
                        height: 300,
                        toolbar: { show: false }
                    },
                    series: [{
                        name: 'Jumlah Pelamar',
                        data: [10, 25, 40, 60, 45, 70, 90, 120, 140, 130, 110, 150]
                    }],
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
                    },
                    colors: ['#2563eb'],
                    stroke: { curve: 'smooth', width: 3 },
                    grid: { borderColor: '#e5e7eb' },
                    theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' }
                };
                const chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            </script>
        @endpush
    @endif

</x-app-layout>
