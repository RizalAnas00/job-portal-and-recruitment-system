@props(['skill', 'checked' => false])

<label
    class="flex items-center gap-3 p-2 border rounded-lg cursor-pointer
           dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition
           {{ $checked ? 'ring-1 ring-primary-500 border-primary-500 bg-primary-50 dark:bg-primary-900/20' : '' }}"
>
    <input
        type="checkbox"
        name="skills[]"
        value="{{ $skill->id }}"
        @checked($checked)
        class="rounded border-gray-300 dark:border-gray-700
               focus:ring-primary-500 dark:focus:ring-primary-600
               {{ $checked ? 'text-primary-600' : 'text-primary-600/40' }}"
    >

    <span class="text-gray-900 dark:text-gray-100 text-sm font-medium">
        {{ $skill->skill_name }}
    </span>
</label>
