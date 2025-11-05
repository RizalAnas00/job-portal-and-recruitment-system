@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0">
        <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold">Daftar Lowongan</h1>
        <div class="flex items-center space-x-2">
            <form action="{{ route('job-postings.index') }}" method="GET" class="flex items-center space-x-2">
                <select name="status" id="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-md">Filter</button>
            </form>
            @if(Auth::user()->hasRole('company'))
                <a href="{{ route('company.job-postings.create') }}" class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-md">
                    Buat Lowongan Baru
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-3 list-none">
        @forelse($jobPostings as $job)
            <li><x-card-single :job="$job" /></li>
        @empty
            <li class="col-span-full text-gray-600 text-center italic py-6 dark:text-gray-400">
                Tidak ada lowongan yang tersedia saat ini.
            </li>
        @endforelse
    </ul>

    <div class="mt-3">
        {{ $jobPostings->links() }}
    </div>
</div>
@endsection
