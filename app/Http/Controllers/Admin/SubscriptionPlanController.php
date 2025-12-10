<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::latest()->get();
        return view('admin.subscription_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription_plans.create');
    }

    public function store(Request $request)
    {
        // Sesuaikan validasi dengan nama kolom DB
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'job_post_limit' => 'required|integer|min:1', // Kolom baru
            'allow_verified_badge' => 'required|boolean', // Kolom baru
        ]);

        SubscriptionPlan::create($request->all());

        return redirect()->route('admin.subscription_plans.index')
            ->with('success', 'Paket langganan berhasil ditambahkan!');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription_plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'job_post_limit' => 'required|integer|min:1',
            'allow_verified_badge' => 'required|boolean',
        ]);

        $subscriptionPlan->update($request->all());

        return redirect()->route('admin.subscription_plans.index')
            ->with('success', 'Paket langganan berhasil diperbarui!');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription_plans.index')
            ->with('success', 'Paket langganan berhasil dihapus!');
    }
}