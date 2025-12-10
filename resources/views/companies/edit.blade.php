<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    
                    {{-- PERUBAHAN 1: Tambah enctype --}}
                    <form method="POST" action="{{ route('company.profile.update', $company) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- PERUBAHAN 2: Input Logo dengan Preview --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-5 mb-5" 
                            x-data="{ logoPreview: null }">
                            
                            <x-input-label for="logo" value="Logo Perusahaan" />
                            
                            <div class="mt-2 flex items-center gap-x-5">
                                
                                {{-- Area Preview Foto --}}
                                <div class="shrink-0">
                                    {{-- Preview Logo Baru --}}
                                    <div x-show="logoPreview" style="display: none;">
                                        <span class="block h-20 w-20 rounded-lg bg-cover bg-center bg-no-repeat border border-gray-300 dark:border-gray-600"
                                            :style="'background-image: url(\'' + logoPreview + '\');'">
                                        </span>
                                    </div>

                                    {{-- Tampilan Logo Lama --}}
                                    <div x-show="!logoPreview">
                                        @if($company->logo_path)
                                            <img class="h-20 w-20 object-cover rounded-lg border border-gray-300 dark:border-gray-600" 
                                                src="{{ asset('storage/' . $company->logo_path) }}" 
                                                alt="{{ $company->company_name }}" />
                                        @else
                                            <div class="h-20 w-20 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 border border-gray-300 dark:border-gray-600">
                                                {{-- Default Icon --}}
                                                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Tombol Upload --}}
                                <div class="w-full relative">
                                    {{-- 
                                        PENTING: 
                                        1. x-ref="logoInput" harus sama dengan pemanggilan di tombol ($refs.logoInput)
                                        2. name="logo" harus sama dengan validasi di controller
                                    --}}
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
                                        Upload Logo
                                    </x-secondary-button>

                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        PNG, JPG or JPEG (MAX. 2MB).
                                    </p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_name" value="Nama Perusahaan" />
                            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full"
                                value="{{ old('company_name', $company->company_name) }}" required />
                            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="industry" value="Industri" />
                            <x-text-input id="industry" name="industry" type="text" class="mt-1 block w-full"
                                value="{{ old('industry', $company->industry) }}" required />
                            <x-input-error :messages="$errors->get('industry')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="website" value="Website" />
                            <x-text-input id="website" name="website" type="url" class="mt-1 block w-full"
                                value="{{ old('website', $company->website) }}" />
                            <x-input-error :messages="$errors->get('website')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone_number" value="No Telepon" />
                            <x-text-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full"
                                value="{{ old('phone_number', $company->phone_number) }}" required />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="address" value="Alamat" />
                            <textarea id="address" name="address"
                                class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                rows="4" required>{{ old('address', $company->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_description" value="Deskripsi" />
                            <textarea id="company_description" name="company_description"
                                class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                rows="4" required>{{ old('company_description', $company->company_description) }}</textarea>
                            <x-input-error :messages="$errors->get('company_description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>