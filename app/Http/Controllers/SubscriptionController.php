<?php

namespace App\Http\Controllers;

use App\Actions\CheckActiveSubscription;
use App\Actions\checkPendingPayment;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\PaymentTransaction;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    private $checkActiveSubscription;
    private $checkPending;

    public function __construct(checkPendingPayment $checkPending, CheckActiveSubscription $checkActiveSubscription) {
        $this->checkPending = $checkPending;
        $this->checkActiveSubscription = $checkActiveSubscription;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = SubscriptionPlan::all();
        
        /** @var \App\Models\User */
        $user = Auth::user();
        $activeSubscription = null;

        // Cek apakah perusahaan memiliki profil dan cari langganan aktif
        if ($user->company) {
            $activeSubscription = $this->checkActiveSubscription->__invoke($user->company);
        }

        return view('company-subscriptions.index', compact('plans', 'activeSubscription'));
    }

    /**
     * Show the form for creating a new resource.
     */
    //public function create()
    //{
        //
    //}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        /** @var \App\Models\User */
        $user = Auth::user();

        $company = $user->company;
        $plan = SubscriptionPlan::find($request->plan_id);

        // Otorisasi: Pastikan pengguna adalah company dan memiliki profil
        if (!$user->hasRole('company') || !$company) {
            return redirect()->back()->with('error', 'Hanya perusahaan yang dapat berlangganan.');
        }

        // Catatan: Di aplikasi nyata, Anda akan menambahkan logika payment gateway di sini.
        // Anda juga mungkin ingin menonaktifkan langganan lama sebelum membuat yang baru.

        // Membuat data langganan baru untuk perusahaan
        CompanySubscription::create([
            'company_id' => $company->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($plan->duration_days),
            'status' => 'active', // Enum: 'active', 'expired', 'canceled'
        ]);

        return redirect()->route('subscriptions.index')
                         ->with('success', "Anda berhasil berlangganan paket {$plan->plan_name}!");
    }

    public function confirmationOrder(SubscriptionPlan $plan)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $checkPending = $this->checkPending->__invoke($user->company);
        Log::info('Pending Payments: ' . $checkPending);

        if ($checkPending) {
            $err = "You have pending payment transactions. Please complete them before creating a new subscription.";
            return view('company-subscriptions.confirm', compact('plan', 'err'));
        }

        return view('company-subscriptions.confirm', compact('plan'));
    }

    public function cancelSubscription(CompanySubscription $subscription)
    {
        if (!$subscription->trashed()) {
            
            $subscription->update([
                'status' => 'canceled',
            ]);

            $subscription->delete();
            return redirect()->route('company.subscriptions.index')->with('success', 'Langganan berhasil dibatalkan.');
        }

        return redirect()->route('company.subscriptions.index')->with('info', 'Langganan ini sudah dibatalkan sebelumnya.');
    }   
    
    /**
     * Display the specified resource.
     */
    //public function show(string $id)
    //{
        //
    //}

    /**
     * Show the form for editing the specified resource.
     */
    //public function edit(string $id)
    //{
        //
    //}

    /**
     * Update the specified resource in storage.
     */
    //public function update(Request $request, string $id)
    //{
        //
    //}

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
        
    // }
}
