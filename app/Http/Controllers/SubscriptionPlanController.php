<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK: HANYA ADMIN YANG DAPAT MENGAKSES HALAMAN INI.');
        }
        $plans = SubscriptionPlan::latest()->paginate(10);
        return view('admin.subscription_plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }
        return view('admin.subscription_plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }

        $validated = $request->validate([
            'plan_name' => 'required|string|max:255|unique:subscription_plans,plan_name',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'job_post_limit' => 'required|integer|min:0',
            'allow_verified_badge' => 'required|boolean',
        ]);

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Paket langganan berhasil dibuat.');
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
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }
        return view('admin.subscription_plans.edit', ['plan' => $subscriptionPlan]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }

        $validated = $request->validate([
            'plan_name' => ['required', 'string', 'max:255', Rule::unique('subscription_plans')->ignore($subscriptionPlan->id)],
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'job_post_limit' => 'required|integer|min:0',
            'allow_verified_badge' => 'required|boolean',
        ]);

        $subscriptionPlan->update($validated);

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Paket langganan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }

        // Keamanan: Cek apakah paket ini sedang digunakan
        if ($subscriptionPlan->companySubscriptions()->exists()) {
            return redirect()->route('admin.subscription-plans.index')->with('error', 'Paket tidak dapat dihapus karena sedang digunakan oleh perusahaan.');
        }

        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Paket langganan berhasil dihapus.');
    }
}
