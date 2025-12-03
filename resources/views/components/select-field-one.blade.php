@props([
    'id',
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
])

<div class="w-full">
    @if ($label)
        <x-input-label :for="$id" :value="$label" />
    @endif

    <div 
        x-data="{ open: false, selectedText: '{{ $options[$selected] ?? 'Pilih...' }}' }"
        class="relative select-none"
    >
        <!-- hidden value for form -->
        <input type="hidden" name="{{ $name }}" value="{{ $selected }}">

        <!-- Trigger -->
        <button 
            type="button"
            @click="open = !open"
            class="mt-1 w-full flex justify-between items-center px-4 py-2.5
                   bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700
                   rounded-md text-left shadow-sm hover:bg-gray-50
                   dark:hover:bg-gray-800 transition"
        >
            <span x-text="selectedText" class="text-sm dark:text-gray-300"></span>

            <svg 
                :class="open ? 'rotate-180 w-4 h-4 text-gray-500 dark:text-gray-400 ml-2' 
                            : 'w-4 h-4 text-gray-500 dark:text-gray-400 ml-2'"
                class="transition-transform duration-200"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown List -->
        <ul 
            x-show="open"
            @click.outside="open = false"
            x-transition.origin.top.left
            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 
                   rounded-md shadow-lg border border-gray-200 dark:border-gray-700
                   py-2 max-h-60 overflow-auto"
        >
            @foreach ($options as $value => $text)
                <li>
                    <button
                        type="button"
                        class="w-full text-left px-4 py-2 text-sm
                               hover:bg-indigo-50 dark:hover:bg-indigo-900/40
                               hover:text-indigo-600 dark:hover:text-indigo-300
                               dark:text-gray-200"
                        @click="
                            selectedText = '{{ $text }}';
                            $el.closest('[x-data]').querySelector('input').value = '{{ $value }}';
                            open = false;
                        "
                    >
                        {{ $text }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>
