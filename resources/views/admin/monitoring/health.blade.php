<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('System Health Monitoring') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Database Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Database</p>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-1">
                                {{ ucfirst($status['database']['status']) }}
                            </h3>
                        </div>
                        <div class="p-3 rounded-full {{ $status['database']['status'] === 'ok' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm {{ $status['database']['status'] === 'ok' ? 'text-green-600' : 'text-red-600' }}">
                        Latency: {{ $status['database']['latency'] }}
                    </div>
                </div>

                <!-- Cache Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cache System</p>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-1">
                                {{ ucfirst($status['cache']['status']) }}
                            </h3>
                        </div>
                        <div class="p-3 rounded-full {{ $status['cache']['status'] === 'ok' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm {{ $status['cache']['status'] === 'ok' ? 'text-green-600' : 'text-red-600' }}">
                        Latency: {{ $status['cache']['latency'] }}
                    </div>
                </div>

                <!-- Disk Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Disk Space</p>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-1">
                                {{ $status['disk']['percentage'] }} Used
                            </h3>
                        </div>
                        <div class="p-3 rounded-full {{ $status['disk']['status'] === 'ok' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ $status['disk']['free_gb'] }} free of {{ $status['disk']['total_gb'] }}
                    </div>
                </div>

                <!-- Payment Gateway Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Gateway</p>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-1">
                                {{ ucfirst($status['payment_gateway']['status']) }}
                            </h3>
                        </div>
                        <div class="p-3 rounded-full {{ $status['payment_gateway']['status'] === 'ok' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm {{ $status['payment_gateway']['status'] === 'ok' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $status['payment_gateway']['message'] }}
                        @if($status['payment_gateway']['latency'])
                            ({{ $status['payment_gateway']['latency'] }})
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detailed System Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Environment Information</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP Version</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ phpversion() }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laravel Version</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ app()->version() }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Environment</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ app()->environment() }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Debug Mode</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ config('app.debug') ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
            
            <div class="mt-6 text-center">
                 <a href="{{ route('admin.monitoring.health') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Refresh Status
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
