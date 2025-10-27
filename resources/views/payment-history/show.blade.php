@extends('payment-history.layout')

@section('content')
    <div class="max-w-3xl mx-auto px-8 pb-8 pt-3 bg-white dark:bg-gray-900 shadow-xl rounded-2xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Payment Details
        </h1>

        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-md bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">
            @php
                $statusColors = [
                    'success' => 'dark:bg-green-600/25 bg-green-300/50 text-green-700 dark:text-green-300 border-green-600 dark:border-green-400',
                    'pending' => 'dark:bg-yellow-600/25 bg-yellow-300/50 text-yellow-600 dark:text-yellow-500 border-yellow-500 dark:border-yellow-300',
                    'failed' => 'dark:bg-red-600/25 bg-red-300/50 text-red-700 dark:text-red-300 border-red-600 dark:border-red-400',
                ];
                $color = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-700 border-gray-300';
            @endphp

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Transaction #{{ $payment->id }}
                </h2>
                <span class="px-3 py-1 rounded-full border text-xs font-semibold uppercase {{ $color }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>

            {{-- Detail Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300 text-sm">
                <div>
                    <p class="text-gray-500 text-xs uppercase">Plan</p>
                    <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $payment->companySubscription->plan->plan_name }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs uppercase">Amount</p>
                    <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs uppercase">Payment Method</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $payment->payment_method ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs uppercase">VA Number</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $payment->va_number ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs uppercase">Payment Date</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $payment->payment_date ? $payment->payment_date->format('d F Y, H:i') : '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs uppercase">Expires At</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $payment->expires_at ? $payment->expires_at->format('d F Y, H:i') : '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs uppercase">Created At</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $payment->created_at->format('d F Y, H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs uppercase">Company</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $payment->companySubscription->company->company_name ?? 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

            {{-- Payment Button --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('company.payment.index') }}"
                class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    ← Back to History
                </a>

                @if ($payment->payment_url && $payment->status === 'pending')
                    <div class="flex items-center gap-3">
                        {{-- Cancel Payment --}}
                        <form action="{{ route('company.payment.cancel', $payment) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to cancel this payment?');">
                            @csrf
                            <button type="submit"
                                class="dark:bg-red-600/20 bg:transparent hover:bg-red-800 text-red-700 hover:text-white dark:text-white px-5 py-2.5 border border-red-600 rounded-lg font-medium transition duration-600 ease-in-out">
                                Cancel Payment
                            </button>
                        </form>

                        {{-- Go to Payment --}}
                        <a href="{{ $payment->payment_url }}" target="_blank"
                            class="dark:bg-primary-600/25 bg:transparent hover:bg-primary-600 text-primary-700 hover:text-white dark:text-white border-primary-600 px-5 py-2.5 rounded-lg border font-medium 
                                transition-colors duration-600 ease-in-out">
                            Go to Payment Page
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @if ($payment->status === 'pending')
        <script>
            setInterval(function() {
                fetch('{{ route("company.payment.check-status", $payment) }}')
                    .then(response => {
                        if (!response.ok) return;
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = '{{ route("company.payment.waiting", $payment) }}';
                        }
                    })
                    .catch(() => {});
            }, 10000);
        </script>
    @endif
@endsection
