<aside 
    x-data="{ 
        open: true, 
        activeAccordion: @js(request()->routeIs('company.job-postings.*') ? 'job' : ''), 
    }"
    x-effect="
        if (!open) activeAccordion = '';  // otomatis tutup semua accordion saat sidebar ditutup
    "
    :class="open ? 'w-64 rounded-r-xl' : 'w-16'" 
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
            {{ config('app.name') }}
        </span>

        <button 
            @click="open = !open" 
            class="p-2 rounded hover:bg-[#0f14aa]/30 transition">
            <span class="h-6 w-6 transition-transform duration-300 text-xl">☰</span>
        </button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-2">

        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition {{ request()->routeIs('dashboard') ? 'bg-[#0f14aa]/30' : '' }}">
            @svg('carbon-dashboard-reference', 'h-6 w-6 flex-shrink-0 text-xl')
           <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                {{ __('Dashboard') }}
            </span>
        </a>

        @if (Auth::user()->hasRole('admin'))
        <a href="{{ route('admin.role.index') }}" class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition">
            @svg('carbon-user-role', 'h-6 w-6 flex-shrink-0 text-xl')
            <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">Role</span>
        </a>
        @endif

        <div class="w-full">
            <button 
                @click="
                    if (!open) {
                        open = true;
                        setTimeout(() => activeAccordion = 'job', 250);
                    } else {
                        activeAccordion === 'job' ? activeAccordion = '' : activeAccordion = 'job';
                    }
                "
                class="flex items-center justify-between w-full p-3 rounded-md hover:bg-[#0f14aa]/30 transition"
                :class="activeAccordion === 'job' ? 'bg-[#0f14aa]/30' : ''">
                
                <div class="flex items-center gap-2">
                    @svg('fluentui-briefcase-28', 'h-6 w-6 flex-shrink-0 text-xl')
                    <span class="truncate text-left" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                        {{ __('Manage Job') }}
                    </span>
                </div>

                <svg 
                    x-show="open"
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke-width="2" 
                    stroke="currentColor"
                    class="h-5 w-5 flex-shrink-0 text-xl transition-transform duration-200"
                    :class="activeAccordion === 'job' ? 'rotate-180' : ''">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Accordion Content -->
            <div 
                x-show="activeAccordion === 'job' && open"
                x-collapse
                class="mt-1 space-y-1 ml-8"
            >
                @if (Auth::user()->hasRole('company'))
                    <a href="{{ route('company.job-postings.create') }}"
                        class="block px-2 py-1.5 rounded-md text-sm hover:bg-[#0f14aa]/20 transition {{ request()->routeIs('company.job-postings.index') ? 'bg-[#0f14aa]/20' : '' }}">
                        {{ __('Create Job Offer') }}
                    </a>
                @else
                    <a href="{{ route('job-postings.index') }}"
                        class="block px-2 py-1.5 rounded-md text-sm hover:bg-[#0f14aa]/20 transition {{ request()->routeIs('company.job-postings.index') ? 'bg-[#0f14aa]/20' : '' }}">
                        {{ __('Find Job Offer') }}
                    </a>
                @endif
                
                @if (Auth::user()->hasRole('user'))
                    <a href="{{ route('company.job-postings.index') }}"
                        class="block px-2 py-1.5 rounded-md text-sm hover:bg-[#0f14aa]/20 transition {{ request()->routeIs('company.job-postings.create') ? 'bg-[#0f14aa]/20' : '' }}">
                        {{ __('My Applied Jobs') }}
                    </a>
                @else
                    <a href="{{ route('company.job-postings.index') }}"
                        class="block px-2 py-1.5 rounded-md text-sm hover:bg-[#0f14aa]/20 transition {{ request()->routeIs('company.job-postings.create') ? 'bg-[#0f14aa]/20' : '' }}">
                        {{ __('My Job Offers') }}
                    </a>
                @endif
                </a>
            </div>
        </div>

        @if (Auth::user()->hasRole('user'))
            <a href="{{ route('user.jobs.index') }}"
                class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition {{ request()->routeIs('user.jobs.*') ? 'bg-[#0f14aa]/30' : '' }}">
                @svg('ionicon-briefcase-outline', 'h-6 w-6 flex-shrink-0 text-xl')
                <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                    Job Recommendations
                </span>
            </a>
            <a href="{{ route('user.skills.index') }}"
                class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition {{ request()->routeIs('user.skills.*') ? 'bg-[#0f14aa]/30' : '' }}">
                @svg('ionicon-ribbon-outline', 'h-6 w-6 flex-shrink-0 text-xl')
                <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                    My Skills
                </span>
            </a>
        @endif

        @if (Auth::user()->hasRole('company'))
            <a href="{{ route('company.subscriptions.index') }}"
                class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition {{ request()->routeIs('subscription_plan.*') ? 'bg-[#0f14aa]/30' : '' }}">
                @svg('ionicon-book', 'h-6 w-6 flex-shrink-0 text-xl')
                <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                    {{ __('Subscription Plans') }}
                </span>
            </a>

            <a href="{{ route('company.payment.index') }}"
                class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition {{ request()->routeIs('company.payment.*') ? 'bg-[#0f14aa]/30' : '' }}">
                @svg('fluentui-payment-28', 'h-6 w-6 flex-shrink-0 text-xl')
                <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                    {{ __('Payment History') }}
                </span>
            </a>
        @endif

        @if (Auth::user()->hasAnyRole(['company', 'user']))
            <a href="{{ route('interviews.index') }}"
                class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition {{ request()->routeIs('interviews.*') ? 'bg-[#0f14aa]/30' : '' }}">
                @svg('ionicon-calendar', 'h-6 w-6 flex-shrink-0 text-xl')
                <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                    {{ __('Interviews') }}
                </span>
            </a>

            <a href="{{ route('notifications.index') }}"
                class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition {{ request()->routeIs('notifications.*') ? 'bg-[#0f14aa]/30' : '' }} relative">
                @svg('ionicon-notifications', 'h-6 w-6 flex-shrink-0 text-xl')
                <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                    {{ __('Notifications') }}
                </span>
                {{-- Unread notification badge --}}
                <span id="unread-notification-badge" 
                      class="absolute top-2 left-8 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold"
                      style="display: none;">
                    0
                </span>
            </a>
        @endif

        <a href="#" class="flex items-center gap-3 p-3 rounded-md hover:bg-[#0f14aa]/30 transition">
            @svg('ionicon-settings-sharp', 'h-6 w-6 flex-shrink-0 text-xl')
            <span class="truncate" :class="open ? 'w-40' : 'w-0 overflow-hidden'">
                {{ __('Settings') }}
            </span>
        </a>
    </nav>
</aside>
