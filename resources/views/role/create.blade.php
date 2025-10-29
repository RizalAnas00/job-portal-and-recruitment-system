@extends('role.layout')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('admin.role.index') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300
                  dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:border dark:border-gray-700 transition">
            ← Back
        </a>
    </div>

    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Add New Role</h2>

    <form action="{{ route('admin.role.store') }}" method="POST"
          class="bg-white dark:bg-gray-900 shadow-inner dark:shadow-md dark:shadow-gray-800 rounded-lg p-6 transition">
        @csrf

        <div class="mb-4">
            <div class="flex items-center gap-1 mb-1">
                <label for="name" class="text-gray-700 dark:text-gray-200 font-medium">Role Name</label>
            </div>

            <input type="text" id="name" name="name"
                class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                        focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                        transition" required>
        </div>

        <div class="mb-4">
            <div class="flex items-center gap-1 mb-1">
                <label for="display_name" class="text-gray-700 dark:text-gray-200 font-medium">Display Role Name</label>
                <span class="text-gray-500 dark:text-gray-400 text-sm">(The name displayed on the web interface)</span>
            </div>

            <input type="text" id="display_name" name="display_name"
                class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                        focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                        transition" required>
        </div>

        <div class="mb-4">
            <div class="flex items-center justify-between mb-1">
                <label for="description" class="text-gray-700 dark:text-gray-200 font-medium">Description</label>
                <span class="text-xs text-gray-500 dark:text-gray-400">(max 255 characters)</span>
            </div>
            <textarea id="description" name="description" rows="3" maxlength="255"
                class="w-full px-4 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-700
                    bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                    focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                    transition resize-none"
                placeholder="Briefly describe this role..."></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-2">
                Assign Permissions
            </label>
            <span class="text-gray-500 dark:text-gray-400 text-sm">(Select one or more)</span>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                @foreach ($permissions as $permission)
                    <label class="inline-flex items-center space-x-2 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->id }}"
                            @checked(is_array(old('permissions')) && in_array($permission->id, old('permissions')))
                            class="text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <span class="text-sm text-gray-800 dark:text-gray-200">
                            {{ $permission->display_name ?? $permission->name }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center px-5 py-2 text-sm font-medium text-white bg-green-600 rounded
                           hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:outline-none
                           dark:bg-green-700 dark:hover:bg-green-600 transition">
                Save Role
            </button>
        </div>
    </form>
@endsection
