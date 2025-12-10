<x-app-layout>
    <x-slot name="breadcrumb">
        Company Moderation
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-3 lg:px-5">
            <div class="pb-2 text-gray-100">
                <div class="mb-5">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Kelola Perusahaan</h1>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Perusahaan</div>
                            <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['total'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Terverifikasi</div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['verified'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Belum Terverifikasi</div>
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['unverified'] }}</div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
                        <form method="GET" action="{{ route('admin.companies.index') }}" class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                                <input type="text" name="search" placeholder="Cari nama perusahaan, email, phone..."
                                    class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                                    value="{{ request('search') }}">
                            </div>
                            <div class="min-w-[150px]">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Verifikasi</label>
                                <select name="verification_status" 
                                    class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                    <option value="">Semua</option>
                                    <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                                    <option value="unverified" {{ request('verification_status') === 'unverified' ? 'selected' : '' }}>Belum Terverifikasi</option>
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                                Filter
                            </button>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <x-table :headers="['ID', 'Nama Perusahaan', 'Email', 'Industri', 'Status', 'Actions']">
                    @forelse ($companies as $company)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-4">{{ $company->id }}</td>
                            <td class="px-6 py-4">{{ $company->company_name }}</td>
                            <td class="px-6 py-4">{{ $company->user->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $company->industry->name ?? $company->industry ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($company->is_verified)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Belum Terverifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.companies.edit', $company) }}"
                                        class="text-xs px-2 py-1 rounded bg-primary-100 text-primary-800 hover:bg-primary-200">
                                        Edit
                                    </a>
                                    @if(!$company->is_verified)
                                        <form action="{{ route('admin.companies.verify', $company) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-2 py-1 rounded bg-green-100 text-green-800 hover:bg-green-200">
                                                Verifikasi
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.companies.unverify', $company) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                                Batalkan Verifikasi
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada perusahaan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                <div class="mt-6">
                    {{ $companies->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

