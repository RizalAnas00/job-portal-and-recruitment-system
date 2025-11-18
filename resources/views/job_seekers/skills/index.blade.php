<x-app-layout>
    <x-slot name="breadcrumb">
        Skill Saya
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col gap-2 mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Kelola Skill Anda</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pilih skill yang menggambarkan kemampuan Anda. Sistem akan menggunakan data ini untuk rekomendasi lowongan.
                        </p>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif
                    <x-input-error :messages="$errors->all()" class="mb-4" />

                    <form method="POST" action="{{ route('user.skills.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="skills" value="Daftar Skill" />
                            <select id="skills" name="skills[]" multiple size="8"
                                class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                @foreach ($allSkills as $skill)
                                    <option value="{{ $skill->id }}" @selected($mySkillIds->contains($skill->id))>
                                        {{ $skill->skill_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Tekan Ctrl/Cmd untuk memilih lebih dari satu skill.
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>
                                Simpan Perubahan Skill
                            </x-primary-button>
                        </div>
                    </form>

                    <div class="mt-10">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Skill Saat Ini</h3>

                        @if ($mySkills->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Anda belum menambahkan skill apa pun. Tambahkan skill untuk membuat rekomendasi lowongan lebih akurat.
                            </p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($mySkills as $skill)
                                    <form method="POST" action="{{ route('user.skills.destroy', $skill) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 px-3 py-1.5 rounded-full text-xs font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900 transition">
                                            {{ $skill->skill_name }}
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

