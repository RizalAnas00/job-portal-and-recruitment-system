@extends('company-subscriptions.layout')

@section('content')
    <div class="bg-white dark:bg-transparent shadow-md rounded-xl p-6 mb-6">
        <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-semibold mb-4 flex items-center gap-2">
            Your Active Subscription Details
        </h2>

        @if ($activeSubscription)
            <div class="bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-700 rounded-xl p-5 transition-all">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <p class="text-2xl font-bold text-primary-700 dark:text-primary-300 mb-1">
                            {{ $activeSubscription->plan->plan_name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $activeSubscription->start_date->format('d M Y') }} – {{ $activeSubscription->end_date->format('d M Y') }}
                        </p>
                    </div>
                    <span class="mt-3 md:mt-0 px-4 py-1 text-sm font-medium dark:bg-green-600/25 bg-green-300/50 text-green-700 dark:text-green-300 border-green-600 dark:border-green-400 border rounded-full">
                        Active
                    </span>
                </div>
            </div>
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
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection