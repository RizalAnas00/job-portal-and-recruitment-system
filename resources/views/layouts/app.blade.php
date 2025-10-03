<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Scripts -->

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
            @include('layouts.sidebar') <!-- Sidebar di kiri -->

            <div class="flex-1 flex flex-col">
                @include('layouts.navigation') <!-- Top Navbar -->

                {{-- <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow-md">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset --}}

                <!-- Breadcrumb -->
                @if (isset($breadcrumb))
                    <div class="max-w-7xl px-4 sm:px-6 lg:px-8 mt-8 text-sm md:text-base lg:text-lg text-gray-600 dark:text-gray-300">
                        <span class="text-gray-500 dark:text-gray-400 font-light">Pages /</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $breadcrumb }}</span>
                    </div>
                @endif
 
                <!-- Page Content -->
                <main class="p-6">
                    {{ $slot }}
                </main>
 
            </div>
        </div>
    </body>
</html>
