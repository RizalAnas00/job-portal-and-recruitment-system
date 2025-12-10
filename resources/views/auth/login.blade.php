<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input 
                id="password" 
                class="block mt-1 w-full"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center space-x-2">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 dark:bg-gray-900 dark:border-gray-700"
                    name="remember">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <x-ripple-button type="submit" 
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-lg transition duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2">
                <x-slot name="buttonText">{{ __('Log In') }}</x-slot>
            </x-ripple-button>
        </div>

        <!-- Register Link -->
        @if (Route::has('register'))
            <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" 
                   class="font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                    {{ __('Register now!') }}
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
