<?php

use App\Models\SubscriptionPlan;
use App\Models\CompanySubscription;
use App\Models\PaymentTransaction;
use App\Actions\CreatePaymentSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a payment transaction with provided data', function () {
    $role = Role::factory()->create(['name' => 'company']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $company = Company::factory()->create(['user_id' => $user->id]);
    $plan = SubscriptionPlan::factory()->create([
        'plan_name' => 'Pro Plan',
        'price' => 500000,
        'duration_days' => 30,
    ]);
    $companySubscription = CompanySubscription::factory()->create([
        'id_plan' => $plan->id,
        'id_company' => $company->id,
    ]);

    $paymentData = [
        'amount' => 500000,
        'payment_method' => 'midtrans',
        'va_number' => '1234567890',
        'payment_url' => 'https://example.com/pay',
        'status' => 'pending',
        'expires_at' => 24, // hours
    ];

    $action = app(CreatePaymentSubscription::class);

    $payment = $action($companySubscription, $paymentData);
    
    expect($payment)
        ->toBeInstanceOf(PaymentTransaction::class)
        ->and($payment->amount)->toEqual(500000)
        ->and($payment->payment_method)->toBe('midtrans')
        ->and($payment->va_number)->toBe('1234567890')
        ->and($payment->status)->toBe('pending')
        ->and($payment->expires_at)->not()->toBeNull();

    $this->assertDatabaseHas('payment_transactions', [
        'id_company_subscription' => $companySubscription->id,
        'amount' => 500000,
        'payment_method' => 'midtrans',
        'status' => 'pending',
    ]);
});

it('creates a payment transaction with default values', function () {
    $role = Role::factory()->create(['name' => 'company']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $company = Company::factory()->create(['user_id' => $user->id]);
    $plan = SubscriptionPlan::factory()->create();

    $companySubscription = CompanySubscription::factory()->create([
        'id_plan' => $plan->id,
        'id_company' => $company->id,
    ]);

    $action = app(CreatePaymentSubscription::class);
    $payment = $action($companySubscription, []);

    expect($payment)
        ->toBeInstanceOf(PaymentTransaction::class)
        ->amount->toEqual(0)
        ->payment_method->toBe('bank_transfer')
        ->va_number->toBeNull()
        ->payment_url->toBeNull()
        ->status->toBe('pending')
        ->expires_at->toBeNull();

    $this->assertDatabaseHas('payment_transactions', [
        'id_company_subscription' => $companySubscription->id,
        'amount' => 0,
        'payment_method' => 'bank_transfer',
        'status' => 'pending',
        'va_number' => null,
    ]);
});