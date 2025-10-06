@extends('role.layout')

@section('content')

    <div class="flex items-center justify-between mb-5">
        <!-- Add New Role Button -->
        <a href="#"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow hover:bg-green-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Role
        </a>

        <!-- Modern Search (Disabled for now) -->
        <form method="GET" action="{{ route('role.index') }}" class="relative w-72">
            <input type="text" name="search" placeholder="Search roles..."
                disabled
                class="w-full pl-4 pr-10 py-2 text-sm rounded-full border border-gray-200 
                    bg-gray-100 text-gray-400 shadow-inner cursor-not-allowed
                    focus:outline-none 
                    dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500
                    transition" 
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
        <?php $roles = [
            (object) ['id' => 1, 'name' => 'Admin', 'users' => 3],
            (object) ['id' => 2, 'name' => 'Job Seeker', 'users' => 420],
            (object) ['id' => 3, 'name' => 'Company', 'users' => 30],
        ]; ?>
        @foreach ($roles as $role)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-4">{{ $role->id }}</td>
                <td class="px-6 py-4 font-bold">{{ $role->name }}</td>
                <td class="px-6 py-4">{{ $role->users }}</td>
                <td class="px-6 py-4">
                    
                    <!-- TODO -->
                    <!-- Use Model Binding to pass the role instead of id 
                        Maybe use icons instead of text -->

                    <a href=" {{ route('role.show', $role->id) }}" class="font-semibold text-green-600 dark:text-green-500 hover:underline">Detail</a> |
                    <a href="#" class="text-blue-600 dark:text-blue-500 hover:underline">Edit</a> |
                    <form action="#" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="font-semibold text-red-600 dark:text-red-500 hover:underline"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-table>
@endsection
