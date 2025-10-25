<?php 

namespace App\Actions;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionPlan;

class CreateCompanySubscription {
    public function __invoke(Company $company, SubscriptionPlan $plan) {
        return CompanySubscription::create([
            'id_company' => $company->id,
            'id_plan' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_days),
            'status' => 'active',
        ]);
    }
}

