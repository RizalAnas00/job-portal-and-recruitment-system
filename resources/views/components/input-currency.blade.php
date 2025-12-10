@props([
    'name',
    'label' => null,
    'value' => null,
])

@php
    $formatted = $value ? number_format($value, 0, ',', '.') : '';
@endphp

<div class="mb-4">
    @if($label)
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    <input type="text"
        id="{{ $name }}_display"
        value="{{ $formatted }}"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
               focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
               dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">

    <input type="hidden" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}">

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const display = document.getElementById('{{ $name }}_display');
    const hidden = document.getElementById('{{ $name }}');

    const formatRupiah = (angka) =>
        angka.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    display.addEventListener('input', () => {
        let num = display.value.replace(/\D/g,'');
        display.value = formatRupiah(num);
        hidden.value = num || '';
    });
});
</script>
