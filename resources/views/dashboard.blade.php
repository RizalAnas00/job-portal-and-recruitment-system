<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
           {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <x-slot name="breadcrumb">
        Dashboard
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-5 lg:px-7">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Anda Sudah Login sebagai
                    <span class="ml-1 font-extrabold text-primary-600 dark:text-primary-300">
                        {{ strtoupper(Auth::user()->getRoleName()) }}
                    </span>
                    dengan email
                    <span class="ml-1 font-extrabold text-primary-600 dark:text-primary-300">
                        {{ Auth::user()->email }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
