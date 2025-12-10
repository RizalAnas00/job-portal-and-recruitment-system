@props(['title', 'open' => false])

@php
    $open = filter_var($open, FILTER_VALIDATE_BOOL); // pastikan boolean
@endphp

<div x-data="{ open: @js($open) }" 
     class="border rounded-xl overflow-hidden dark:border-gray-700">

    {{-- Header --}}
    <button @click="open = !open"
        class="w-full flex justify-between items-center px-5 py-4 
               bg-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 
               font-semibold text-gray-900 dark:text-white">

        <span>{{ $title }}</span>

        <span class="transition-transform duration-300"
              :class="open ? 'rotate-180' : ''">
            @svg('carbon-chevron-down','h-5 w-5')
        </span>
    </button>

    {{-- Content --}}
    <div x-show="open" x-collapse
         class="p-5 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
        {{ $slot }}
    </div>
</div>
