<x-app-layout>
    <x-slot name="breadcrumb">
        Detail Lowongan (Admin)
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Header & Back Button --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Lowongan</h1>
                <a href="{{ route('admin.jobs.moderation.index') }}"
                    class="mt-2 inline-flex items-center text-sm text-gray-600 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Moderasi
                </a>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                {{-- KOLOM KIRI (UTAMA): Deskripsi --}}
                <div class="space-y-6 lg:col-span-2">
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
                        <div class="p-6">
                            {{-- Header Lowongan --}}
                            <div class="mb-6 border-b border-gray-100 pb-6 dark:border-gray-700">
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ $jobPosting->job_title }}</h2>
                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <span class="mr-4 flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        {{ $jobPosting->company->company_name }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $jobPosting->location->name ?? $jobPosting->location }}
                                    </span>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="prose dark:prose-invert max-w-none">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Deskripsi Pekerjaan</h3>
                                <div class="mt-4 whitespace-pre-line leading-relaxed text-gray-600 dark:text-gray-300">
                                    {!! nl2br(e($jobPosting->job_description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN (SIDEBAR): Meta & Aksi --}}
                <div class="space-y-6">

                    {{-- 1. PANEL MODERASI (Paling Atas) --}}
                    <div
                        class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Status Moderasi</h3>
                        <div class="mt-4">
                            @if ($jobPosting->moderation_status == 'pending')
                                <div
                                    class="rounded-md border border-yellow-100 bg-yellow-50 p-4 dark:border-yellow-900/50 dark:bg-yellow-900/20">
                                    <div class="flex items-center gap-3">
                                        <span class="relative flex h-3 w-3">
                                            <span
                                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-yellow-400 opacity-75"></span>
                                            <span
                                                class="relative inline-flex h-3 w-3 rounded-full bg-yellow-500"></span>
                                        </span>
                                        <span class="font-bold text-yellow-800 dark:text-yellow-400">Menunggu
                                            Review</span>
                                    </div>
                                </div>
                            @elseif($jobPosting->moderation_status == 'approved')
                                <div
                                    class="rounded-md border border-green-100 bg-green-50 p-4 dark:border-green-900/50 dark:bg-green-900/20">
                                    <div class="flex items-center gap-2 text-green-800 dark:text-green-400">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="font-bold">Disetujui</span>
                                    </div>
                                </div>
                            @elseif($jobPosting->moderation_status == 'rejected')
                                <div
                                    class="rounded-md border border-red-100 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-900/20">
                                    <div class="flex items-center gap-2 text-red-800 dark:text-red-400">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span class="font-bold">Ditolak</span>
                                    </div>
                                    @if ($jobPosting->rejection_reason)
                                        <p class="mt-2 border-t border-red-200 pt-2 text-sm italic dark:border-red-800">
                                            "{{ $jobPosting->rejection_reason }}"</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex flex-col gap-3">
                            @if ($jobPosting->moderation_status != 'approved')
                                <form action="{{ route('admin.jobs.moderation.approve', $jobPosting->id) }}"
                                    method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" onclick="return confirm('Setujui lowongan ini?')"
                                        class="flex w-full items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-500">
                                        Setujui Lowongan
                                    </button>
                                </form>
                            @endif

                            @if ($jobPosting->moderation_status != 'rejected')
                                <button type="button"
                                    onclick="openRejectModal('{{ route('admin.jobs.moderation.reject', $jobPosting->id) }}')"
                                    class="flex w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500">
                                    Tolak Lowongan
                                </button>
                            @endif

                            <a href="{{ route('admin.jobs.moderation.edit', $jobPosting->id) }}"
                                class="flex w-full items-center justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                                Edit Konten
                            </a>
                        </div>
                    </div>

                    {{-- 2. DETAIL INFORMASI (Pindah ke Kanan) --}}
                    <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Detail Pekerjaan</h3>
                        <dl class="mt-4 space-y-4 divide-y divide-gray-100 dark:divide-gray-700">
                            <div class="pt-4 first:pt-0">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Jenis Pekerjaan</dt>
                                <dd
                                    class="mt-1 flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ ucwords(str_replace('_', ' ', $jobPosting->job_type)) }}
                                </dd>
                            </div>
                            <div class="pt-4">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Gaji</dt>
                                <dd
                                    class="mt-1 flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @if ($jobPosting->min_salary)
                                        Rp {{ number_format($jobPosting->min_salary) }} -
                                        {{ number_format($jobPosting->max_salary) }}
                                    @else
                                        Dirahasiakan
                                    @endif
                                </dd>
                            </div>
                            <div class="pt-4">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Tanggal Posting</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $jobPosting->posted_date ? $jobPosting->posted_date->format('d M Y') : '-' }}
                                </dd>
                            </div>
                            <div class="pt-4">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Batas Lamaran</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $jobPosting->closing_date ? $jobPosting->closing_date->format('d M Y') : '-' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- 3. SKILLS (Pindah ke Kanan untuk mengisi ruang) --}}
                    @if ($jobPosting->skills->count() > 0)
                        <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                            <h3
                                class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Keahlian</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($jobPosting->skills as $skill)
                                    <span
                                        class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $skill->skill_name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 4. INFO PERUSAHAAN --}}
                    <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                        <div class="flex items-center gap-4">
                            @if ($jobPosting->company->logo)
                                <img src="{{ asset('storage/' . $jobPosting->company->logo) }}" alt=""
                                    class="h-12 w-12 rounded-full bg-gray-100 object-cover">
                            @else
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-600">
                                    {{ substr($jobPosting->company->company_name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $jobPosting->company->company_name }}</h4>
                                <p class="text-xs text-gray-500">
                                    {{ $jobPosting->company->industry->name ?? 'Industry N/A' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-700">
                            <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Lihat
                                Profil Perusahaan &rarr;</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal & Script --}}
    <div id="rejectModal"
        class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div
            class="mx-4 w-full max-w-md scale-100 transform rounded-xl bg-white p-6 shadow-2xl transition-all dark:bg-gray-800">
            <h2 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">Tolak Lowongan</h2>
            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                Berikan alasan mengapa lowongan ini ditolak agar perusahaan dapat memperbaikinya.
            </p>
            <form id="rejectForm" method="POST">
                @csrf @method('PATCH')
                <textarea name="rejection_reason" rows="4" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Contoh: Informasi gaji tidak masuk akal..."></textarea>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Konfirmasi
                        Tolak</button>
                </div>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            function openRejectModal(actionUrl) {
                document.getElementById('rejectForm').action = actionUrl;
                document.getElementById('rejectModal').classList.remove('hidden');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
                document.getElementById('rejectForm').reset();
            }
        </script>
    @endsection
</x-app-layout>
