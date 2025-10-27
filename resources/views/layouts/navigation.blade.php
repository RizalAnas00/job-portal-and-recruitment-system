<nav x-data="{ open: false }" 
     class="bg-white dark:bg-gray-800/90 backdrop-blur-md shadow-md sticky z-20 m-3 rounded-lg">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-8 w-auto text-indigo-600 dark:text-indigo-400" />
                    <span class="font-bold text-lg text-gray-800 dark:text-gray-200">MyApp</span>
                </a>
            </div>

            <!-- Right Section -->
            <div class="hidden sm:flex sm:items-center gap-4">
                <!-- Profile -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-1 px-2.5 py-1.5 rounded-full bg-transparent
                                       text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600
                                       transition shadow-sm">
                            <img src="https://i.pravatar.cc/40" class="h-8 w-8 rounded-full border border-gray-300" />
                            <span class="hidden sm:block ml-2">{{ Auth::user()->email }}</span>
                            @if (Auth::user()->company && Auth::user()->company->is_verified)
                                @svg('gmdi-verified-s', 'h-5 w-5 text-blue-500 ml-1')
                            @endif
                            <svg class="h-4 w-4 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0
                                111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
