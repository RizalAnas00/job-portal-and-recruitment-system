@extends('role.layout')

@section('content')
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
                    <a href="#" class="font-semibold text-green-600 dark:text-green-500 hover:underline">Detail</a> |
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
