<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="min-h-screen flex flex-col md:flex-row">

        <!-- Left Background Section -->
        <div 
            class="hidden md:flex md:w-1/2 relative bg-cover bg-center bg-no-repeat" 
            style="background-image: url('{{ asset('images/loginimgbg.webp') }}');"
        >
        
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-gray-700/80 to-gray-900/80 dark:from-primary-800/70 dark:to-primary-900/70 backdrop-blur-[3px]"></div>
            {{-- <x-application-logo class="h-32 z-10 w-auto text-white absolute top-14 left-1/2 transform -translate-x-1/2 -translate-y-1/2" /> --}}

            <!-- Content over Background -->
            <div class="relative z-10 flex flex-col items-center justify-center w-full text-center text-white p-10">
                <h1 class="text-4xl font-bold mb-4">Selamat Datang di {{ config('app.name') }}</h1>
                <p class="text-sm mb-6 max-w-md">Temukan pengalaman terbaik bersama kami. Cari Peluangmu. Cari Orang yang Tepat.</p>
            </div>
        </div>

        <!-- Right: Auth Form -->
        <div class="flex w-full md:w-1/2 items-center justify-center bg-gray-50 dark:bg-gray-900">
            <div class="w-full max-w-md px-8 py-10">
                <div class="flex justify-center mb-6">
                    <a href="/" class="flex items-center space-x-2">
                        <x-application-logo-light class="h-24 w-auto text-primary-600 dark:text-primary-400" />
                        <span class="font-semibold text-primary-600 dark:text-primary-400 text-lg">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
