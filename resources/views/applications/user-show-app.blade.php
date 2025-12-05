@extends('applications.layout')

@section('content')
    @if ($application->status === 'hired')
        <div class="bg-green-100 text-center border border-green-400 text-green-700 px-4 py-3 mb-8 rounded relative" role="alert">
            <strong class="font-bold">Selamat!</strong>
            <span class="block sm:inline"> Anda telah diterima di {{ $application->jobPosting->company->company_name }}</span>
        </div>
    @endif
    
    <div>
        <a href="{{ route('user.applications.index') }}" 
            class="px-5 py-2 rounded-lg font-semibold shadow-sm text-white 
                    bg-gray-700 hover:bg-gray-900 dark:bg-gray-600 dark:hover:bg-gray-500">
            ← {{ __('Back') }}
        </a>
    </div>

    <div class="max-w-screen-2xl mx-auto space-y-4 pt-6 pb-10">

        <x-accordion-1 title="{{ __('Job Details') }}" open="true">
            <x-job-detail :jobPosting="$application->jobPosting" />
        </x-accordion-1>

        <x-accordion-1 title="{{ __('Your Job Application') }}">

            <p class="mb-1 rounded-md border px-3 py-2 bg-transparent
                    border-gray-300 dark:border-gray-700
                    text-gray-800 dark:text-gray-200">
                <span class="font-semibold">Status :</span>
                <span class="
                    @if ($application->status === 'applied') text-gray-400
                    @elseif ($application->status === 'reviewed') text-gray-200
                    @elseif ($application->status === 'pending') text-primary-300
                    @elseif ($application->status === 'under_review') text-primary-400
                    @elseif ($application->status === 'interview_scheduled') text-primary-500
                    @elseif ($application->status === 'interviewing') text-teal-500
                    @elseif ($application->status === 'offered') text-teal-300
                    @elseif ($application->status === 'accepted') text-teal-400
                    @elseif ($application->status === 'hired') text-teal-500
                    @elseif ($application->status === 'rejected') text-red-500
                    @endif font-bold
                ">
                    {{ __(ucfirst(str_replace('_', ' ', $application->status))) }}
                </span>
            </p>

            <p><span class="font-semibold">Dilamar pada :</span>
               {{ $application->created_at->format('d F Y H:i') }}
            </p>

            @if($application->cover_letter)
                <div class="mt-4">
                    <span class="font-semibold">Cover Letter:</span>
                    <div class="p-4 mt-1 rounded-lg bg-gray-100 dark:bg-gray-800 border dark:border-gray-700">
                        {!! nl2br(e($application->cover_letter)) !!}
                    </div>
                </div>
            @endif

        </x-accordion-1>

    </div>

@endsection
