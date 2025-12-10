<x-app-layout>
    <x-slot name="breadcrumb">
        Role Management
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-3 lg:px-5">
            <div class="pb-2 text-gray-100">
                @yield('content')
            </div>
        </div>
    </div>

    @yield('scripts')
</x-app-layout>
