<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Registering as')" />
            <select id="role" name="role"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700
                    dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm
                    focus:ring-indigo-500 focus:border-indigo-500">
                <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>
                    {{ __('Job Seeker (default)') }}
                </option>
                <option value="company" {{ old('role') === 'company' ? 'selected' : '' }}>
                    {{ __('Company') }}
                </option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}"
                class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600
                    dark:hover:text-indigo-400 underline transition">
                {{ __('Already have an account?') }}
            </a>

            <x-ripple-button type="submit" class="ms-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md">
                <x-slot name="buttonText">{{ __('Register') }}</x-slot>
            </x-ripple-button>
        </div>
    </form>
</x-guest-layout>
