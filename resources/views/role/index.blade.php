@extends('role.layout')

@section('content')

    <div class="flex items-center justify-between mb-5">
        <!-- Add New Role Button -->
        <a href="{{ route('role.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow hover:bg-green-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Role
        </a>

        <!-- Search -->
        <form method="GET" action="{{ route('role.index') }}" class="relative w-72">
            <input type="text" name="search" placeholder="Search roles..." disabled
                class="w-full pl-4 pr-10 py-2 text-sm rounded-full border border-gray-200 bg-gray-100 text-gray-400 shadow-inner cursor-not-allowed
                       dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500 transition" 
                value="{{ request('search') }}">
            <button type="submit" disabled
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                </svg>
            </button>
        </form>
    </div>

    <x-table :headers="['ID', 'Name', 'Users', 'Actions']">
        @foreach ($roles as $role)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-4">{{ $role->id }}</td>
                <td class="px-6 py-4 font-bold">{{ $role->display_name }}</td>
                <td class="px-6 py-4">{{ $role->users_count ?? '—' }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('role.show', $role) }}" class="font-semibold text-green-600 dark:text-green-500 hover:underline">Detail</a> |
                    <a href="{{ route('role.edit', $role) }}" class="text-blue-600 dark:text-blue-500 hover:underline">Edit</a> |
                    
                    <!-- Delete Button -->
                    <button type="button" 
                        onclick="openDeleteModal('{{ route('role.destroy', $role) }}')"
                        class="font-semibold text-red-600 dark:text-red-500 hover:underline">
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-6">
        {{ $roles->onEachSide(5)->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-sm">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Confirm Deletion</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this role? This action cannot be undone.</p>

            <div class="flex justify-end gap-3">
                <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 text-sm rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300">
                    Cancel
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="px-4 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-700">
                        Yes, Delete it
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openDeleteModal(actionUrl) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        form.action = actionUrl;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }
</script>
@endsection
