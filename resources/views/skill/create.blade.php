@extends('skill.layout')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('admin.skill.index') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300
                  dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:border dark:border-gray-700 transition">
            ← Kembali
        </a>
    </div>

    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Tambah Skill Baru</h2>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.skill.store') }}" method="POST"
          class="bg-white dark:bg-gray-900 shadow-inner dark:shadow-md dark:shadow-gray-800 rounded-lg p-6 transition">
        @csrf

        <div class="mb-4">
            <div class="flex items-center gap-1 mb-1">
                <label for="skill_name" class="text-gray-700 dark:text-gray-200 font-medium">Nama Skill</label>
                <span class="text-red-500">*</span>
            </div>

            <input type="text" id="skill_name" name="skill_name"
                value="{{ old('skill_name') }}"
                class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                        focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                        transition" 
                placeholder="Contoh: PHP, JavaScript, Laravel, React"
                required>
            
            @error('skill_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center px-5 py-2 text-sm font-medium text-white bg-green-600 rounded
                           hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:outline-none
                           dark:bg-green-700 dark:hover:bg-green-600 transition">
                Simpan Skill
            </button>
        </div>
    </form>
@endsection

