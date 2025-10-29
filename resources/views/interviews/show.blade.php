@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold">Detail Jadwal Interview</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <!-- Status Badge -->
        <div class="mb-6">
            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                {{ $interview->application->status === 'interviewing' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                {{ $interview->application->status === 'hired' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                {{ $interview->application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                Status: {{ ucfirst($interview->application->status) }}
            </span>
        </div>

        <!-- Informasi Lamaran -->
        <div class="border-b dark:border-gray-700 pb-4 mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Informasi Lamaran</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Posisi</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $interview->application->jobPosting->job_title ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perusahaan</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $interview->application->jobPosting->company->company_name ?? '-' }}
                    </p>
                </div>
                @if(Auth::user()->hasRole('company') || Auth::user()->hasRole('admin'))
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nama Pelamar</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $interview->application->jobSeeker->user->name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email Pelamar</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $interview->application->jobSeeker->user->email ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No. Telepon</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $interview->application->jobSeeker->phone_number ?? '-' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail Interview -->
        <div class="border-b dark:border-gray-700 pb-4 mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Detail Interview</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal & Waktu</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($interview->interview_date)->translatedFormat('l, d F Y') }}
                    </p>
                    <p class="text-base text-gray-700 dark:text-gray-300">
                        Pukul {{ \Carbon\Carbon::parse($interview->interview_date)->format('H:i') }} WIB
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Jenis Interview</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $interview->interview_type === 'online' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $interview->interview_type === 'offline' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $interview->interview_type === 'phone_screen' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}">
                            {{ str_replace('_', ' ', ucfirst($interview->interview_type)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nama Interviewer</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $interview->interviewer_name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if($interview->interview_type === 'online')
                            Link Meeting
                        @else
                            Lokasi
                        @endif
                    </p>
                    <p class="text-base font-semibold text-gray-900 dark:text-white break-all">
                        @if($interview->interview_type === 'online' && (str_contains($interview->location, 'http://') || str_contains($interview->location, 'https://')))
                            <a href="{{ $interview->location }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">
                                {{ $interview->location }}
                            </a>
                        @else
                            {{ $interview->location }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        @if($interview->notes)
            <div class="border-b dark:border-gray-700 pb-4 mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Catatan Tambahan</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $interview->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Timestamps -->
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-6">
            <p>Dibuat: {{ $interview->created_at->translatedFormat('d F Y H:i') }}</p>
            @if($interview->updated_at != $interview->created_at)
                <p>Terakhir diperbarui: {{ $interview->updated_at->translatedFormat('d F Y H:i') }}</p>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('interviews.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali ke Daftar
            </a>
            
            @if(Auth::user()->hasRole('company') || Auth::user()->hasRole('admin'))
                <a href="{{ route('interviews.edit', $interview) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Jadwal
                </a>
                
                <form action="{{ route('interviews.destroy', $interview) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan jadwal interview ini? Status lamaran akan kembali ke Under Review.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Batalkan Interview
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

