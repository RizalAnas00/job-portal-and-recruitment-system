@extends('company-subscriptions.layout')

@section('content')
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-900 shadow-2xl rounded-2xl p-10 text-center transition-all duration-300">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                Confirm Your Subscription
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-base">
                You’re about to activate the 
                <span class="font-bold text-primary-600 dark:text-primary-400">{{ $plan->plan_name }}</span> plan.
            </p>
        </div>

        @if (isset($err) || session('error'))
            <div class="bg-yellow-600/20 text-center text-yellow-500 border border-yellow-300 rounded-lg p-4 mb-6 shadow-sm">

                <div>
                    <p class="font-semibold mx-auto">Warning</p>
                    <p class="text-sm mx-auto">{{ $err ?? session('error') }}</p>
                    <p class="text-sm text-yellow-500 mt-4 inline-block">
                        Check your pending payment 
                        <a href="#" class="underline font-semibold hover:text-yellow-600">here.</a>
                    </p>

                </div>
            </div>
        @endif

        <div class="relative border-2 border-primary-200 dark:border-primary-800 bg-gradient-to-br from-primary-100/30 to-transparent dark:from-primary-800/20 dark:to-transparent rounded-2xl p-8 mb-8 shadow-md transition-transform hover:scale-[1.01]">
            <div class="absolute -top-3 right-6">
                <span class="px-3 py-1 text-xs font-semibold bg-primary-600 text-white rounded-full shadow-sm uppercase tracking-wide">
                    Your Selection
                </span>
            </div>

            <div class="flex flex-col gap-4 text-left">
                <div class="flex justify-between items-center">
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $plan->plan_name }}</h2>
                    @if ($plan->price > 0)
                        <div class="text-right">
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Price</p>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($plan->price, 0, ',', '.') }}
                            </p>
                        </div>
                    @else
                        <div class="text-right">
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Price</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">Free</p>
                        </div>
                    @endif
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

                <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
                    <p><strong>Duration:</strong> {{ $plan->duration_days }} days</p>
                    <p>
                        <strong>Job Post Limit:</strong>
                        @if ($plan->job_post_limit == 999)
                            <span class="font-bold text-primary-600 dark:text-primary-400">Unlimited</span>
                        @else
                            {{ $plan->job_post_limit }}
                        @endif
                    </p>
                    <p><strong>Verified Badge:</strong> {{ $plan->allow_verified_badge ? 'Yes' : 'No' }}</p>
                    <p><strong>Status:</strong> <span class="text-yellow-600 font-medium">{{ $confirmStatus ?? 'Pending'}}</span></p>
                </div>
            </div>
        </div>

        <div class="mb-8">
            @if ($plan->price > 0)
                <p class="text-gray-600 dark:text-gray-400 text-sm md:text-base">
                    Please confirm your payment to activate this plan. You’ll be redirected to the payment page.
                </p>
            @else
                <p class="text-gray-600 dark:text-gray-400 text-sm md:text-base">
                    This plan is free. Click below to activate it instantly and start using your benefits.
                </p>
            @endif
        </div>

        <div class="flex justify-center gap-4">
            <a href="{{ route('company.subscriptions.index') }}"
               class="px-6 py-3 rounded-lg font-semibold border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 shadow-sm hover:shadow-md">
                ← Back to Plans
            </a>

            @if ($plan->price > 0)
                <form action="{{ route('company.payment.process', $plan) }}" method="POST">
                    @csrf
                    <button 
                        type="submit"
                        class="px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg focus:ring-2 focus:ring-primary-300
                            bg-primary-600 hover:bg-primary-700 active:scale-[0.98] text-white
                            @if (isset($err) || session('error') ) opacity-50 cursor-not-allowed @endif"
                        @disabled(isset($err) || session('error'))
                    >
                        Proceed
                    </button>
                </form>
            @else
                <form action="{{ route('company.payment.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan }}">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 active:scale-[0.98] text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg focus:ring-2 focus:ring-green-300">
                        Activate Free Plan
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
