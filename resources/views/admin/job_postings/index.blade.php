<x-app-layout>
    <x-slot name="breadcrumb">
        Job Posting Moderation
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-3 lg:px-5">
            <div class="pb-2 text-gray-100">
                <div class="mb-5">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Moderasi Lowongan Pekerjaan</h1>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Lowongan</div>
                            <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['total'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Pending</div>
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Disetujui</div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['approved'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Ditolak</div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['rejected'] }}</div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
                        <form method="GET" action="{{ route('admin.jobs.moderation.index') }}" class="space-y-4">
                            <div class="flex flex-wrap gap-4 items-end">
                                <!-- Search -->
                                <div class="flex-1 min-w-[250px]">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                                    <input type="text" name="search" placeholder="Cari judul, deskripsi, atau perusahaan..."
                                        class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                                        value="{{ request('search') }}">
                                </div>

                                <!-- Moderation Status Filter -->
                                <div class="min-w-[150px]">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Moderation</label>
                                    <select name="moderation_status" 
                                        class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                        <option value="">Semua</option>
                                        <option value="pending" {{ request('moderation_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('moderation_status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="rejected" {{ request('moderation_status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div class="min-w-[150px]">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                    <select name="status" 
                                        class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                        <option value="">Semua</option>
                                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                                    Filter
                                </button>
                                <a href="{{ route('admin.jobs.moderation.index') }}"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition dark:bg-gray-700 dark:text-gray-200">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <x-table :headers="['ID', 'Perusahaan', 'Judul Lowongan', 'Tanggal Post', 'Status Moderation', 'Status', 'Actions']">
                    @forelse ($jobs as $job)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-4">{{ $job->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold">{{ $job->company->company_name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $job->location->name ?? $job->location ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $job->job_title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $job->job_type }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($job->posted_date)
                                    {{ $job->posted_date->format('d M Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($job->moderation_status == 'pending')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Menunggu Review
                                    </span>
                                @elseif($job->moderation_status == 'approved')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Disetujui
                                    </span>
                                @elseif($job->moderation_status == 'rejected')
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $job->status === 'open' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $job->status === 'closed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                    {{ $job->status === 'draft' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.jobs.moderation.show', $job) }}"
                                        class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800 hover:bg-blue-200">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.jobs.moderation.edit', $job) }}"
                                        class="text-xs px-2 py-1 rounded bg-primary-100 text-primary-800 hover:bg-primary-200">
                                        Edit
                                    </a>
                                    
                                    @if($job->moderation_status == 'pending')
                                        <form action="{{ route('admin.jobs.moderation.approve', $job) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                onclick="return confirm('Apakah Anda yakin ingin menyetujui lowongan ini?')"
                                                class="text-xs px-2 py-1 rounded bg-green-100 text-green-800 hover:bg-green-200">
                                                Approve
                                            </button>
                                        </form>
                                        <button type="button" 
                                            onclick="openRejectModal('{{ route('admin.jobs.moderation.reject', $job) }}')"
                                            class="text-xs px-2 py-1 rounded bg-red-100 text-red-800 hover:bg-red-200">
                                            Reject
                                        </button>
                                    @endif

                                    @if($job->moderation_status == 'rejected' && $job->rejection_reason)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Alasan: {{ Str::limit($job->rejection_reason, 30) }}
                                        </div>
                                    @endif

                                    <form action="{{ route('admin.jobs.moderation.destroy', $job) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs px-2 py-1 rounded bg-red-100 text-red-800 hover:bg-red-200">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada lowongan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                <div class="mt-6">
                    {{ $jobs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Tolak Lowongan Pekerjaan</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Mohon berikan alasan penolakan agar perusahaan dapat memperbaikinya.
            </p>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <textarea name="rejection_reason" rows="4" required
                    class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Contoh: Deskripsi mengandung unsur diskriminasi SARA..."></textarea>
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeRejectModal()" 
                        class="px-4 py-2 text-sm rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-700">
                        Tolak Lowongan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openRejectModal(actionUrl) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = actionUrl;
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            document.getElementById('rejectForm').reset();
        }
    </script>
    @endpush
</x-app-layout>
