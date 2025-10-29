<?php

namespace App\Http\Controllers;

use App\Actions\CreateCompanySubscription;
use App\Actions\CreatePaymentSubscription;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\PaymentTransaction;
use App\Models\SubscriptionPlan;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentTransactionController extends Controller
{
    private $createCompanySubscription;
    private $createPaymentSubscription;

    public function __construct(
        CreateCompanySubscription $createCompanySubscription,
        CreatePaymentSubscription $createPaymentSubscription    
    )
    {
        $this->createCompanySubscription = $createCompanySubscription;
        $this->createPaymentSubscription = $createPaymentSubscription;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $payments = null;

        if($user->company) {
            $payments = PaymentTransaction::whereHas('companySubscription', function ($query) use ($user) {
                    $query->where('id_company', $user->company->id);
                })
                ->with('companySubscription.plan')
                ->latest()
                ->paginate(5);
        }

        return view('payment-history.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function processPayment(Request $request, SubscriptionPlan $subscription)
    {
        // Log::info('Processing payment for subscription: ' . $subscription);
        // Log::info('Request data: ' . json_encode($request->all()));

        $expiredHours = (int) config('services.payment.expired_hours', 24);

        // Create Company Subscription
        /** @var \App\Models\User */
        $user = Auth::user();
        $role = $user->getRoleName();

        // dd([
        //     'User' => $user->toArray(),
        //     'Role' => $role,
        //     'Company' => $user->company ? $user->company->toArray() : null,
        //     'Subscription' => $subscription->toArray(),
        // ]);
        
        $company = $user->company;

        // Check if company already has an active subscription
        // $activeSubscription = CompanySubscription::where('id_company', $company->id)
        //     ->where('status', 'active')
        //     ->where('end_date', '>', now())
        //     ->first();

        // if ($activeSubscription) {
        //     return redirect()->route('company.subscriptions.index')
        //         ->with('error', 'You already have an active subscription. Please wait until it expires or cancel it first.');
        // }

        $companySubscription = $this->createCompanySubscription->__invoke($company, $subscription);

        // Check if this is a free plan
        if ($subscription->price == 0) {
            // Free plan - activate immediately without payment
            $companySubscription->update([
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addDays($subscription->duration_days),
            ]);

            return redirect()->route('company.subscriptions.index')
                ->with('success', 'Free plan activated successfully!');
        }

        $paymentTransaction = $this->createPaymentSubscription->__invoke($companySubscription, [
            'amount' => $subscription->price,
            'payment_method' => $request->input('payment_method'),
            'payment_url' => config('services.payment.payment_url', 'https://payment.example.com/pay'),
            'expires_at' => $expiredHours,
        ]);

        // Log::info('Created Payment Transaction: ' . $paymentTransaction);

        // Create Virtual Account via Payment Gateway API
        try {
            $response = Http::withHeaders([
                'X-API-Key' => config('services.payment.api_key'),
                'Accept' => 'application/json',
            ])->post(config('services.payment.base_url') . '/virtual-account/create', [
                'external_id' => $paymentTransaction->id,
                'amount' => $paymentTransaction->amount,
                'customer_name' => $role === 'company' ? $user->company->company_name : 'admin do testing',
                'customer_email' => $user->email,
                'customer_phone' => $role === 'company' ? $user->company->phone_number : '081234567890',
                'description' => 'Pembayaran ' . $subscription->name,
                'expired_duration' => $expiredHours,
                // This doesnt do shit tbh, since we manually handle the redirect response
                'callback_url' => route('company.payment.waiting', $paymentTransaction), 
                'metadata' => [
                    'subscription_id' => $companySubscription->id,
                    'user_id' => $user->id,
                ],
            ]);

            // dd($response->json());

            if ($response->successful()) {
                $data = $response->json();

                $paymentTransaction->update([
                    'va_number' => $data['data']['va_number'],
                    'payment_url' => $data['data']['payment_url'],
                ]);

                return redirect()->route('company.payment.waiting', $paymentTransaction);
            } else {
                $paymentTransaction->update(['payment_status' => 'failed']);
                return redirect()->route('customer.products.index')
                    ->with('error', 'Gagal membuat pembayaran. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $paymentTransaction->update(['payment_status' => 'failed']);
            // dd($e);
            // Log::error('Error creating virtual account: ' . $e->getMessage());
            return redirect()->route('company.payment.failure')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function waitingPayment(PaymentTransaction $payment)
    {
        return view('payment-history.show', compact('payment'));
    }

    // TODO : Implement cancel payment for db and payment gateway
    public function cancelPayment(PaymentTransaction $payment)
    {
        try {
            $url = config('services.payment.base_url') . "/virtual-account/{$payment->va_number}/cancel";

            $response = Http::withHeaders([
                'X-API-Key' => config('services.payment.api_key'),
                'Accept' => 'application/json',
            ])->post($url, [
                'callback_url' => route('company.payment.index'),
            ]);

            if($response->failed()) {
                return redirect()->route('company.payment.waiting', $payment)
                    ->with('error', 'Gagal membatalkan pembayaran di gateway. Silakan coba lagi.');
            }

            $vaNumber = $payment->va_number;
            $payment->companySubscription->forceDelete();

            return redirect()->route('company.payment.index')
                ->with('success-cancel', 'Pembayaran dengan VA Number ' . $vaNumber . ' berhasil dibatalkan.');

        } catch (\Exception $e) {
            return redirect()->route('company.payment.waiting', $payment)
                ->with('error', 'Gagal membatalkan pembayaran. Silakan coba lagi.');
        }
    }

    public function checkPaymentStatus(PaymentTransaction $payment)
    {
        return response()->json([
            'status' => $payment->status,
            'payment_date' => $payment->payment_date,
        ]);
    }   

    /**
     * Display the specified resource.
     */
    public function show(PaymentTransaction $paymentTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentTransaction $paymentTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentTransaction $paymentTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentTransaction $paymentTransaction)
    {
        //
    }
}
