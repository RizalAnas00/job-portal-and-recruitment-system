@extends('admin.users.layout')

@section('content')

    <div class="mb-5">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Kelola Akun Pengguna</h1>
        
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                <div class="flex flex-wrap gap-4 items-end">
                    <!-- Search -->
                    <div class="flex-1 min-w-[250px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari
                        </label>
                        <input type="text" name="search" placeholder="Cari email, phone, company, industry..."
                            class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100
                                   focus:outline-none focus:ring-2 focus:ring-primary-500"
                            value="{{ request('search') }}">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Mencari di: Email, Phone, Job Seeker Name, Company Name, Industry
                        </p>
                    </div>

                    <!-- Role Filter -->
                    <div class="min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select name="role" 
                            class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100
                                   focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Semua Role</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Job Seeker</option>
                            <option value="company" {{ request('role') === 'company' ? 'selected' : '' }}>Company</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" 
                            class="w-full px-4 py-2 text-sm rounded border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100
                                   focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </a>
                </div>

                <!-- Active Filters Display -->
                @if(request('search') || request('role') || request('status'))
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Filter Aktif:</p>
                        <div class="flex flex-wrap gap-2">
                            @if(request('search'))
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('search'), ['page' => 1])) }}" 
                                       class="ml-1 hover:text-primary-600">×</a>
                                </span>
                            @endif
                            @if(request('role'))
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Role: {{ request('role') === 'user' ? 'Job Seeker' : 'Company' }}
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role'), ['page' => 1])) }}" 
                                       class="ml-1 hover:text-green-600">×</a>
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    Status: {{ request('status') === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('status'), ['page' => 1])) }}" 
                                       class="ml-1 hover:text-purple-600">×</a>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Pengguna</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $roleCounts['all'] }}</div>
                    </div>
                    <div class="p-3 bg-primary-100 dark:bg-primary-900 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 dark:text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Job Seeker</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $roleCounts['user'] }}</div>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Company</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $roleCounts['company'] }}</div>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
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

    <x-table :headers="['ID', 'Email', 'Nama', 'Role', 'Detail', 'Status', 'Actions']">
        @forelse ($users as $user)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-4">{{ $user->id }}</td>
                <td class="px-6 py-4">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    @if($user->hasRole('user') && $user->jobSeeker)
                        {{ $user->jobSeeker->first_name }} {{ $user->jobSeeker->last_name }}
                    @elseif($user->hasRole('company') && $user->company)
                        {{ $user->company->company_name }}
                    @else
                        {{ $user->name ?? '—' }}
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $user->hasRole('user') ? 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200' : '' }}
                        {{ $user->hasRole('company') ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                        {{ $user->hasRole('admin') ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                        {{ $user->role->display_name ?? '—' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    @if($user->hasRole('user') && $user->jobSeeker)
                        <span class="text-gray-600 dark:text-gray-400">Phone: {{ $user->jobSeeker->phone_number ?? '—' }}</span>
                    @elseif($user->hasRole('company') && $user->company)
                        <span class="text-gray-600 dark:text-gray-400">{{ $user->company->industry ?? '—' }}</span>
                    @else
                        —
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($user->is_active)
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
                        @if(!$user->hasRole('admin'))
                            <!-- Toggle Status Button -->
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="text-xs px-2 py-1 rounded
                                    {{ $user->is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>

                            <!-- Reset Password Button -->
                            <a href="{{ route('admin.users.reset-password', $user) }}"
                                class="text-xs px-2 py-1 rounded bg-primary-100 text-primary-800 hover:bg-primary-200">
                                Reset Password
                            </a>

                            <!-- Delete Button -->
                            <button type="button" 
                                onclick="openDeleteModal('{{ route('admin.users.destroy', $user) }}', '{{ $user->email }}')"
                                class="text-xs px-2 py-1 rounded bg-red-100 text-red-800 hover:bg-red-200">
                                Hapus
                            </button>
                        @else
                            <span class="text-xs text-gray-400">Admin</span>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    Tidak ada pengguna ditemukan.
                </td>
            </tr>
        @endforelse
    </x-table>

    <div class="mt-6">
        {{ $users->appends(request()->query())->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-sm">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Konfirmasi Penghapusan</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                Apakah Anda yakin ingin menghapus akun <strong id="userEmail"></strong>?
            </p>
            <p class="text-xs text-red-600 dark:text-red-400 mb-6">
                Tindakan ini tidak dapat dibatalkan. Semua data terkait akan dihapus permanen.
            </p>

            <div class="flex justify-end gap-3">
                <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 text-sm rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300">
                    Batal
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="px-4 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-700">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openDeleteModal(actionUrl, userEmail) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const emailElement = document.getElementById('userEmail');
        
        form.action = actionUrl;
        emailElement.textContent = userEmail;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }
</script>
@endsection

