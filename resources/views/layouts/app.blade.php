<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('icons/logolight.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Notification Counter Script - For Companies and Job Seekers --}}
    @if (Auth::check() && (Auth::user()->hasRole('company') || Auth::user()->hasRole('user')))
        <script>
            // Function to update unread notification count
            function updateUnreadCount() {
                fetch('{{ route('notifications.unread-count') }}')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('unread-notification-badge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 99 ? '99+' : data.count;
                                badge.style.display = 'flex';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching unread count:', error));
            }

            // Update count when page loads
            document.addEventListener('DOMContentLoaded', function() {
                updateUnreadCount();
                // Update every 30 seconds
                setInterval(updateUnreadCount, 30000);
            });
        </script>
    @endif
</head>

<body class="font-sans antialiased">
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
        @auth
            @include('layouts.sidebar') <!-- Sidebar di kiri -->
        @endauth

        <div class="flex flex-1 flex-col">
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
                <div
                    class="mt-4 max-w-7xl px-4 text-sm text-gray-600 sm:px-6 md:text-base lg:px-8 lg:text-lg dark:text-gray-300">
                    <span class="font-light text-gray-500 dark:text-gray-400">Pages /</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $breadcrumb }}</span>
                </div>
            @endif

            <!-- Page Content -->
            <main class="@auth p-6 @else pb-6 @endauth">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>

        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @stack('scripts')
</body>

</html>
