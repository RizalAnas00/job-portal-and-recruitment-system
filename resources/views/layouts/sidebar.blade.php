<aside 
    x-data="{ open: true }" 
    :class="open ? 'w-64 rounded-r-xl' : 'w-20'" 
    class="bg-gradient-to-b from-[#3f36f7] to-[#171ee0] text-white h-screen transition-all duration-300 ease-in-out shadow-lg flex flex-col sticky top-0">

    <!-- Logo & Toggle -->
    <div class="flex items-center justify-between p-4">
        <span x-show="open" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 -translate-x-2" 
            x-transition:enter-end="opacity-100 translate-x-0" 
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100 translate-x-0" 
            x-transition:leave-end="opacity-0 -translate-x-2" 
            class="font-bold text-lg"> 
            
            MyApp 
        
        </span>

        <button 
            @click="open = !open" 
            class="p-2 rounded hover:bg-[#0f14aa]/30 transition">
            <x-heroicons-chevron-up class="h-6 w-6 transition-transform duration-300" 
                 :class="open ? 'rotate-0' : 'rotate-180'" />
        </button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-2">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition {{ request()->routeIs('dashboard') ? 'bg-[#0f14aa]/30' : '' }}">
            <x-gmdi-space-dashboard class="h-6 w-6 flex-shrink-0"/>
            <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                Dashboard
            </span>
        </a>

        @if (Auth::user()->hasRole('admin'))
        <a href="#" class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition">
            <x-eos-role-binding class="h-6 w-6 flex-shrink-0"/>
            <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                Role
            </span>
        </a>
        @endif

        <a href="#" class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition">
            <x-iconsax-bol-setting-2 class="h-6 w-6 flex-shrink-0"/>
            <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                Settings
            </span>
        </a>
    </nav>
</aside>
