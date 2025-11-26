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

                    <form method="POST" action="{{ route('user.job-seekers.store') }}" class="space-y-5">
                        @csrf

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

                        <div>
                            <x-input-label for="phone_number" value="Nomor Telepon" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full"
                                :value="old('phone_number')" autocomplete="tel" required />
                            <p class="text-sm text-gray-500 mt-1">
                                Gunakan nomor aktif agar perusahaan mudah menghubungi Anda.
                            </p>
                        </div>

                        <div>
                            <x-input-label for="address" value="Alamat Lengkap" />
                            <textarea id="address" name="address" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>{{ old('address') }}</textarea>
                        </div>

                        <div>
                            <x-input-label for="profile_summary" value="Ringkasan Profil (Opsional)" />
                            <textarea id="profile_summary" name="profile_summary" rows="4"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm"
                                placeholder="Ceritakan pengalaman, keahlian utama, atau tujuan karier Anda.">{{ old('profile_summary') }}</textarea>
                        </div>

                        <div>
                            <x-input-label for="skills" value="Skill yang Dikuasai" />
                            @php
                                $selectedSkills = collect(old('skills', $selectedSkillIds?->toArray() ?? []));
                            @endphp
                            <select id="skills" name="skills[]" multiple size="8"
                                class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                @foreach ($skills as $skill)
                                    <option value="{{ $skill->id }}" @selected($selectedSkills->contains($skill->id))>
                                        {{ $skill->skill_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Tekan Ctrl/Cmd untuk memilih lebih dari satu skill. Anda dapat mengubahnya kapan saja.
                            </p>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                Simpan Profil
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

