<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of company subscriptions.
     */
    public function index(Request $request)
    {
        $query = CompanySubscription::with(['company.user', 'plan']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('id_company', $request->company_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->whereHas('company', function($q) use ($request) {
                $q->where('company_name', 'like', "%{$request->search}%");
            });
        }

        $subscriptions = $query->latest()->paginate(15);

        $stats = [
            'total' => CompanySubscription::count(),
            'active' => CompanySubscription::where('status', 'active')
                ->where('end_date', '>', now())->count(),
            'expired' => CompanySubscription::where('status', 'active')
                ->where('end_date', '<=', now())->count(),
            'canceled' => CompanySubscription::where('status', 'canceled')->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    /**
     * Show the form for creating a manual subscription.
     */
    public function create()
    {
        $companies = Company::with('user')->whereHas('user', fn($q) => $q->where('is_active', true))->get();
        $plans = SubscriptionPlan::all();
        
        return view('admin.subscriptions.create', compact('companies', 'plans'));
    }

    /**
     * Store a manually created subscription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,canceled',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Deactivate existing active subscription if any
        $existingActive = CompanySubscription::where('id_company', $company->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        if ($existingActive) {
            $existingActive->update(['status' => 'canceled']);
        }

        $subscription = CompanySubscription::create($validated);

        // Update company verification if plan allows
        if ($validated['status'] === 'active' && $plan->allow_verified_badge) {
            $company->update(['is_verified' => true]);
        }

        AuditLog::log(
            'subscription.created_manual',
            $subscription,
            "Manual subscription created for {$company->company_name} - Plan: {$plan->plan_name}"
        );

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Langganan berhasil dibuat secara manual.');
    }

    /**
     * Extend a subscription manually.
     */
    public function extend(Request $request, CompanySubscription $subscription)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $oldEndDate = $subscription->end_date;
        $newEndDate = $subscription->end_date->addDays($validated['days']);
        
        $subscription->update(['end_date' => $newEndDate]);

        AuditLog::log(
            'subscription.extended',
            $subscription,
            "Subscription extended by {$validated['days']} days. New end date: {$newEndDate->format('Y-m-d')}"
        );

        return back()->with('success', "Langganan berhasil diperpanjang {$validated['days']} hari.");
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(CompanySubscription $subscription)
    {
        $subscription->update(['status' => 'canceled']);

        AuditLog::log(
            'subscription.canceled_manual',
            $subscription,
            "Subscription canceled by admin"
        );

        return back()->with('success', 'Langganan berhasil dibatalkan.');
    }
}
