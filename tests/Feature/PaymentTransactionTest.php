<?php

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\PaymentTransaction;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->role = Role::create([
        'name' => 'company', 
        'display_name' => 'Company',
        'description' => 'Company role',
    ]);

    $this->user = User::factory()->create([
        'role_id' => $this->role->id,
        'password' => bcrypt('password'),
    ]);

    $this->company = Company::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->user->refresh();

    $this->subscription = SubscriptionPlan::factory()->create(['price' => 30000]);

    $this->actingAs($this->user);
});

it('successfully processes a payment by hitting the real API and Pending Payment', function () {
    //  dd([
    //     'User dari Properti Test ($this->user)' => $this->user->toArray(),
    //     'Company dari Properti Test ($this->company)' => $this->company->toArray(),
    //     'User yang Sedang Login (auth()->user())' => auth()->user()->toArray(),
    // ]);

    $response = $this->post(route('payment.process', $this->subscription), [
        'payment_method' => 'virtual_account',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect();

    $this->assertDatabaseHas('payment_transactions', [
        'id_company_subscription' => 1,
        'amount' => 30000,
    ]);

    $transaction = PaymentTransaction::latest('id')->first();
    expect($transaction->va_number)->not()->toBeNull();
    expect($transaction->payment_url)->not()->toBeNull();

    // Pastikan relasi terbentuk
    $this->assertDatabaseHas('company_subscriptions', [
        'id_company' => $this->company->id,
        'id_plan' => $this->subscription->id,
    ]);

    $this->assertDatabaseHas('payment_transactions', [
        'id_company_subscription' => 1,
        'amount' => 30000,
        'va_number' => $transaction->va_number,
        'payment_url' => $transaction->payment_url,
        'status' => 'pending',
    ]);
});
