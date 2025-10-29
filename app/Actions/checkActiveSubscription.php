<?php 

namespace App\Actions;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionPlan;

class CheckActiveSubscription {
    public function __invoke(Company $company) {
        return CompanySubscription::withTrashed()
                ->where('id_company', $company->id)
                ->where(function ($query) {
                    $query->where('status', 'active')
                          ->orWhere('status', 'canceled');
                })
                ->where('end_date', '>', now())
                ->where(function ($query) {
                    // Free plans (price = 0) don't need payment transactions
                    $query->whereHas('plan', function ($q) {
                        $q->where('price', 0);
                    })
                    // OR has successful payment for paid plans
                    ->orWhereHas('paymentTransactions', function ($q) {
                        $q->where('status', 'success')
                          ->whereNotNull('payment_date');
                    });
                })
                ->latest()
                ->first();
    }
}

