<x-app-layout>
    <x-slot name="breadcrumb">
        @if (Auth::user()->hasRole('company'))
            {{  __('Pelamar') }}
        @elseif(Auth::user()->hasRole('user'))
            {{  __('Lamaran Saya') }}
        @endif
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
