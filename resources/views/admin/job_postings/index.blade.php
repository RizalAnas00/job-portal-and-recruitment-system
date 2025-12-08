{{-- resources/views/admin/job_postings/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Moderasi Lowongan Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Flash Message (Untuk notifikasi sukses/gagal) --}}
            @if (session('success'))
                <div class="relative mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="relative mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700" role="alert">
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Perusahaan</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Posisi / Judul</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Tanggal Post</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($jobs as $job)
                                    <tr>
                                        {{-- Kolom Perusahaan --}}
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $job->company->company_name ?? 'Nama Tidak Ada' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $job->location }}
                                            </div>
                                        </td>

                                        {{-- Kolom Judul Job --}}
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $job->job_title }}</div>
                                            <div class="text-xs text-gray-500">{{ $job->job_type }}</div>
                                        </td>

                                        {{-- Kolom Tanggal --}}
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $job->posted_date ? \Carbon\Carbon::parse($job->posted_date)->format('d M Y') : '-' }}
                                        </td>

                                        {{-- Kolom Status (Badge Warna) --}}
                                        <td class="whitespace-nowrap px-6 py-4">
                                            @if ($job->moderation_status == 'pending')
                                                <span
                                                    class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                                                    Menunggu Review
                                                </span>
                                            @elseif($job->moderation_status == 'approved')
                                                <span
                                                    class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                    Disetujui
                                                </span>
                                            @elseif($job->moderation_status == 'rejected')
                                                <span
                                                    class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                                    Ditolak
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Kolom Aksi --}}
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                            {{-- Link Lihat Detail (Buka di tab baru) --}}
                                            <a href="{{ route('job-postings.show', $job->id) }}" target="_blank"
                                                class="mr-3 text-indigo-600 hover:text-indigo-900">
                                                Lihat
                                            </a>

                                            @if ($job->moderation_status == 'pending')
                                                {{-- Tombol Approve --}}
                                                <form action="{{ route('admin.jobs.approve', $job->id) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        onclick="return confirm('Apakah Anda yakin ingin menyetujui lowongan ini?')"
                                                        class="mr-3 font-bold text-green-600 hover:text-green-900">
                                                        Approve
                                                    </button>
                                                </form>

                                                {{-- Tombol Reject (Trigger Modal) --}}
                                                <button type="button"
                                                    onclick="openRejectModal('{{ route('admin.jobs.reject', $job->id) }}')"
                                                    class="font-bold text-red-600 hover:text-red-900">
                                                    Reject
                                                </button>
                                            @elseif($job->moderation_status == 'rejected')
                                                {{-- Tampilkan alasan jika sudah ditolak --}}
                                                <span class="mt-1 block text-xs italic text-gray-500">
                                                    Alasan: {{ Str::limit($job->rejection_reason, 20) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada lowongan yang perlu dimoderasi saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $jobs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL REJECT (Overlay) --}}
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">

            {{-- Background Overlay --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeRejectModal()"></div>

            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div
                class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                <form id="rejectForm" method="POST" action="">
                    @csrf
                    @method('PATCH')

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                    Tolak Lowongan Pekerjaan
                                </h3>
                                <div class="mt-2">
                                    <p class="mb-2 text-sm text-gray-500">
                                        Mohon berikan alasan penolakan agar perusahaan dapat memperbaikinya.
                                    </p>
                                    <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Contoh: Deskripsi mengandung unsur diskriminasi SARA..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            Tolak Lowongan
                        </button>
                        <button type="button" onclick="closeRejectModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script JavaScript untuk Mengatur Modal --}}
    <script>
        function openRejectModal(actionUrl) {
            // Set action URL pada form sesuai tombol yang diklik
            document.getElementById('rejectForm').action = actionUrl;

            // Tampilkan modal
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            // Sembunyikan modal
            document.getElementById('rejectModal').classList.add('hidden');

            // Reset form (opsional)
            document.getElementById('rejectForm').reset();
        }
    </script>

</x-app-layout>
