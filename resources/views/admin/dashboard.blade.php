<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Selamat Datang, Admin! 👋</h3>
                    <p class="mb-6">Ini adalah halaman dashboard utama admin.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Card Menu ke Subscription Plan --}}
                        {{-- PERBAIKAN: Sekarang pakai UNDERSCORE (_) --}}
                        <a href="{{ route('admin.subscription_plans.index') }}" class="block p-6 bg-blue-50 border border-blue-200 rounded-lg shadow hover:bg-blue-100 transition">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">📦 Paket Langganan</h5>
                            <p class="font-normal text-gray-700">Kelola harga, durasi, dan benefit paket (Subscription Plans).</p>
                        </a>

                        {{-- Card Menu Dummy Lainnya --}}
                        <div class="block p-6 bg-gray-50 border border-gray-200 rounded-lg shadow opacity-75">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">👥 Users</h5>
                            <p class="font-normal text-gray-700">Manage pengguna (Coming Soon).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>