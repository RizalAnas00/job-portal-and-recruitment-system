<x-app-layout>
    <x-slot name="breadcrumb">
        <a href="{{ route('admin.industries.index') }}">Industries</a> / Edit
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-3 lg:px-5">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Edit Industri</h2>

                <form method="POST" action="{{ route('admin.industries.update', $industry) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Industri <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $industry->name) }}"
                                class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Deskripsi
                            </label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description', $industry->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $industry->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                                Update
                            </button>
                            <a href="{{ route('admin.industries.index') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200">
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

