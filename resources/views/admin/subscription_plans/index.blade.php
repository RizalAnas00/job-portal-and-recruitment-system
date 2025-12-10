<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Paket Langganan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.subscription_plans.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Tambah Paket Baru
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Paket</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Limit Post</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Badge</th>
                                {{-- KOLOM BARU DITAMBAHKAN --}}
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                {{-- END KOLOM BARU --}}
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plans as $plan)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold">{{ $plan->plan_name }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">Rp {{ number_format($plan->price, 0, ',', '.') }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $plan->duration_days }} Hari</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $plan->job_post_limit }} Jobs</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    @if($plan->allow_verified_badge)
                                        <span class="bg-green-200 text-green-800 py-1 px-2 rounded-full text-xs">Ya</span>
                                    @else
                                        <span class="bg-gray-200 text-gray-800 py-1 px-2 rounded-full text-xs">Tidak</span>
                                    @endif
                                </td>
                                {{-- SEL BARU DITAMBAHKAN (STATUS IS_ACTIVE) --}}
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    @if($plan->is_active)
                                        <span class="bg-blue-200 text-blue-800 py-1 px-2 rounded-full text-xs font-semibold">Aktif</span>
                                    @else
                                        <span class="bg-red-200 text-red-800 py-1 px-2 rounded-full text-xs font-semibold">Nonaktif</span>
                                    @endif
                                </td>
                                {{-- END SEL BARU --}}
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <a href="{{ route('admin.subscription_plans.edit', $plan->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    
                                    <form action="{{ route('admin.subscription_plans.destroy', $plan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus paket ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">Belum ada paket data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>