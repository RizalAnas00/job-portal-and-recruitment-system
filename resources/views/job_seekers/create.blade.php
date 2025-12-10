<x-app-layout>
    <x-slot name="breadcrumb">
        Buat Profil Pencari Kerja
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        Lengkapi profil Anda sebelum mulai melamar pekerjaan
                    </h2>

                    <x-auth-session-status class="mb-4" :status="session('error')" />
                    <x-input-error :messages="$errors->all()" class="mb-4" />

                    {{-- Form Start --}}
                    <form method="POST" action="{{ route('user.job-seekers.store') }}" class="space-y-5" enctype="multipart/form-data">
                        @csrf

                        {{-- Input Foto Profil dengan Preview --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-5 mb-5" 
                             x-data="{ photoPreview: null }">
                            
                            <x-input-label for="profile_picture" value="Foto Profil (Opsional)" />
                            
                            <div class="mt-2 flex items-center gap-x-5">
                                {{-- Area Preview Foto --}}
                                <div class="shrink-0">
                                    <div x-show="photoPreview" style="display: none;">
                                        <span class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat border border-gray-300 dark:border-gray-600"
                                              :style="'background-image: url(\'' + photoPreview + '\');'">
                                        </span>
                                    </div>
                                    <div x-show="!photoPreview">
                                        <div class="h-20 w-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 border border-gray-300 dark:border-gray-600">
                                            @svg('carbon-user-avatar-filled', 'h-20 w-20')
                                        </div>
                                    </div>
                                </div>

                                {{-- Input File Button --}}
                                <div class="w-full relative">
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

                                    <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                                        Pilih Foto
                                    </x-secondary-button>

                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        PNG, JPG or JPEG (MAX. 2MB).
                                    </p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                        </div>

                        {{-- Nama --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="first_name" value="Nama Depan" />
                                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full"
                                    :value="old('first_name')" autocomplete="given-name" required autofocus />
                            </div>

                            <div>
                                <x-input-label for="last_name" value="Nama Belakang (Opsional)" />
                                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full"
                                    :value="old('last_name')" autocomplete="family-name" />
                            </div>
                        </div>

                        {{-- Kontak --}}
                        <div>
                            <x-input-label for="phone_number" value="Nomor Telepon" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full"
                                :value="old('phone_number')" autocomplete="tel" required />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Gunakan nomor aktif (WhatsApp) agar perusahaan mudah menghubungi Anda.
                            </p>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <x-input-label for="address" value="Alamat Lengkap" />
                            <textarea id="address" name="address" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                                required>{{ old('address') }}</textarea>
                        </div>

                        {{-- Ringkasan --}}
                        <div>
                            <x-input-label for="profile_summary" value="Ringkasan Profil (Opsional)" />
                            <textarea id="profile_summary" name="profile_summary" rows="4"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 rounded-md shadow-sm"
                                placeholder="Ceritakan pengalaman singkat, keahlian utama, atau tujuan karier Anda.">{{ old('profile_summary') }}</textarea>
                        </div>

                        {{-- Skill Selection dengan Pencarian --}}
                        <div x-data="{ search: '' }">
                            <x-input-label for="skills" value="Skill yang Dikuasai" />

                            <input
                                type="text"
                                x-model="search"
                                placeholder="Cari skill..."
                                class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600
                                    focus:ring-primary-500 dark:focus:ring-primary-600 p-2 text-sm"
                            />

                            <div class="mt-3 p-4 border border-gray-300 dark:border-gray-700 rounded-xl 
                                            bg-white dark:bg-gray-900 max-h-60 overflow-y-auto">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($skills as $skill)
                                        <div x-show="{{ json_encode(strtolower($skill->skill_name)) }}.includes(search.toLowerCase())"
                                             x-transition>
                                            <x-check-box-one
                                                :skill="$skill"
                                                :checked="collect(old('skills', []))->contains($skill->id)"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Pilih keahlian yang relevan untuk meningkatkan peluang Anda.
                            </p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                Simpan Profil & Lanjutkan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>