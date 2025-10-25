<x-app-layout>
    <x-slot name="breadcrumb">
        Subscriptions List
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-3 lg:px-5">
            <div class="pb-3 text-gray-100">
                @yield('content')
            </div>
        </div>
    </div>

    @yield('scripts')
</x-app-layout>
