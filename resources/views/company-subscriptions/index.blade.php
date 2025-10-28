@extends('company-subscriptions.layout')

@section('content')

    <div class="bg-white dark:bg-transparent shadow-md rounded-xl p-6 mb-6">
        <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-semibold mb-4 flex items-center gap-2">
            Your Active Subscription
        </h2>

        @if (session('success'))
            <div 
                id="success-alert"
                class="relative dark:bg-green-700/15 bg-green-600/20 text-green-700 border-green-600 text-center dark:text-green-500 border dark:border-green-300 rounded-lg px-4 py-3 mb-6 shadow-sm max-w-3xl mx-auto transition-opacity duration-500 opacity-100"
            >
                <button 
                    type="button" 
                    onclick="document.getElementById('success-alert').style.display='none'"
                    class="absolute top-3 right-3 text-green-600 hover:text-green-800 text-xl font-bold leading-none focus:outline-none"
                >
                    ×
                </button>

                <div>
                    <span class="font-semibold">Success</span>
                    <span class="text-sm">{{ session('success') }}</span>
                </div>
            </div>

            <script>
                setTimeout(() => {
                    const alertBox = document.getElementById('success-alert');
                    if (alertBox) {
                        alertBox.style.opacity = '0';
                        setTimeout(() => alertBox.style.display = 'none', 500);
                    }
                }, 10000); // 10 secs
            </script>
        @endif

        @if (session('info'))
            <div 
                id="info-alert"
                class="relative dark:bg-yellow-700/15 bg-yellow-600/20 text-yellow-700 border-yellow-600 text-center dark:text-yellow-500 border dark:border-yellow-300 rounded-lg px-4 py-3 mb-6 shadow-sm max-w-3xl mx-auto transition-opacity duration-500 opacity-100"
            >
                <button 
                    type="button" 
                    onclick="document.getElementById('info-alert').style.display='none'"
                    class="absolute top-3 right-3 text-yellow-600 hover:text-yellow-800 text-xl font-bold leading-none focus:outline-none"
                >
                    ×
                </button>

                <div>
                    <span class="font-semibold">info</span>
                    <span class="text-sm">{{ session('info') }}</span>
                </div>
            </div>

            <script>
                setTimeout(() => {
                    const alertBox = document.getElementById('info-alert');
                    if (alertBox) {
                        alertBox.style.opacity = '0';
                        setTimeout(() => alertBox.style.display = 'none', 500);
                    }
                }, 10000); // 10 secs
            </script>
        @endif

        @if ($activeSubscription)
            <details class="group">
                <summary class="list-none cursor-pointer bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-700 rounded-xl p-5 transition-all hover:bg-primary-100 dark:hover:bg-primary-900/40">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div>
                            <p class="text-2xl font-bold text-primary-700 dark:text-primary-300 mb-1">
                                {{ $activeSubscription->plan->plan_name }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $activeSubscription->start_date->format('d M Y') }} – {{ $activeSubscription->end_date->format('d M Y') }}
                                @if($activeSubscription->status === 'canceled')
                                    <span class="mx-2"> | </span>
                                    <span class="mr-2 italic text-gray-500 dark:text-gray-500">canceled but remain active until {{ $activeSubscription->end_date->copy()->setTime(23, 59)->translatedFormat('d F Y H:i') }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center gap-4 mt-3 md:mt-0">
                            @if ($activeSubscription->status === 'canceled')
                                <span class="px-4 py-1 text-sm font-medium dark:bg-red-600/25 bg-red-300/50 text-red-700 dark:text-red-300 border-red-600 dark:border-red-400 border rounded-full">
                                    Canceled
                                </span>
                            @else 
                                <span class="px-4 py-1 text-sm font-medium dark:bg-green-600/25 bg-green-300/50 text-green-700 dark:text-green-300 border-green-600 dark:border-green-400 border rounded-full">
                                    Active
                                </span>
                            @endif
                            <svg class="w-5 h-5 text-primary-700 dark:text-primary-300 transition-transform duration-200 group-open:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </summary>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mt-6">
                    
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Subscription Details</h3>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="md:col-span-1">
                            <dt class="font-medium text-gray-600 dark:text-gray-400">Plan</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $activeSubscription->plan->plan_name }}</dd>
                        </div>
                        <div class="md:col-span-1">
                            <dt class="font-medium text-gray-600 dark:text-gray-400">Price</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100">
                                Rp. {{ number_format($activeSubscription->plan->price ?? 0, 2) }} / {{ $activeSubscription->plan->duration_days . ' days' ?? 'month' }}
                            </dd>
                        </div>
                        <div class="md:col-span-1">
                            <dt class="font-medium text-gray-600 dark:text-gray-400">Payment Method</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $activeSubscription->payment_method ?? 'N/A' }}</dd>
                        </div>
                        <div class="md:col-span-1">
                            <dt class="font-medium text-gray-600 dark:text-gray-400">Payment Date</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $activeSubscription->start_date->format('d F Y H:i:s') }}</dd>
                        </div>
                        <div class="md:col-span-1">
                            <dt class="font-medium text-gray-600 dark:text-gray-400">Next Billing Date</dt>
                            <dd class="font-semibold text-gray-900 dark:text-gray-100">{{ $activeSubscription->end_date->copy()->setTime(23, 59)->translatedFormat('d F Y H:i') }}</dd>
                        </div>
                        <div class="md:col-span-1">
                            <dt class="font-medium text-gray-600 dark:text-gray-400">Job Post Limit</dt>
                            <dd class="font-semibold text-primary-700 dark:text-primary-200">{{ $activeSubscription->plan->job_post_limit }}</dd>
                        </div>
                        <div class="md:col-span-1">
                            <dt class="font-medium text-gray-600 dark:text-gray-400">Verified Badge</dt>
                            <dd class="font-bold text-gray-900 dark:text-gray-100">
                                {{ $activeSubscription->plan->allow_verified_badge ? 'Yes' : 'No' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="border-t border-gray-200 dark:border-gray-600 mt-6 pt-5">
                        <h4 class="text-md font-semibold dark:font-bold text-red-600 dark:text-red-500">Cancel Subscription</h4>
                        @if($activeSubscription->status === 'active')
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 mb-4">
                                If you cancel, your subscription will remain active until {{ $activeSubscription->end_date->format('d F Y') }}. 
                                You will not be charged again.
                            </p>
                            <div class="flex justify-end">
                                <form action="{{ route('company.subscriptions.cancel', $subscription = $activeSubscription) }}" method="POST">
                                    @csrf
                                    <button class="px-5 py-2.5 bg-red-600 dark:bg-red-700/30 dark:border dark:border-red-500 dark:text-red-100 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                                        Cancel Subscription
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                You have already canceled this subscription. It will remain active with full benefit until 
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $activeSubscription->end_date->format('d F Y') }}.</span>
                            </p>
                        @endif
                    </div>

                </div>
            </details>
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-xl p-4">
                <p class="text-yellow-800 dark:text-yellow-300 font-semibold">
                    You do not have an active subscription. Please choose a plan to get started.
                </p>
            </div>
        @endif
    </div>

    {{-- ====================== Available Plans ====================== --}}
    <div class="bg-white dark:bg-transparent shadow-md rounded-xl p-6">
        <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-semibold mb-5">
            Available Subscription Plans
        </h2>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($plans as $plan)
                @php
                    $isActive = $activeSubscription && $activeSubscription->plan->id === $plan->id;
                    $isEnterprise = strtolower($plan->plan_name) === 'enterprise';
                @endphp

                <div class="relative rounded-xl border-2 transition-all duration-300 shadow-sm hover:shadow-lg transform hover:-translate-y-1
                    {{ $isEnterprise 
                        ? 'border-teal-400 bg-gradient-to-b from-teal-50 to-white dark:from-teal-900/20 dark:to-gray-900' 
                        : ($isActive 
                            ? 'border-primary-400 bg-primary-50 dark:bg-primary-900/20' 
                            : 'border-gray-200 dark:border-gray-700 bg-transparent dark:bg-transparent') }}">

                    @if ($isEnterprise)
                        <span class="absolute top-3 right-3 bg-teal-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                        Best Value
                        </span>
                    @elseif ($isActive)
                        <span class="absolute top-3 right-3 bg-primary-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                            Active Plan
                        </span>
                    @endif

                    <div class="p-6 md:p-7 flex flex-col justify-between h-full">
                        {{-- Plan name --}}
                        <h3 class="text-2xl font-bold mb-3 
                            {{ $isEnterprise ? 'text-teal-700 dark:text-teal-300' : ($isActive ? 'text-primary-700 dark:text-primary-300' : 'text-gray-800 dark:text-gray-100') }}">
                            {{ $plan->plan_name }}
                        </h3>

                        {{-- Price & Duration --}}
                        <div class="flex items-center justify-between mb-6 pb-24">
                            <div class="text-left">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Price (Rp)</p>
                                @if($plan->price > 0)
                                    @php
                                        $price = $plan->price;
                                        $formatted = number_format($price, 2, '.', '');
                                        $len = strlen($formatted);
                                        $main = substr($formatted, 0, -6);
                                        $fraction = substr($formatted, -6);
                                    @endphp

                                    <div class="flex items-baseline space-x-1">
                                        <span class="text-4xl font-bold {{ $isEnterprise ? 'text-teal-600 dark:text-teal-300' : 'text-primary-600 dark:text-primary-300' }}">
                                            {{ $main }}
                                        </span>
                                        <span class="text-xs text-gray-500 relative top-[2px]">
                                            .{{ $fraction }}
                                        </span>
                                    </div>
                                @else
                                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-300">
                                        Free
                                    </p>
                                @endif
                            </div>

                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Duration</p>
                                <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $plan->duration_days }} Days
                                </p>
                            </div>
                        </div>

                        {{-- Benefit Section --}}
                        <div class="mt-6 mb-5">
                            {{-- <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-6">
                                What You Get :
                            </p> --}}
                            <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p>
                                    <strong>Job Post Limit:</strong>
                                    @if ($plan->job_post_limit == 999)
                                        <span class="font-bold text-teal-600 dark:text-teal-400">Unlimited</span>
                                    @else
                                        {{ $plan->job_post_limit }}
                                    @endif
                                </p>
                                <p><strong>Verified Badge:</strong> {{ $plan->allow_verified_badge ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>

                        @if (!$isActive)
                            <form action="{{ route('company.subscriptions.confirm', $plan) }}" method="GET">
                                @csrf
                                <button
                                    class="w-full py-2 rounded-lg font-semibold text-white transition
                                        {{ $isEnterprise
                                            ? 'bg-teal-500 hover:bg-teal-600'
                                            : 'bg-primary-500 hover:bg-primary-600' }}">
                                    Choose Plan
                                </button>
                            </form>
                        @else
                            <button disabled
                                class="w-full py-2 bg-primary-300 text-white rounded-lg opacity-70 cursor-not-allowed font-semibold">
                                Current Plan
                            </button>
                            {{-- <form action="#" method="GET">
                                @csrf
                                <button
                                    class="w-full py-2 rounded-lg font-semibold text-white transition
                                        {{ $isEnterprise
                                            ? 'bg-teal-500 hover:bg-teal-600'
                                            : 'bg-primary-500 hover:bg-primary-600' }}">
                                    Cancel Subscription
                                </button>
                            </form> --}}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection