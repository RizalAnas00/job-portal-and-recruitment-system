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
        //
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

        $companySubscription = $this->createCompanySubscription->__invoke($company, $subscription);

        $paymentTransaction = $this->createPaymentSubscription->__invoke($companySubscription, [
            'amount' => $subscription->price,
            'payment_method' => $request->input('payment_method'),
            'payment_url' => config('services.payment.payment_url', 'https://payment.example.com/pay'),
            'expires_at' => $expiredHours,
        ]);

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
                'callback_url' => route('payment.success'),
                'metadata' => [
                    'subscription_id' => $subscription->id,
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

                return redirect()->route('payment.waiting', $paymentTransaction);
            } else {
                $paymentTransaction->update(['payment_status' => 'failed']);
                return redirect()->route('customer.products.index')
                    ->with('error', 'Gagal membuat pembayaran. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $paymentTransaction->update(['payment_status' => 'failed']);
            // dd($e);
            return redirect()->route('customer.products.error')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
