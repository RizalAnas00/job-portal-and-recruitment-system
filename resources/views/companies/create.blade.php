<x-app-layout>
    <x-slot name="breadcrumb">
        Buat Profil Perusahaan
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        Lengkapi Profil Perusahaan Anda
                    </h2>

                    {{-- Tampilkan error validasi --}}
                    <x-auth-session-status class="mb-4" :status="session('error')" />
                    <x-input-error :messages="$errors->all()" class="mb-4" />

                    <form method="POST" action="{{ route('companies.store') }}">
                        @csrf

                        <!-- Company Name -->
                        <div>
                            <x-input-label for="company_name" :value="__('Nama Perusahaan')" />
                            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus />
                        </div>

                        <!-- Industry -->
                        <div class="mt-4">
                            <x-input-label for="industry" :value="__('Industri')" />
                            <x-text-input id="industry" class="block mt-1 w-full" type="text" name="industry" :value="old('industry')" required />
                            <p class="text-sm text-gray-500 mt-1">Contoh: Teknologi, Keuangan, Manufaktur, dll.</p>
                        </div>

                        <!-- Website -->
                        <div class="mt-4">
                            <x-input-label for="website" :value="__('Website')" />
                            <x-text-input id="website" class="block mt-1 w-full" type="url" name="website" :value="old('website')" required placeholder="https://www.contoh.com" />
                        </div>

                        <!-- Phone Number -->
                        <div class="mt-4">
                            <x-input-label for="phone_number" :value="__('Nomor Telepon')" />
                            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required />
                        </div>

                        <!-- Address -->
                        <div class="mt-4">
                            <x-input-label for="address" :value="__('Alamat Lengkap Perusahaan')" />
                            <textarea id="address" name="address" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('address') }}</textarea>
                        </div>

                        <!-- Company Description -->
                        <div class="mt-4">
                            <x-input-label for="company_description" :value="__('Deskripsi Perusahaan')" />
                            <textarea id="company_description" name="company_description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('company_description') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Jelaskan tentang perusahaan Anda, visi, misi, dan budaya kerja.</p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Simpan Profil Perusahaan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>