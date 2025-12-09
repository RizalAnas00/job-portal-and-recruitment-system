@extends('applications.layout')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Title --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            Lamar Pekerjaan
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 border border-gray-200 dark:border-gray-700">

        {{-- Flash Error --}}
        @if(session('error'))
            <div class="mb-4 p-3 rounded border border-red-500 text-red-700 bg-red-100 dark:bg-red-200 dark:text-red-900">
                {{ session('error') }}
            </div>
        @endif


        {{-- FORM --}}
        <form action="{{ route('user.applications.store', $jobPosting->id) }}" method="POST">
            @csrf

            {{-- Resume Selection --}}
            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-3">
                    Pilih Resume yang Akan Dilampirkan
                </label>

                <input type="hidden" name="id_resume" id="selected_resume" required>

                <div class="grid md:grid-cols-2 gap-4">
                    @foreach ($userResumes as $resume)
                        <div 
                            class="resume-card cursor-pointer p-4 rounded-xl border border-gray-300 dark:border-gray-700 
                            bg-gray-50 dark:bg-gray-900 hover:shadow-md transition relative">

                            {{-- Icon --}}
                            <div class="flex items-center gap-3">
                                <div class="text-4xl text-primary-500 dark:text-primary-400">
                                    📄
                                </div>
                                <div>
                                    <p class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                        {{ $resume->resume_title }}
                                    </p>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Diunggah {{ $resume->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Summary --}}
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-3 line-clamp-2">
                                {{ $resume->parsed_text ? Str::limit($resume->parsed_text, 200) : 'Tidak ada ringkasan tersedia.' }}
                            </p>

                            <a href="{{ route('user.resume.view', $resume) }}" target="_blank"
                                class="text-primary-600 dark:text-primary-400 text-sm font-nomal hover:underline mt-3 inline-block">
                                Lihat Dokumen
                            </a>

                            {{-- Badge DIPILIH --}}
                            <div class="selected-badge hidden absolute top-2 right-2 
                                bg-primary-500 dark:bg-primary-700 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                Dipilih
                            </div>

                            <span class="resume-id hidden">{{ $resume->id }}</span>

                        </div>
                    @endforeach
                </div>

                @error('id_resume')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Cover Letter --}}
            <div class="mb-6">
                <label for="cover_letter" class="block font-medium text-gray-800 dark:text-gray-200 mb-1">
                    Cover Letter (Opsional)
                </label>

                <textarea name="cover_letter" id="cover_letter" rows="6"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 
                           text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500 px-3 py-2"
                    placeholder="Ceritakan mengapa Anda cocok untuk posisi ini...">{{ old('cover_letter') }}</textarea>
            </div>


            {{-- Submit --}}
            <div class="flex justify-end gap-3">
                <a href="{{ url()->previous() }}"
                   class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                          hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Batal
                </a>

                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-primary-600 dark:bg-primary-500 text-white font-semibold hover:bg-primary-700 dark:hover:bg-primary-400 shadow-sm transition">
                    Kirim Lamaran
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    const resumeCards = document.querySelectorAll(".resume-card");
    const input = document.querySelector("#selected_resume");

    resumeCards.forEach(card => {
        card.addEventListener("click", () => {
            resumeCards.forEach(c => {
                c.classList.remove("ring-2", "ring-primary-500", "dark:ring-primary-400");
                c.querySelector(".selected-badge").classList.add("hidden");
            });

            card.classList.add("ring-2", "ring-primary-500", "dark:ring-primary-400");
            card.querySelector(".selected-badge").classList.remove("hidden");

            input.value = card.querySelector(".resume-id").innerText;
        });
    });
</script>
@endsection
