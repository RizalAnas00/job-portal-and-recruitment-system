<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <form method="POST" action="{{ route('company.profile.update', $company) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

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
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                rows="4">{{ old('address', $company->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_description" value="Deskripsi" />
                            <textarea id="company_description" name="company_description"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                rows="4">{{ old('company_description', $company->company_description) }}</textarea>
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
