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

                    {{-- Form Start --}}
                    <form method="POST" action="{{ route('companies.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Input Logo dengan Live Preview --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-5 mb-5" 
                             x-data="{ logoPreview: null }">
                            
                            <x-input-label for="logo" value="Logo Perusahaan (Opsional)" />
                            
                            <div class="mt-2 flex items-center gap-x-5">
                                {{-- Area Preview --}}
                                <div class="shrink-0">
                                    {{-- Preview Logo Baru (Jika user sudah pilih file) --}}
                                    <div x-show="logoPreview" style="display: none;">
                                        <span class="block h-20 w-20 rounded-lg bg-cover bg-center bg-no-repeat border border-gray-300 dark:border-gray-600"
                                              :style="'background-image: url(\'' + logoPreview + '\');'">
                                        </span>
                                    </div>

                                    {{-- Placeholder Default (Gedung) --}}
                                    <div x-show="!logoPreview">
                                        <div class="h-20 w-20 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 border border-gray-300 dark:border-gray-600">
                                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tombol Upload --}}
                                <div class="w-full relative">
                                    {{-- Input File Hidden --}}
                                    <input class="hidden" 
                                           id="logo" 
                                           name="logo" 
                                           type="file"
                                           accept="image/png, image/jpeg, image/jpg"
                                           x-ref="logoInput"
                                           x-on:change="
                                                const file = $refs.logoInput.files[0];
                                                if (file) {
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { logoPreview = e.target.result; };
                                                    reader.readAsDataURL(file);
                                                }
                                           ">

                                    {{-- Tombol Pemicu --}}
                                    <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.logoInput.click()">
                                        Pilih Logo
                                    </x-secondary-button>

                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        PNG, JPG or JPEG (MAX. 2MB).
                                    </p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="company_name" :value="__('Nama Perusahaan')" />
                            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="industry" :value="__('Industri')" />
                            <x-text-input id="industry" class="block mt-1 w-full" type="text" name="industry" :value="old('industry')" required />
                            <p class="text-sm text-gray-500 mt-1">Contoh: Teknologi, Keuangan, Manufaktur, dll.</p>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="website" :value="__('Website')" />
                            <x-text-input id="website" class="block mt-1 w-full" type="url" name="website" :value="old('website')" placeholder="https://www.contoh.com" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="phone_number" :value="__('Nomor Telepon')" />
                            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="address" :value="__('Alamat Lengkap Perusahaan')" />
                            <textarea id="address" name="address" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required>{{ old('address') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="company_description" :value="__('Deskripsi Perusahaan')" />
                            <textarea id="company_description" name="company_description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required>{{ old('company_description') }}</textarea>
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