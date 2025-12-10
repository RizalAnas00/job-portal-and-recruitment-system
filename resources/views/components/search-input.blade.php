@props([
    'name' => 'search',
    'placeholder' => 'Cari lowongan...',
    'value' => null,
    'width' => '200px'
])

<input type="text"
       name="{{ $name }}"
       value="{{ $value }}"
       placeholder="{{ $placeholder }}"
       class="border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-lg p-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
       style="width: {{ $width }};"
>
