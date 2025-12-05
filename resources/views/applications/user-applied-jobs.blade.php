@extends('applications.layout')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Applied Jobs</h1>
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

    <x-table :headers="['Job Title', 'Company', 'Applied On', 'Status', 'Actions']">
        @forelse($applications as $application)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">

                <td class="px-6 py-4">{{ $application->jobPosting->job_title ?? 'N/A' }}</td>

                <td class="px-6 py-4">{{ $application->jobPosting->company->name ?? 'N/A' }}</td>

                <td class="px-6 py-4">{{ $application->application_date }}</td>

                <td class="px-6 py-4 capitalize">
                    {{ str_replace('_', ' ', $application->status) }}
                </td>

                <td class="px-6 py-4">
                    <a href="#" 
                    class="text-indigo-600 hover:text-indigo-900">
                        View Details
                    </a>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="5" 
                    class="italic px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                    No applications found.
                </td>
            </tr>
        @endforelse
    </x-table>

</div>
@endsection