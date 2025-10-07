@extends('role.layout')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('role.index') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 
                  bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            ← Back
        </a>
    </div>

    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Edit Role</h2>

    <form action="{{ route('role.update', $role->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 dark:text-gray-200 mb-1">
                Role Name <span class="text-red-500">(required)</span>
            </label>
            <input type="text" id="name" name="name"
                   value="{{ old('name', $role->name) }}"
                   class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                          bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                          focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                          transition" required>
        </div>

        <div class="mb-4">
            <label for="display_name" class="block text-gray-700 dark:text-gray-200 mb-1">
                Display Name <span class="text-red-500">(required)</span>
                <span class="text-gray-500 dark:text-gray-400 text-sm">(The name displayed on the web interface)</span>
            </label>
            <input type="text" id="display_name" name="display_name"
                   value="{{ old('display_name', $role->display_name) }}"
                   class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                          bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                          focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                          transition" required>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 dark:text-gray-200 mb-1">Description</label>
            <textarea id="description" name="description" rows="2"
                class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                       bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                       focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                       transition">{{ old('description', $role->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-2">Assign Permissions</label>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                @foreach ($permissions as $permission)
                    <label class="inline-flex items-center space-x-2 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            @checked(in_array($permission->id, $role->permissions->pluck('id')->toArray())) 
                            class="text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-800 dark:text-gray-200">
                            {{ $permission->display_name ?? $permission->name }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mb-4 flex items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   @checked($role->is_active)
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="is_active" class="text-gray-700 dark:text-gray-200">Active</label>
        </div>

        <button type="submit" 
            class="inline-flex items-center px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition">
            Update Role
        </button>
    </form>
@endsection
