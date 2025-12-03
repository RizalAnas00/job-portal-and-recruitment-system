<div class="flex gap-6">

    {{-- Left: Grid list --}}
    <div class="{{ $selectedResume ? 'w-1/2' : 'w-full' }}">
        <h1 class="text-2xl font-bold mb-4">Resume Saya</h1>
        <div class="flex justify-end mb-4">
        <button wire:click="openUploadModal" 
                class="px-4 py-2 bg-primary-600 text-white rounded">
            + Tambah Resume
        </button>
        </div>

        @if($showUploadModal)
            @include('livewire.resumes.upload-modal')
        @endif

        <div 
            class="grid gap-4 
                grid-cols-2 
                sm:grid-cols-3 
                md:grid-cols-3 
                lg:grid-cols-4"
        >
            @foreach ($resumes as $resume)
                @php
                    $isActive = $selectedResume && $selectedResume['id'] == $resume->id;
                @endphp

                <div 
                    wire:click="openResume({{ $resume->id }})"
                    class="border rounded-lg p-4 cursor-pointer shadow-sm transition 
                        bg-white dark:bg-gray-900
                        h-36 flex flex-col justify-center items-center text-center

                        {{ $isActive 
                                ? 'border-primary-400 shadow-md scale-105 bg-primary-50 dark:bg-primary-900/20' 
                                : 'hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-800' 
                        }}"
                >

                    <svg class="w-10 h-10 text-gray-500 mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 3h6l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                    </svg>

                    <p class="text-sm font-semibold line-clamp-1 text-gray-900 dark:text-gray-100">
                        {{ $resume->resume_title }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        {{ $isActive ? 'Klik untuk menutup' : 'Klik untuk membuka' }}
                    </p>

                </div>
            @endforeach
        </div>

    </div>

    @if ($selectedResume)
        <div 
            x-data="{ open: true }" 
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-10 opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-10 opacity-0"
            class="w-1/2 border-l border-l-primary-500 rounded-lg px-6 py-3 bg-gray-100 dark:bg-gray-900"
        >
            <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">
                {{ $selectedResume->resume_title }}
                {{-- <a 
                    href="{{ route('user.resume.download', $selectedResume) }}"
                    class="text-sm text-primary-600 hover:underline ml-4"
                >   
                    Download
                </a> --}}
                <a 
                    href="{{ route('user.resume.view', $selectedResume) }}"
                    target="_blank"
                    class="text-sm text-primary-600 dark:text-primary-400 hover:underline ml-2"
                >   
                    Lihat Full
                </a>
            </h2>

            <div class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Terakhir diperbarui:
                {{ \Carbon\Carbon::parse($selectedResume->updated_at)->format('d M Y') }}
            </div>

            <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                {!! nl2br(e($selectedResume->parsed_text)) !!}
            </div>
        </div>
    @endif

    {{-- <div class="prose dark:prose-invert max-w-none">

    @if ($selectedResume)
        <iframe 
            src="{{ asset('storage/' . $selectedResume['file_path']) }}"
            class="w-full h-[80vh] border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 shadow-sm"
        ></iframe>
    @endif

    </div> --}}

</div>
