<?php 

namespace App\Actions;

use App\Models\Company;
use App\Models\PaymentTransaction;

class checkPendingPayment {
    public function __invoke(Company $company) {
        return PaymentTransaction::where('status', 'pending')
                ->whereHas('companySubscription', function ($query) use ($company) {
                    $query->where('id_company', $company->id);
                })
                ->latest()
                ->first();
    }
}

