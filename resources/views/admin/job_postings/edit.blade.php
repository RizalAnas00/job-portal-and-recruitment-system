<x-app-layout>
    <x-slot name="breadcrumb">
        Edit Lowongan (Mode Admin)
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="border-b border-gray-200 p-6 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                        Edit Lowongan: {{ $jobPosting->job_title }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Perubahan di sini akan langsung tersimpan ke database.
                    </p>
                </div>

                <div class="p-6">
                    {{-- Pastikan Route Update Mengarah ke Controller yang benar --}}
                    <form action="{{ route('admin.jobs.moderation.update', $jobPosting->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Bagian 1: Konten Lowongan --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {{-- Judul --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul
                                    Pekerjaan</label>
                                <input type="text" name="job_title"
                                    value="{{ old('job_title', $jobPosting->job_title) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    required>
                            </div>

                            {{-- Tipe --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe
                                    Pekerjaan</label>
                                <select name="job_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @foreach (['full_time', 'part_time', 'contract', 'internship', 'temporary', 'freelance', 'remote'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('job_type', $jobPosting->job_type) == $type ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Lokasi (Simple Input, bisa diganti select jika punya master data) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                                <input type="text" name="location"
                                    value="{{ old('location', $jobPosting->location) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    required>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                Pekerjaan</label>
                            <textarea name="job_description" rows="6"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                required>{{ old('job_description', $jobPosting->job_description) }}</textarea>
                        </div>

                        {{-- Bagian 2: KONTROL ADMIN (Status Moderasi) --}}
                        <div
                            class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-700">
                            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Admin Control</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                                {{-- Status Moderasi (Admin Only) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status
                                        Moderasi</label>
                                    <select name="moderation_status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:text-white">
                                        <option value="pending"
                                            {{ old('moderation_status', $jobPosting->moderation_status) == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="approved"
                                            {{ old('moderation_status', $jobPosting->moderation_status) == 'approved' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="rejected"
                                            {{ old('moderation_status', $jobPosting->moderation_status) == 'rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                </div>

                                {{-- Alasan Penolakan (Editable) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan
                                        Penolakan (Opsional)</label>
                                    <input type="text" name="rejection_reason"
                                        value="{{ old('rejection_reason', $jobPosting->rejection_reason) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:text-white"
                                        placeholder="Isi jika status Rejected">
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('admin.jobs.moderation.show', $jobPosting->id) }}"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                Batal
                            </a>
                            <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
