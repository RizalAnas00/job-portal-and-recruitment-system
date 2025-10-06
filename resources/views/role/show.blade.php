@extends('role.layout')

@section('content')
    <?php $roles = [
        (object) ['id' => 1, 'name' => 'Admin', 'users' => 3],
        (object) ['id' => 2, 'name' => 'Job Seeker', 'users' => 420],
        (object) ['id' => 3, 'name' => 'Company', 'users' => 30],
    ]; ?>
    <div class="flex items-center justify-between mb-5">
        <!-- Tombol Back -->
        <a href="{{ route('role.index') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
            ← Back
        </a>

        <!-- Tombol Edit -->
        <a href="#"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
            Edit
        </a>
    </div>

    <!-- Card Role Detail -->
    <div class="py-6">
        <x-table :headers="['ID', 'Name', 'Users']">
            <tr class="bg-white text-lg border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-4">{{ $roles[$role - 1]->id }}</td>
                <td class="px-6 py-4 font-bold">{{ $roles[$role - 1]->name }}</td>
                <td class="px-6 py-4">{{ $roles[$role - 1]->users }}</td>
            </tr>
        </x-table>
    </div>

    <!-- Card Permission -->
    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Permissions</h2>

    @php
        // Dummy data permission (sementara di view)
        $permissions = [
            (object)['id' => 1, 'name' => 'create user'],
            (object)['id' => 2, 'name' => 'edit user'],
            (object)['id' => 3, 'name' => 'delete user'],
            (object)['id' => 4, 'name' => 'view reports'],
        ];
    @endphp

    <x-table :headers="['ID', 'Name']">
        @forelse($permissions as $permission)
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
