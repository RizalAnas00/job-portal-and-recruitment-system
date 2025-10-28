<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lengkapi Profil Perusahaan Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">
                    
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Selamat Datang!') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Akun Anda telah dibuat. Harap lengkapi detail perusahaan Anda untuk melanjutkan.") }}
                        </p>
                    </header>

                    <form method="POST" action="{{ route('company.profile.store') }}" class="mt-6 space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nama Perusahaan')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="company_description" :value="__('Deskripsi Singkat Perusahaan')" />
                            <textarea id="company_description" name="company_description" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full h-32">{{ old('company_description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('company_description')" />
                        </div>

                        <div>
                            <x-input-label for="industry" :value="__('Industri (cth: Teknologi, Keuangan, F&B)')" />
                            <x-text-input id="industry" name="industry" type="text" class="mt-1 block w-full" :value="old('industry')" />
                            <x-input-error class="mt-2" :messages="$errors->get('industry')" />
                        </div>

                        <div>
                            <x-input-label for="website" :value="__('Website Perusahaan')" />
                            <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website')" placeholder="https://contoh.com" />
                            <x-input-error class="mt-2" :messages="$errors->get('website')" />
                        </div>

                        <div>
                            <x-input-label for="phone_number" :value="__('Nomor Telepon (Kantor/HRD)')" />
                            <x-text-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full" :value="old('phone_number')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                        </div>

                        <div>
                            <x-input-label for="address" :value="__('Alamat Lengkap Perusahaan')" />
                            <textarea id="address" name="address" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full h-24">{{ old('address') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan Profil') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>