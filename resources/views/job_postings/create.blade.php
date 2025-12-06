@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold mb-4">Buat Lowongan Baru</h1>

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

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <form action="{{ route('job-postings.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="job_title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul</label>
                <input type="text" id="job_title" name="job_title" value="{{ old('job_title') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
            </div>

            <div class="mb-4">
                <label for="job_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deskripsi</label>
                <textarea id="job_description" name="job_description" rows="6" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>{{ old('job_description') }}</textarea>
            </div>

            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-4">
                    <label for="location" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lokasi</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div class="mb-4">
                    <label for="job_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Pekerjaan</label>
                    <select id="job_type" name="job_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        @foreach(['full_time','part_time','contract','internship','temporary','freelance','remote'] as $type)
                            <option value="{{ $type }}" {{ old('job_type') == $type ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 md:gap-6 mb-6">
                <x-input-currency name="min_salary" label="Gaji Minimum (Rp)"/>
                <x-input-currency name="max_salary" label="Gaji Maksimum (Rp)"/>
            </div>

            <div class="mb-4">
                <div class="mt-3 p-4 border border-gray-300 dark:border-gray-500 rounded-xl 
                            bg-gray-50 dark:bg-gray-800 max-h-80 overflow-y-auto">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                        @foreach ($skills as $skill)
                            <div
                                x-show="{{ json_encode(
                                    strtolower($skill->skill_name)
                                ) }}.includes(search.toLowerCase())"
                                x-transition
                            >
                                <x-check-box-one
                                    :skill="$skill"
                                />
                            </div>
                        @endforeach

                    </div>

                </div>
            </div>

            <div class="grid md:grid-cols-2 md:gap-6 mb-6">
                <div>
                    <label for="posted_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu Buka (Open Date)</label>
                    <input type="datetime-local" id="posted_date" name="posted_date"
                        value="{{ old('posted_date', now()->format('Y-m-d\TH:i')) }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label for="closing_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu Tutup (Close Date)</label>
                    <input type="datetime-local" id="closing_date" name="closing_date"
                        value="{{ old('closing_date', now()->addWeek()->format('Y-m-d\TH:i')) }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                <a href="{{ route('job-postings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection