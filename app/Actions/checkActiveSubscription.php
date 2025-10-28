<?php 

namespace App\Actions;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionPlan;

class CheckActiveSubscription {
    public function __invoke(Company $company) {
        return CompanySubscription::withTrashed()
                ->where('id_company', $company->id)
                ->where('status', 'active')
                ->orWhere('status', 'canceled')
                ->where('end_date', '>', now())
                ->whereHas('paymentTransactions', function ($query) {
                    $query->where('status', 'success')
                          ->whereNotNull('payment_date');
                })
                ->latest()
                ->first();
    }
}

