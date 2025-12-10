<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Broadcast Notification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.broadcast-notifications.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="recipients" :value="__('Recipients')" />
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center">
                                    <input id="recipient_job_seekers" name="recipients[]" type="checkbox" value="job_seekers" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="recipient_job_seekers" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        All Job Seekers
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="recipient_companies" name="recipients[]" type="checkbox" value="companies" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="recipient_companies" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        All Companies
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('recipients')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="message" :value="__('Message')" />
                            <textarea id="message" name="message" rows="4" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required>{{ old('message') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">The notification message to be displayed to users.</p>
                            <x-input-error :messages="$errors->get('message')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="link_url" :value="__('Link URL (Optional)')" />
                            <x-text-input id="link_url" class="block mt-1 w-full" type="url" name="link_url" :value="old('link_url')" placeholder="https://..." />
                            <p class="mt-1 text-sm text-gray-500">Users will be redirected to this URL when they click the notification.</p>
                            <x-input-error :messages="$errors->get('link_url')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>
                                {{ __('Send Broadcast') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
