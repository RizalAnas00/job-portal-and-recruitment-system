<?php 

namespace App\Actions;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionPlan;

class CheckActiveSubscription {
    public function __invoke(Company $company) {
        return CompanySubscription::where('id_company', $company->id)
                ->where('status', 'active')
                ->where('end_date', '>', now())
                ->latest()
                ->first();
    }
}

