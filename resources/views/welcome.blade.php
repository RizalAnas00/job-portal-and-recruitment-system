<!-- resources/views/landing.blade.php -->
@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-500 to-primary-600 text-white pt-52 pb-28">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6">Temukan Pekerjaan Impianmu</h1>
            <p class="text-lg md:text-xl text-primary-100 mb-8">Jelajahi <span class="font-extrabold text-white">{{ config('app.name') }}</span> dan temukan lowongan pekerjaan dari perusahaan terpercaya di seluruh Indonesia.</p>

            <!-- Search Bar -->
            <form action="#" method="GET" class="max-w-2xl mx-auto flex bg-white rounded-full overflow-hidden shadow-lg">
                <input type="text" name="query" placeholder="Cari pekerjaan, posisi, atau perusahaan..."
                    class="flex-1 px-6 py-3 text-gray-700 focus:outline-none" />
                <button type="submit"
                    class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 font-semibold transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Decorative Shapes -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    </section>

    <!-- Category Section -->
    <section class="pt-16 pb-32 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Jelajahi Berdasarkan Kategori</h2>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-6">
            @foreach (['Full Time', 'Part Time', 'Contract', 'Internship', 'Temporary', 'Freelance', 'Remote'] as $category)
                <form action="#" method="GET">
                    @php
                        $categorySearch = str_replace(' ', '_', strtolower($category));
                    @endphp
                    <input type="hidden" name="category" value="{{ $categorySearch }}">
                    <button type="submit"
                        class="w-full bg-gray-200/40 dark:bg-gray-600/20 text-gray-800 dark:text-gray-200 
                               border border-gray-200 dark:border-gray-700 rounded-lg shadow 
                               hover:bg-gray-200 dark:hover:bg-gray-700/70 hover:-translate-y-1 text-center backdrop-blur-sm
                               px-4 py-4 font-semibold transition">
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
    <section class="relative pt-24 pb-16 bg-gray-200 dark:bg-gray-800 overflow-hidden">
        <div class="absolute inset-0">
            <!-- desktop -->
            <img src="{{ asset('images/cbl.webp') }}" 
                alt="Company Building Background" 
                class="hidden md:block w-full h-full object-cover absolute top-0 left-0 opacity-80 dark:opacity-35">

            <!-- mobile -->
            <img src="{{ asset('images/cbp.webp') }}" 
                alt="Company Building Background Mobile" 
                class="block md:hidden w-full h-64 object-cover absolute top-0 left-0 opacity-20 dark:opacity-25">
        </div>

        <!-- Content -->
        <div class="relative container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-semibold text-gray-900 dark:text-white mb-12">
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
                        $count = ($rowIndex % 2 == 1) ? 4 : 3;
                        $chunks[] = $companies->splice(0, $count);
                        $rowIndex++;
                    }
                @endphp

                @foreach ($chunks as $index => $row)
                    <div class="grid gap-6 justify-items-center
                        {{ $index % 2 == 1 ? 'grid-cols-1 sm:grid-cols-2 md:grid-cols-4' : 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3' }}">
                        
                        @foreach ($row as $company)
                            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 rounded-xl p-4 w-full max-w-xs shadow-sm hover:shadow-md transition">
                                @if ($company->logo_url)
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="h-12 w-12 object-contain rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-700">
                                @else
                                    @svg('gmdi-corporate-fare-r', 'h-12 w-12 text-gray-400')
                                @endif
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $company->company_name }}</p>
                                    @if ($company->created_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Bergabung sejak {{ $company->created_at->format('Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
                <!-- Footer text -->
                <p class="text-gray-600 dark:text-gray-300 text-sm pt-12">
                    dan masih banyak lagi perusahaan lainnya yang telah mempercayai kami...
                </p>
            </div>
        </div>
    </section>

    <!-- Featured Jobs Section -->
    <section class="pt-16 pb-32 bg-gray-100 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Lowongan Terbaru</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @forelse ($latestJobs as $job)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">{{ $job->job_title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">{{ $job->company?->company_name }} || <strong>{{ $job->job_type }}</strong> || {{ $job->location }}</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 max-h-16 overflow-hidden">{{ $job->job_description ?? '-' }}</p>
                        <a href="#" class="text-primary-600 dark:text-primary-400 font-semibold hover:underline">Lihat Detail →</a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-600 dark:text-gray-400">Belum ada lowongan terbaru.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="relative bg-gradient-to-br from-indigo-600 to-blue-600 text-white overflow-hidden">
        <div class="container mx-auto pt-20 pb-28 flex flex-col-reverse md:flex-row items-center justify-between relative z-10">
            <div class="w-full md:w-1/2 text-center md:text-left">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Perusahaan Anda Sedang Mencari Talenta?
                </h2>
                <p class="text-lg text-blue-100 mb-8">
                    Pasang lowongan dan temukan kandidat terbaik untuk perusahaan Anda.
                </p>
                <a href="@auth {{ route('job-postings.create') }} @else {{ route('login') }} @endauth"
                class="inline-block bg-white text-indigo-700 font-semibold px-6 py-3 rounded-full shadow 
                        hover:bg-gray-100 transition duration-300">
                    Pasang Lowongan Sekarang
                </a>
            </div>
        </div>

        <div class="hidden md:block absolute ml-6 top-0 right-0 w-1/2 h-full">
            <img src="{{ asset('images/irl.webp') }}"
                alt="Interview"
                class="w-full h-full object-cover rounded-l-full" />
        </div>
    </section>

    <footer class="bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 text-center py-6 text-sm">
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
</div>

<!-- blob animation -->
<style>
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(30px, -20px) scale(1.1); }
}
.animate-blob { animation: blob 8s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
</style>
@endsection
