<x-app-layout>
    <x-slot name="breadcrumb">
        Analytics Dashboard
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-3 lg:px-5">
            <div class="pb-2 text-gray-100">
                <div class="mb-5">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Analytics Dashboard</h1>
                    
                    <!-- Period Filter -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
                        <form method="GET" action="{{ route('admin.analytics.index') }}" class="flex gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode</label>
                                <select name="period" 
                                    class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                    onchange="this.form.submit()">
                                    <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Hari Ini</option>
                                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                                </select>
                            </div>
                            <a href="{{ route('admin.analytics.export', ['type' => 'users']) }}"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                                Export Data
                            </a>
                        </form>
                    </div>
                </div>

                <!-- User Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Pengguna</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($userStats['total']) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Baru: {{ $userStats['new_this_period'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pengguna Aktif</div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($userStats['active']) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Job Seeker</div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($userStats['job_seekers']) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Company</div>
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($userStats['companies']) }}</div>
                    </div>
                </div>

                <!-- Job Posting Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Lowongan</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($jobStats['total']) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Baru: {{ $jobStats['new_this_period'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Lowongan Aktif</div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($jobStats['active']) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pending Moderation</div>
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($jobStats['pending']) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Lowongan Ditutup</div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($jobStats['closed']) }}</div>
                    </div>
                </div>

                <!-- Application Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Lamaran</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($applicationStats['total']) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Baru: {{ $applicationStats['new_this_period'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Sedang Interview</div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($applicationStats['interviewing']) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Diterima</div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($applicationStats['hired']) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Ditolak</div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($applicationStats['rejected']) }}</div>
                    </div>
                </div>

                <!-- Conversion Rates -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Conversion Rates</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Apply → Interview</div>
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $conversionRates['apply_to_interview'] }}%</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Interview → Hire</div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $conversionRates['interview_to_hire'] }}%</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Apply → Hire</div>
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $conversionRates['apply_to_hire'] }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Financial Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Financial Statistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($financialStats['total_revenue'], 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Revenue (Periode)</div>
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($financialStats['revenue_this_period'], 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Pending Payments</div>
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($financialStats['pending_payments']) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Active Subscriptions</div>
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($financialStats['active_subscriptions']) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Lowongan Terbaru</h2>
                        <div class="space-y-3">
                            @forelse($recentJobs as $job)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $job->job_title }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $job->company->company_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        @if($job->posted_date)
                                            {{ $job->posted_date->diffForHumans() }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada lowongan terbaru</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Lamaran Terbaru</h2>
                        <div class="space-y-3">
                            @forelse($recentApplications as $app)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <div class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $app->jobSeeker ? ($app->jobSeeker->first_name . ' ' . $app->jobSeeker->last_name) : 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $app->jobPosting->job_title ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        @if($app->application_date)
                                            {{ \Carbon\Carbon::parse($app->application_date)->diffForHumans() }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada lamaran terbaru</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

