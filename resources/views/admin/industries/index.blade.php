<x-app-layout>
    <x-slot name="breadcrumb">
        Master Data - Industries
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-3 lg:px-5">
            <div class="pb-2 text-gray-100">
                <div class="mb-5 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Kelola Industri</h1>
                    <a href="{{ route('admin.industries.create') }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                        + Tambah Industri
                    </a>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
                    <form method="GET" action="{{ route('admin.industries.index') }}" class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                            <input type="text" name="search" placeholder="Cari nama industri..."
                                class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                                value="{{ request('search') }}">
                        </div>
                        <div class="min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" 
                                class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                <option value="">Semua</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                            Filter
                        </button>
                    </form>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <x-table :headers="['ID', 'Nama', 'Deskripsi', 'Status', 'Actions']">
                    @forelse ($industries as $industry)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-4">{{ $industry->id }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $industry->name }}</td>
                            <td class="px-6 py-4">{{ Str::limit($industry->description ?? '—', 50) }}</td>
                            <td class="px-6 py-4">
                                @if($industry->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.industries.edit', $industry) }}"
                                        class="text-xs px-2 py-1 rounded bg-primary-100 text-primary-800 hover:bg-primary-200">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.industries.destroy', $industry) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus industri ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs px-2 py-1 rounded bg-red-100 text-red-800 hover:bg-red-200">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada industri ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                <div class="mt-6">
                    {{ $industries->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

