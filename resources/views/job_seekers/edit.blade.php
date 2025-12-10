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

                    <form method="POST" action="{{ route('user.job-seekers.update', $jobSeeker) }}" class="space-y-5" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- 
                            INPUT FOTO PROFIL DENGAN LIVE PREVIEW 
                            x-data menyimpan state 'photoPreview' yang defaultnya null
                        --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-5 mb-5" 
                             x-data="{ photoPreview: null }">
                            
                            <x-input-label for="profile_picture" value="Foto Profil" />
                            
                            <div class="mt-2 flex items-center gap-x-5">
                                
                                {{-- AREA FOTO --}}
                                <div class="shrink-0">
                                    {{-- 1. Tampilkan Preview jika user baru saja upload (photoPreview tidak null) --}}
                                    <div x-show="photoPreview" style="display: none;">
                                        <span class="block h-16 w-16 rounded-full bg-cover bg-center bg-no-repeat border border-gray-300 dark:border-gray-600"
                                              :style="'background-image: url(\'' + photoPreview + '\');'">
                                        </span>
                                    </div>

                                    {{-- 2. Tampilkan Foto Lama jika tidak ada preview baru --}}
                                    <div x-show="!photoPreview">
                                        @if($jobSeeker->profile_picture_path)
                                            <img class="h-16 w-16 object-cover rounded-full border border-gray-300 dark:border-gray-600" 
                                                 src="{{ asset('storage/' . $jobSeeker->profile_picture_path) }}" 
                                                 alt="{{ $jobSeeker->first_name }}" />
                                        @else
                                            <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                                @svg('carbon-user-avatar-filled', 'h-16 w-16')
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- INPUT FILE & BUTTON --}}
                                <div class="w-full relative">
                                    {{-- Input File Asli (Hidden tapi tetap berfungsi) --}}
                                    <input class="hidden" 
                                           id="profile_picture" 
                                           name="profile_picture" 
                                           type="file"
                                           accept="image/png, image/jpeg, image/jpg"
                                           x-ref="photo"
                                           x-on:change="
                                                const file = $refs.photo.files[0];
                                                if (file) {
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                                    reader.readAsDataURL(file);
                                                }
                                           ">

                                    {{-- Tombol Custom untuk Trigger Input --}}
                                    <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                                        Pilih Foto Baru
                                    </x-secondary-button>

                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" id="file_input_help">
                                        PNG, JPG or JPEG (MAX. 2MB).
                                    </p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                        </div>
                        {{-- Batas Input Foto Profil --}}

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
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                                required>{{ old('address', $jobSeeker->address) }}</textarea>
                        </div>

                        <div>
                            <x-input-label for="profile_summary" value="Ringkasan Profil (Opsional)" />
                            <textarea id="profile_summary" name="profile_summary" rows="4"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 rounded-md shadow-sm"
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
                                    dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600
                                    focus:ring-primary-500 dark:focus:ring-primary-600 p-2"
                            />

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
                            <a href="{{ route('dashboard') }}" class="text-sm text-primary-600 hover:text-primary-500">
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