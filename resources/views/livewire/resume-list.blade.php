<div>
    <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold">Daftar Resume</h1>
    <div class="mt-4">
        @forelse ($applicants as $resume)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-4">
                <a href="#" class="text-blue-500 hover:underline mt-3 inline-block">{{ $resume->id }}</a>
            </div>
        @empty
            <p class="text-gray-600 dark:text-gray-400">Tidak ada resume yang tersedia.</p>
        @endforelse
    </div>
</div>
