@extends('role.layout')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <!-- Tombol Back -->
        <div class="flex items-center justify-between mb-5">
            <a href="{{ route('admin.role.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 dark:text-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">
                ← Back
            </a>
        </div>

        <a href="{{ route('admin.role.edit', $role) }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
            Edit
        </a>
    </div>

    <div class="py-6">
        @if ($role)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    Role Details
                </h2>

                <x-table :headers="['ID', 'Name', 'Display Name', 'Description', 'Users']">
                    <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                        <td class="px-6 py-4">{{ $role->id }}</td>
                        <td class="px-6 py-4 font-bold capitalize">{{ $role->name }}</td>
                        <td class="px-6 py-4">{{ $role->display_name }}</td>
                        <td class="px-6 py-4">{{ $role->description }}</td>
                        <td class="px-6 py-4">
                            {{ $role->users_count ?? '—' }}
                        </td>
                    </tr>
                </x-table>
            </div>
        @else
            <div class="text-center text-gray-600 dark:text-gray-400">
                Role not found.
            </div>
        @endif
    </div>

    <!-- Card Permission -->
    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Permissions</h2>

    <x-table :headers="['ID', 'Name']">
        @forelse($role->permissions as $permission)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-4">{{ $permission->id }}</td>
                <td class="px-6 py-4">{{ $permission->name }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="px-6 py-4 text-center text-gray-500">No permissions assigned</td>
            </tr>
        @endforelse
    </x-table>
@endsection
