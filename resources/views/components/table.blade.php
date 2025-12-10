<table class="w-full text-sm text-gray-500 dark:text-gray-300 rounded-lg overflow-hidden shadow">
    <thead class="text-xs text-gray-100 uppercase bg-primary-600 dark:bg-primary-500 dark:text-gray-900">
        <tr>
            @foreach ($headers as $header)
                <th scope="col" class="px-6 py-3 
                    {{ $loop->first ? 'rounded-tl-lg' : '' }} 
                    {{ $loop->last ? 'rounded-tr-lg' : '' }}">
                    {{ $header }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody class="text-center">
        {{ $slot }}
    </tbody>
</table>
