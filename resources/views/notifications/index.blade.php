@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-gray-900 dark:text-gray-100 text-2xl font-bold">Notifikasi</h1>
        
        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline-block">
                @csrf
                @method('PUT')
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded">
                    Tandai Semua Sudah Dibaca
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        @forelse($notifications as $notification)
            <div class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="p-4 flex items-start gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0 mt-1">
                        @if(!$notification->is_read)
                            <div class="h-3 w-3 bg-blue-500 rounded-full"></div>
                        @else
                            <div class="h-3 w-3 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white {{ !$notification->is_read ? 'font-bold' : '' }}">
                            @if($notification->company)
                                <span class="text-blue-600 dark:text-blue-400">{{ $notification->company->company_name }}</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 {{ !$notification->is_read ? 'font-semibold' : '' }}">
                            {{ $notification->message }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex-shrink-0 flex gap-2">
                        @if($notification->link_url)
                            <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                    Lihat Detail
                                </button>
                            </form>
                        @elseif(!$notification->is_read)
                            <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 text-sm font-medium">
                                    Tandai Sudah Dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="text-lg">Tidak ada notifikasi</p>
                <p class="text-sm mt-1">Anda akan menerima notifikasi di sini ketika ada update terkait lamaran Anda</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

