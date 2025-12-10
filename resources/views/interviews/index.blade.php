@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold">Daftar Interview</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
        <x-table :headers="[
            'Posisi / Pelamar',
            'Tanggal Interview',
            'Jenis',
            'Interviewer',
            'Status',
            'Aksi'
        ]">
            @forelse($interviews as $interview)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    
                    {{-- Posisi / Pelamar --}}
                    <td class="px-6 py-4 text-left">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $interview->application->jobPosting->job_title ?? '-' }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $interview->application->jobSeeker->firs_name ?? '' }} {{ $interview->application->jobSeeker->last_name ?? '' }}
                        </div>
                        @if(Auth::user()->hasRole('company'))
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $interview->application->jobSeeker->user->email ?? '-' }}
                            </div>
                        @endif
                    </td>

                    {{-- Tanggal Interview --}}
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($interview->interview_date)->format('d M Y') }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($interview->interview_date)->format('H:i') }}
                        </div>
                    </td>

                    {{-- Jenis --}}
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($interview->interview_type === 'online')
                                bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-200
                            @elseif($interview->interview_type === 'offline')
                                bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200
                            @elseif($interview->interview_type === 'phone_screen')
                                bg-gray-300 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                            @elseif($interview->interview_type === 'video')
                                bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                            @else
                                bg-gray-300 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                            @endif
                        ">
                            {{ str_replace('_', ' ', ucfirst($interview->interview_type)) }}
                        </span>
                    </td>

                    {{-- Interviewer --}}
                    <td class="px-6 py-4 text-gray-900 dark:text-white">
                        {{ $interview->interviewer_name }}
                    </td>

                    {{-- Status Application --}}
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if ($interview->application->status === 'interviewing')
                                bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200
                            @elseif ($interview->application->status === 'hired')
                                bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200
                            @elseif ($interview->application->status === 'rejected')
                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @else
                                bg-gray-300 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                            @endif
                        ">
                            {{ ucfirst($interview->application->status) }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 text-center text-sm font-medium">
                        <a href="{{ route('interviews.show', $interview) }}" 
                        class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 mr-3">
                            Lihat
                        </a>

                        @if(Auth::user()->hasRole('company') || Auth::user()->hasRole('admin'))
                            <a href="{{ route('interviews.edit', $interview) }}" 
                            class="text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300 mr-3">
                                Edit
                            </a>

                            <form action="{{ route('interviews.destroy', $interview) }}"
                                method="POST"
                                class="inline-block"
                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan jadwal interview ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    Batalkan
                                </button>
                            </form>
                        @endif
                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        Tidak ada jadwal interview.
                    </td>
                </tr>
            @endforelse
        </x-table>

        </div>
    </div>

    <div class="mt-4">
        {{ $interviews->links() }}
    </div>
</div>
@endsection

