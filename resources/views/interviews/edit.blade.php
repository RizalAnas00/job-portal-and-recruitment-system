@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold mb-4">Edit Jadwal Interview</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oops!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Info Pelamar -->
    <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Informasi Pelamar</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p><strong>Nama:</strong> {{ $interview->application->jobSeeker->user->name ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $interview->application->jobSeeker->user->email ?? '-' }}</p>
                    <p><strong>Posisi:</strong> {{ $interview->application->jobPosting->job_title ?? '-' }}</p>
                    <p><strong>Status Lamaran:</strong> <span class="font-semibold">{{ ucfirst($interview->application->status) }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <form action="{{ route('interviews.update', $interview) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="interviewer_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Interviewer <span class="text-red-500">*</span>
                </label>
                <input type="text" id="interviewer_name" name="interviewer_name" 
                    value="{{ old('interviewer_name', $interview->interviewer_name) }}" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                    placeholder="Contoh: John Doe - HR Manager" required>
            </div>

            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-4">
                    <label for="interview_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Tanggal & Waktu Interview <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="interview_date" name="interview_date" 
                        value="{{ old('interview_date', \Carbon\Carbon::parse($interview->interview_date)->format('Y-m-d\TH:i')) }}" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                        min="{{ now()->addHours(1)->format('Y-m-d\TH:i') }}" required>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih waktu minimal 1 jam dari sekarang</p>
                </div>

                <div class="mb-4">
                    <label for="interview_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Jenis Interview <span class="text-red-500">*</span>
                    </label>
                    <select id="interview_type" name="interview_type" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">-- Pilih Jenis Interview --</option>
                        <option value="online" {{ old('interview_type', $interview->interview_type) == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ old('interview_type', $interview->interview_type) == 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="phone_screen" {{ old('interview_type', $interview->interview_type) == 'phone_screen' ? 'selected' : '' }}>Phone Screen</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="location" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Lokasi / Link Meeting <span class="text-red-500">*</span>
                </label>
                <input type="text" id="location" name="location" 
                    value="{{ old('location', $interview->location) }}" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                    placeholder="Contoh: https://zoom.us/j/123456 atau Jl. Sudirman No. 123" required>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Isi dengan link meeting (Zoom, Google Meet, dll) atau alamat fisik</p>
            </div>

            <div class="mb-6">
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Catatan Tambahan
                </label>
                <textarea id="notes" name="notes" rows="4" 
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                    placeholder="Informasi tambahan untuk kandidat (persiapan, dokumen yang perlu dibawa, dll)">{{ old('notes', $interview->notes) }}</textarea>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Perubahan
                </button>
                <a href="{{ route('interviews.show', $interview) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

