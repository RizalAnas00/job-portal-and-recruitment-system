@extends('skill.layout')

@section('content')

    <div class="flex items-center justify-between mb-5">
        <!-- Add New Skill Button -->
        <a href="{{ route('admin.skill.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow hover:bg-green-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Skill
        </a>

        <!-- Search -->
        <form method="GET" action="{{ route('admin.skill.index') }}" class="relative w-72">
            <input type="text" name="search" placeholder="Cari skill..."
                class="w-full pl-4 pr-10 py-2 text-sm rounded-full border border-gray-200 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 shadow-inner
                       dark:border-gray-700 transition" 
                value="{{ request('search') }}">
            <button type="submit"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                </svg>
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <x-table :headers="['ID', 'Nama Skill', 'Actions']">
        @forelse ($skills as $skill)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-6 py-4">{{ $skill->id }}</td>
                <td class="px-6 py-4 font-bold">{{ $skill->skill_name }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.skill.edit', $skill) }}" class="text-primary-600 dark:text-primary-500 hover:underline">Edit</a> |
                    
                    <!-- Delete Button -->
                    <button type="button" 
                        onclick="openDeleteModal('{{ route('admin.skill.destroy', $skill) }}', '{{ $skill->skill_name }}')"
                        class="font-semibold text-red-600 dark:text-red-500 hover:underline">
                        Hapus
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    Tidak ada skill ditemukan.
                </td>
            </tr>
        @endforelse
    </x-table>

    <div class="mt-6">
        {{ $skills->onEachSide(5)->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-sm">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Konfirmasi Penghapusan</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                Apakah Anda yakin ingin menghapus skill <strong id="skillName"></strong>?
            </p>
            <p class="text-xs text-red-600 dark:text-red-400 mb-6">Tindakan ini tidak dapat dibatalkan.</p>

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
    function openDeleteModal(actionUrl, skillName) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const nameElement = document.getElementById('skillName');
        
        form.action = actionUrl;
        nameElement.textContent = skillName;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }
</script>
@endsection

