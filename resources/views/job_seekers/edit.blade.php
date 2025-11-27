<x-app-layout>
    <x-slot name="breadcrumb">
        Edit Profil Pencari Kerja
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        Perbarui informasi Anda agar perusahaan dapat mengenal Anda lebih baik
                    </h2>

                    @if (session('success'))
                        <div class="mb-4 text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                    <x-input-error :messages="$errors->all()" class="mb-4" />

                    <form method="POST" action="{{ route('user.job-seekers.update') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="first_name" value="Nama Depan" />
                                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full"
                                    :value="old('first_name', $jobSeeker->first_name)" autocomplete="given-name" required autofocus />
                            </div>

                            <div>
                                <x-input-label for="last_name" value="Nama Belakang (Opsional)" />
                                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full"
                                    :value="old('last_name', $jobSeeker->last_name)" autocomplete="family-name" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="phone_number" value="Nomor Telepon" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full"
                                :value="old('phone_number', $jobSeeker->phone_number)" autocomplete="tel" required />
                        </div>

                        <div>
                            <x-input-label for="address" value="Alamat Lengkap" />
                            <textarea id="address" name="address" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>{{ old('address', $jobSeeker->address) }}</textarea>
                        </div>

                        <div>
                            <x-input-label for="profile_summary" value="Ringkasan Profil (Opsional)" />
                            <textarea id="profile_summary" name="profile_summary" rows="4"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm"
                                placeholder="Ceritakan pengalaman, keahlian utama, atau tujuan karier Anda.">{{ old('profile_summary', $jobSeeker->profile_summary) }}</textarea>
                        </div>

                        <div x-data="{ search: '' }">
                            <x-input-label for="skills" value="Skill yang Dikuasai" />

                            @php
                                $selectedSkills = collect(old('skills', $selectedSkillIds?->toArray() ?? []));
                            @endphp

                            <input
                                type="text"
                                x-model="search"
                                placeholder="Cari skill..."
                                class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600
                                    focus:ring-indigo-500 dark:focus:ring-indigo-600 p-2"
                            />

                            <!-- Scroll Container -->
                            <div class="mt-3 p-4 border border-gray-300 dark:border-gray-700 rounded-xl 
                                        bg-white dark:bg-gray-900 max-h-80 overflow-y-auto">

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                                    @foreach ($skills as $skill)
                                        <div
                                            x-show="{{ json_encode(
                                                strtolower($skill->skill_name)
                                            ) }}.includes(search.toLowerCase())"
                                            x-transition
                                        >
                                            <x-check-box-one
                                                :skill="$skill"
                                                :checked="$selectedSkills->contains($skill->id)"
                                            />
                                        </div>
                                    @endforeach

                                </div>

                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Centang skill yang sesuai dengan kemampuan Anda.
                            </p>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Kembali ke Dashboard
                            </a>

                            <x-primary-button>
                                Simpan Perubahan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

