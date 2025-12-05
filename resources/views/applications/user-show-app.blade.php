@extends('applications.layout')

@section('content')
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

            <p class="mb-1">
                <span class="font-semibold">Status :</span>
                <span class="
                    @if($application->status==='accepted') text-green-500 
                    @elseif($application->status==='rejected') text-red-500 
                    @else text-yellow-400 @endif font-bold
                ">
                    {{ __(ucfirst($application->status)) }}
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
