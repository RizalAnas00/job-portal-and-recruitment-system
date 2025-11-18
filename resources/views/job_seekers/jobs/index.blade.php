<x-app-layout>
    <x-slot name="breadcrumb">
        Rekomendasi Lowongan
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <div class="flex flex-col gap-2 mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Cari Lowongan Sesuai Skill</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Kami menampilkan lowongan dengan kecocokan tertinggi berdasarkan skill di profil Anda. Gunakan filter berikut untuk mempersempit hasil.
                    </p>
                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <x-input-label for="q" value="Kata Kunci" />
                        <x-text-input id="q" name="q" type="text" class="mt-1 block w-full"
                            :value="$filters['q']" placeholder="Contoh: UI Designer" />
                    </div>
                    <div>
                        <x-input-label for="location" value="Lokasi" />
                        <x-text-input id="location" name="location" type="text" class="mt-1 block w-full"
                            :value="$filters['location']" placeholder="Jakarta, Remote, dll." />
                    </div>
                    <div>
                        <x-input-label for="job_type" value="Tipe Pekerjaan" />
                        <select id="job_type" name="job_type"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @foreach ($jobTypes as $value => $label)
                                <option value="{{ $value }}" @selected($filters['job_type'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <x-primary-button class="w-full justify-center">
                            Terapkan Filter
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Lowongan Untuk Anda</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Ditemukan {{ $jobPostings->total() }} lowongan yang cocok dengan skill Anda.
                        </p>
                    </div>
                    <a href="{{ route('user.job-seekers.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                        Perbarui Profil & Skill →
                    </a>
                </div>

                @if ($jobPostings->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada lowongan yang cocok. Coba tambah skill baru atau ubah filter pencarian Anda.
                    </div>
                @else
                    <div class="space-y-5">
                        @foreach ($jobPostings as $jobPosting)
                            @php
                                $matchingSkills = $jobPosting->skills->whereIn('id', $skillIds);
                            @endphp
                            <div class="border border-gray-100 dark:border-gray-700 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="text-sm uppercase tracking-wide text-gray-400 dark:text-gray-500">
                                            {{ $jobPosting->company->company_name ?? 'Perusahaan' }}
                                        </p>
                                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $jobPosting->job_title }}
                                        </h3>
                                    </div>
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-200 capitalize">
                                        {{ str_replace('_', ' ', $jobPosting->job_type) }}
                                    </span>
                                </div>

                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                                    {{ \Illuminate\Support\Str::limit($jobPosting->job_description, 180) }}
                                </p>

                                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-2">
                                        @svg('ionicon-location', 'h-4 w-4')
                                        <span>{{ $jobPosting->location }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @svg('ionicon-time-outline', 'h-4 w-4')
                                        <span>Ditutup: {{ optional($jobPosting->closing_date)->format('d M Y H:i') ?? 'Tidak ditentukan' }}</span>
                                    </div>
                                    @if ($matchingSkills->isNotEmpty())
                                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                                            @svg('ionicon-star', 'h-4 w-4')
                                            <span>{{ $matchingSkills->count() }} skill cocok</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($matchingSkills as $skill)
                                        <span class="text-xs px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-200">
                                            {{ $skill->skill_name }}
                                        </span>
                                    @endforeach
                                    @if ($matchingSkills->count() < $jobPosting->skills->count())
                                        @foreach ($jobPosting->skills->diff($matchingSkills)->take(3) as $skill)
                                            <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                                {{ $skill->skill_name }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="mt-6 flex flex-wrap gap-3">
                                    <a href="{{ route('job-postings.show', $jobPosting) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                                        Lihat Detail
                                        @svg('ionicon-arrow-forward-outline', 'h-4 w-4')
                                    </a>
                                    <a href="{{ route('user.applications.create', $jobPosting) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 border border-indigo-200 text-indigo-600 dark:text-indigo-400 text-sm font-semibold rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition">
                                        Lamar Sekarang
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $jobPostings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

