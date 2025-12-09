<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of payment transactions.
     */
    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['companySubscription.company.user', 'companySubscription.plan']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('va_number', 'like', "%{$request->search}%")
                  ->orWhere('id', 'like', "%{$request->search}%")
                  ->orWhereHas('companySubscription.company', function($q) use ($request) {
                      $q->where('company_name', 'like', "%{$request->search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(15);

        $stats = [
            'total' => PaymentTransaction::count(),
            'success' => PaymentTransaction::where('status', 'success')->count(),
            'pending' => PaymentTransaction::where('status', 'pending')->count(),
            'failed' => PaymentTransaction::where('status', 'failed')->count(),
            'total_revenue' => PaymentTransaction::where('status', 'success')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show the specified payment transaction.
     */
    public function show(PaymentTransaction $payment)
    {
        $payment->load(['companySubscription.company.user', 'companySubscription.plan']);
        
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Manually update payment status (for corrections).
     */
    public function updateStatus(Request $request, PaymentTransaction $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,success,failed',
            'payment_date' => 'nullable|date',
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus = $payment->status;
        $oldValues = $payment->toArray();

        $payment->update([
            'status' => $validated['status'],
            'payment_date' => $validated['payment_date'] ?? ($validated['status'] === 'success' ? now() : $payment->payment_date),
        ]);

        // If status changed to success, activate subscription
        if ($validated['status'] === 'success' && $oldStatus !== 'success') {
            $subscription = $payment->companySubscription;
            if ($subscription) {
                $subscription->update(['status' => 'active']);
                
                // Update company verification if plan allows
                if ($subscription->plan->allow_verified_badge) {
                    $subscription->company->update(['is_verified' => true]);
                }
            }
        }

        AuditLog::log(
            'payment.status_updated_manual',
            $payment,
            "Payment status manually updated from {$oldStatus} to {$validated['status']}. Reason: " . ($validated['reason'] ?? 'N/A'),
            $oldValues,
            $payment->fresh()->toArray()
        );

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
