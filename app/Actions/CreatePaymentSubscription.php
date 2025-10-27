<?php 

namespace App\Actions;

use App\Models\CompanySubscription;
use App\Models\PaymentTransaction;
use App\Models\SubscriptionPlan;

class CreatePaymentSubscription {
    public function __invoke(CompanySubscription $companySubscription, array $paymentData) {
        $expiresAtTimestamp = isset($paymentData['expires_at'])
            ? now()->addHours($paymentData['expires_at'])
            : null;

        return PaymentTransaction::create([
            'id_company_subscription' => $companySubscription->id,
            'amount' => $paymentData['amount'] ?? 0,
            'payment_date' => null,
            'payment_method' => $paymentData['payment_method'] ?? null,
            'va_number' => $paymentData['va_number'] ?? null,
            'payment_url' => $paymentData['payment_url'] ?? null,
            'status' => $paymentData['status'] ?? 'pending',
            'expires_at' => $expiresAtTimestamp,
        ]);
    }
}