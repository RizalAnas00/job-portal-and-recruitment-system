<!-- resources/views/landing.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-gray-900">
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-primary-500 to-primary-600 pb-28 pt-52 text-white">
            <div class="container mx-auto px-6 text-center">
                <h1 class="mb-6 text-4xl font-extrabold md:text-5xl">Temukan Pekerjaan Impianmu</h1>
                <p class="mb-8 text-lg text-primary-100 md:text-xl">Jelajahi <span
                        class="font-extrabold text-white">{{ config('app.name') }}</span> dan temukan lowongan pekerjaan dari
                    perusahaan terpercaya di seluruh Indonesia.</p>

                <!-- Search Bar -->
                <form action="#" method="GET"
                    class="mx-auto flex max-w-2xl overflow-hidden rounded-full bg-white shadow-lg">
                    <input type="text" name="query" placeholder="Cari pekerjaan, posisi, atau perusahaan..."
                        class="flex-1 px-6 py-3 text-gray-700 focus:outline-none" />
                    <button type="submit"
                        class="bg-primary-600 px-6 py-3 font-semibold text-white transition hover:bg-primary-700">
                        Cari
                    </button>
                </form>
            </div>

            <!-- Decorative Shapes -->
            <div
                class="animate-blob absolute left-0 top-0 h-64 w-64 rounded-full bg-blue-500 opacity-30 mix-blend-multiply blur-3xl filter">
            </div>
            <div
                class="animate-blob animation-delay-2000 absolute bottom-0 right-0 h-64 w-64 rounded-full bg-indigo-500 opacity-30 mix-blend-multiply blur-3xl filter">
            </div>
        </section>

        <!-- Category Section -->
        <section class="bg-gray-50 pb-32 pt-16 dark:bg-gray-900">
            <div class="container mx-auto px-6 text-center">
                <h2 class="mb-12 text-center text-3xl font-bold text-gray-900 dark:text-white">Jelajahi Berdasarkan Kategori
                </h2>
                <div class="grid grid-cols-2 gap-6 md:grid-cols-6">
                    @foreach (['Full Time', 'Part Time', 'Contract', 'Internship', 'Temporary', 'Freelance', 'Remote'] as $category)
                        <form action="{{ route('job-postings.index') }}" method="GET">
                            @php
                                $categorySearch = str_replace(' ', '_', strtolower($category));
                            @endphp
                            <input type="hidden" name="category" value="{{ $categorySearch }}">
                            <button type="submit"
                                class="w-full rounded-lg border border-gray-200 bg-gray-200/40 px-4 py-4 text-center font-semibold text-gray-800 shadow backdrop-blur-sm transition hover:-translate-y-1 hover:bg-gray-200 dark:border-gray-700 dark:bg-gray-600/20 dark:text-gray-200 dark:hover:bg-gray-700/70">
                                {{ $category }}
                            </button>
                        </form>
                    @endforeach
                </div>
                {{-- <a href="#" class="mt-8 inline-block text-primary-600 dark:text-primary-400 font-semibold hover:underline">
                Lihat Semua Kategori →
            </a> --}}
            </div>
        </section>

        <!-- Companies Section -->
        <section class="relative overflow-hidden bg-gray-200 pb-16 pt-24 dark:bg-gray-800">
            <div class="absolute inset-0">
                <!-- desktop -->
                <img src="{{ asset('images/cbl.webp') }}" alt="Company Building Background"
                    class="absolute left-0 top-0 hidden h-full w-full object-cover opacity-80 md:block dark:opacity-35">

                <!-- mobile -->
                <img src="{{ asset('images/cbp.webp') }}" alt="Company Building Background Mobile"
                    class="absolute left-0 top-0 block h-64 w-full object-cover opacity-20 md:hidden dark:opacity-25">
            </div>

            <!-- Content -->
            <div class="container relative mx-auto px-6 text-center">
                <h2 class="mb-12 text-3xl font-semibold text-gray-900 md:text-4xl dark:text-white">
                    Dipercaya Lebih Dari
                    <strong class="text-primary-300">
                        {{ isset($companyCount) ? floor($companyCount / 10) * 10 : 1000 }}+
                    </strong>
                    Perusahaan
                </h2>

                <div class="space-y-10">
                    @php
                        $chunks = [];
                        $companies = $companies->values();
                        $rowIndex = 0;

                        while ($companies->isNotEmpty()) {
                            $count = $rowIndex % 2 == 1 ? 4 : 3;
                            $chunks[] = $companies->splice(0, $count);
                            $rowIndex++;
                        }
                    @endphp

                    @foreach ($chunks as $index => $row)
                        <div
                            class="{{ $index % 2 == 1 ? 'grid-cols-1 sm:grid-cols-2 md:grid-cols-4' : 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3' }} grid justify-items-center gap-6">

                            @foreach ($row as $company)
                                <div
                                    class="flex w-full max-w-xs items-center gap-3 rounded-xl bg-gray-50/40 p-4 shadow-sm backdrop-blur-md transition hover:shadow-md dark:bg-gray-800/70">
                                    @if ($company->logo_path)
                                        <img src="{{ $company->logo_path }}" alt="{{ $company->name }}"
                                            class="h-12 w-12 rounded-lg border border-gray-200 bg-white object-contain dark:border-gray-700 dark:bg-gray-700">
                                    @else
                                        @svg('gmdi-corporate-fare-r', 'h-12 w-12 text-gray-800 dark:text-gray-200')
                                    @endif
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $company->company_name }}</p>
                                        @if ($company->created_at)
                                            <p class="text-xs text-gray-800 dark:text-gray-400">
                                                Bergabung sejak {{ $company->created_at->format('Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <!-- Footer text -->
                    <p class="pt-10 text-base font-bold text-gray-900 dark:text-gray-300">
                        dan masih banyak lagi perusahaan lainnya yang telah mempercayai kami...
                    </p>
                </div>
            </div>
        </section>

        <!-- Featured Jobs Section -->
        <section class="bg-gray-100 pb-32 pt-16 dark:bg-gray-900">
            <div class="container mx-auto px-6">
                <h2 class="mb-12 text-center text-3xl font-bold text-gray-900 dark:text-white">Lowongan Terbaru</h2>
                <div class="grid gap-8 md:grid-cols-3">
                    @forelse ($latestJobs as $job)
                        <div
                            class="flex h-full flex-col justify-between rounded-xl bg-white p-6 shadow transition hover:shadow-lg dark:bg-gray-800">
                            <div>
                                <h3 class="mb-2 text-xl font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $job->job_title }}
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $job->company?->company_name }} || <strong>{{ $job->job_type }}</strong> ||
                                    {{ $job->location }}
                                </p>
                                <p class="mb-6 max-h-20 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $job->job_description ? \Illuminate\Support\Str::limit($job->job_description, 200) : '-' }}
                                </p>
                            </div>

                            <a href="#"
                                class="mt-auto font-semibold text-primary-600 hover:underline dark:text-primary-400">
                                Lihat Detail →
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center">
                            <p class="text-gray-600 dark:text-gray-400">Belum ada lowongan terbaru.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-blue-600 text-white">
            <div
                class="container relative z-10 mx-auto flex flex-col-reverse items-center justify-between pb-28 pt-20 md:flex-row">
                <div class="w-full text-center md:w-1/2 md:text-left">
                    <h2 class="mb-4 text-3xl font-bold md:text-4xl">
                        Perusahaan Anda Sedang Mencari Talenta?
                    </h2>
                    <p class="mb-8 text-lg text-blue-100">
                        Pasang lowongan dan temukan kandidat terbaik untuk perusahaan Anda.
                    </p>
                    <a href="@auth {{ route('job-postings.create') }} @else {{ route('login') }} @endauth"
                        class="inline-block rounded-full bg-white px-6 py-3 font-semibold text-indigo-700 shadow transition duration-300 hover:bg-gray-100">
                        Pasang Lowongan Sekarang
                    </a>
                </div>
            </div>

            <div class="absolute right-0 top-0 ml-6 hidden h-full w-1/2 md:block">
                <img src="{{ asset('images/irl.webp') }}" alt="Interview"
                    class="h-full w-full rounded-l-full object-cover" />
            </div>
        </section>

        <footer class="bg-gray-100 py-6 text-center text-sm text-gray-500 dark:bg-gray-900 dark:text-gray-400">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </footer>
    </div>

    <!-- blob animation -->
    <style>
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(30px, -20px) scale(1.1);
            }
        }

        .animate-blob {
            animation: blob 8s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
@endsection
