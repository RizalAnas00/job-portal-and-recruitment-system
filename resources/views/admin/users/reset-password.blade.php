@extends('admin.users.layout')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300
                  dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:border dark:border-gray-700 transition">
            ← Kembali
        </a>
    </div>

    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Reset Password</h2>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-4">
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Email:</p>
            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $user->email }}</p>
        </div>
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Role:</p>
            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $user->role->display_name ?? '—' }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
          class="bg-white dark:bg-gray-900 shadow-inner dark:shadow-md dark:shadow-gray-800 rounded-lg p-6 transition">
        @csrf

        <div class="mb-4">
            <label for="new_password" class="block text-gray-700 dark:text-gray-200 font-medium mb-1">
                Password Baru <span class="text-red-500">*</span>
            </label>
            <input type="password" id="new_password" name="new_password"
                class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                        focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                        transition" 
                placeholder="Minimal 8 karakter"
                required>
            @error('new_password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="new_password_confirmation" class="block text-gray-700 dark:text-gray-200 font-medium mb-1">
                Konfirmasi Password Baru <span class="text-red-500">*</span>
            </label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                        focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                        transition" 
                placeholder="Ulangi password baru"
                required>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded
                           hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:outline-none
                           dark:bg-primary-700 dark:hover:bg-primary-600 transition">
                Reset Password
            </button>
        </div>
    </form>
@endsection

