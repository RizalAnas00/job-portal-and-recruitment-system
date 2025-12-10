<div 
    class="fixed inset-0 flex items-center justify-center z-50"
    x-data
    x-show="true"
>

    <div 
        class="absolute inset-0 bg-black/30"
        x-show="true"
    ></div>

    <div 
        class="relative bg-white dark:bg-gray-800 p-5 rounded-xl shadow-lg w-96"
        x-show="true"
    >
        <h2 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-100">
            Upload Resume
        </h2>

        {{-- File input --}}
        <input 
            type="file" 
            wire:model="file"
            class="block w-full text-sm 
                   file:mr-3 file:py-2 file:px-4 
                   file:rounded-md file:border-0 
                   file:bg-primary-600 file:text-white
                   file:cursor-pointer
                   hover:file:bg-primary-700
                   dark:file:bg-primary-500 dark:hover:file:bg-primary-600
                   text-gray-700 dark:text-gray-200"
        >

        @error('file')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror

        <div class="flex justify-end gap-2 mt-5">

            <button 
                wire:click="$set('showUploadModal', false)"
                class="px-3 py-2 rounded-md border border-gray-300 
                       hover:bg-gray-100 dark:hover:bg-gray-700
                       dark:text-gray-200 dark:border-gray-600
                       text-sm transition-colors"
            >
                Batal
            </button>

            <button 
                wire:click="save"
                class="px-3 py-2 rounded-md bg-primary-600 text-white
                       hover:bg-primary-700 transition-colors text-sm
                       dark:bg-primary-500 dark:hover:bg-primary-600"
            >
                Upload
            </button>

        </div>
    </div>
</div>
