@extends('role.layout')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('role.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
            ← Back
        </a>
    </div>

    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Edit Role</h2>

    <form action="{{ route('role.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 dark:text-gray-200">Role Name</label>
            <input type="text" id="name" name="name" class="w-full px-4 py-2 text-sm rounded border border-gray-300" value="{{ $role->name }}" required>
        </div>

        <div class="mb-4">
            <label for="permissions" class="block text-gray-700 dark:text-gray-200">Assign Permissions</label>
            <select id="permissions" name="permissions[]" multiple class="w-full px-4 py-2 text-sm rounded border border-gray-300">
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->id }}" 
                        @if(in_array($permission->id, $role->permissions->pluck('id')->toArray())) selected @endif>
                        {{ $permission->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Update Role</button>
    </form>
@endsection
