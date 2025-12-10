@extends('payment-history.layout')

@section('content')
    {{-- <div class="max-w-5xl mx-auto p-8 bg-white dark:bg-gray-900 shadow-xl rounded-2xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Payment History
        </h1>

        @if ($payments->isEmpty())
            <div class="text-center text-gray-600 dark:text-gray-400 py-12">
                <p class="text-lg">No payment history yet.</p>
                <a href="{{ route('company.subscriptions.index') }}"
                   class="inline-block mt-4 px-5 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold transition-all duration-200">
                    Browse Subscription Plans
                </a>
            </div>
        @else
            <div class="space-y-5">
                @foreach ($payments as $payment)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-md bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 transition hover:shadow-lg hover:scale-[1.01]">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ $payment->companySubscription->plan->plan_name ?? 'Unknown Plan' }}
                            </h2>

                            @php
                                $statusColors = [
                                    'paid' => 'bg-green-100 text-green-700 border-green-300',
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                    'failed' => 'bg-red-100 text-red-700 border-red-300',
                                ];
                                $color = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-700 border-gray-300';
                            @endphp

                            <span class="px-3 py-1 rounded-full border text-xs font-semibold uppercase {{ $color }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700 dark:text-gray-300">
                            <p><strong>Amount:</strong> Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                            <p><strong>Transaction ID:</strong> {{ $payment->id ?? 'N/A' }}</p>
                            <p><strong>VA Number:</strong> {{ $payment->va_number ?? 'N/A' }}</p>
                            <p><strong>Payment Method:</strong> {{ $payment->payment_method ?? 'N/A' }}</p>
                            <p><strong>Payment Date:</strong> {{ $payment->payment_date->format('d F Y, H:i') }}</p>
                            <p><strong>Expires At:</strong> {{ $payment->expires_at ? $payment->expires_at->format('d F Y, H:i') : '-' }}</p>
                        </div>

                        @if ($payment->payment_url)
                            <div class="mt-4">
                                <a href="{{ $payment->payment_url }}" target="_blank"
                                   class="inline-block bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                    View Payment Page
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div> --}}
    @if (session('success-cancel'))
        <div 
            id="success-alert"
            class="relative dark:bg-green-700/15 bg-green-600/20 text-green-700 border-green-600 text-center dark:text-green-500 border dark:border-green-300 rounded-lg px-4 py-3 mb-6 shadow-sm max-w-3xl mx-auto transition-opacity duration-500 opacity-100"
        >
            <button 
                type="button" 
                onclick="document.getElementById('success-alert').style.display='none'"
                class="absolute top-2 right-3 text-green-600 hover:text-green-800 text-xl font-bold leading-none focus:outline-none"
            >
                ×
            </button>

            <div>
                <span class="font-semibold">Success</span>
                <span class="text-sm">{{ session('success-cancel') }}</span>
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

    <x-table :headers="['VA Number', 'Plan Name', 'Amount', 'Status', 'Payment Date', 'Actions']">
        @if (isset($payments) && $payments->isEmpty() || $payments === null)
            <tr>
                <td colspan="6" class="italic px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                    No payment history yet.
                </td>
            </tr>
        @else
            @foreach ($payments as $payment)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                    <td class="px-6 py-4">{{ $payment->va_number ?? 'N/A' }}</td>
                    <td class="px-6 py-4 font-bold">{{ $payment->companySubscription->plan->plan_name ?? 'Unknown Plan' }}</td>
                    <td class="px-6 py-4">Rp {{ number_format($payment->amount, 0, ',', '.') ?? 'N/A' }}</td>
                    <td class="px-6 py-4">@if ($payment->status == 'success')
                        <span class="px-3 py-1 rounded-full border bg-green-600/25 text-green-700 dark:text-green-300 border-green-600 dark:border-green-400 text-xs font-semibold uppercase">
                            Paid
                        </span>
                    @elseif ($payment->status == 'pending')
                        <span class="px-3 py-1 rounded-full border bg-yellow-600/25 text-yellow-700 dark:text-yellow-500 border-yellow-500 dark:border-yellow-300 text-xs font-semibold uppercase">
                            Pending
                        </span>
                    @elseif ($payment->status == 'failed')
                        <span class="px-3 py-1 rounded-full border bg-red-600/25 text-red-500 border-red-300 text-xs font-semibold uppercase">
                            Failed
                        </span>
                    @endif</td>
                    <td class="px-6 py-4">
                        {{ $payment->payment_date ? $payment->payment_date->format('d F Y, H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                    <form action="{{ route('company.payment.waiting', $payment) }}" method="GET">
                        <button type="submit" class="dark:bg-primary-600/25 bg:transparent hover:bg-primary-500 text-primary-700 hover:text-white dark:text-white border-primary-600 border px-4 py-2 rounded-lg text-sm font-medium transition">
                            Details
                        </button>
                    </form>
                    </td>
                </tr>
            @endforeach
        @endif
        </x-table>
        
        @if (isset($payments) && !$payments->isEmpty() && $payments !== null)
            <div class="mt-6">
                {{ $payments->onEachSide(5)->links() }}
            </div>
        @endif
@endsection