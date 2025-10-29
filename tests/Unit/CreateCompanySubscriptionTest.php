<?php

namespace Tests\Feature;

use App\Actions\CreateCompanySubscription;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

uses(TestCase::class, RefreshDatabase::class);

it('creates a company subscription and sets the correct dates and status', function () {
    $role = Role::factory()->create(['name' => 'company']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $company = Company::factory()->create(['user_id' => $user->id]);
    
    $plan = SubscriptionPlan::factory()->create([
        'duration_days' => 30, // days
    ]);

    $action = app(CreateCompanySubscription::class);

    $subscription = $action($company, $plan);

    expect($subscription)->toBeInstanceOf(CompanySubscription::class);
    expect($subscription->id_company)->toBe($company->id);
    expect($subscription->id_plan)->toBe($plan->id);
    expect($subscription->status)->toBe('inactive');
    expect($subscription->start_date)->not->toBeNull();
    expect($subscription->end_date)->not->toBeNull();

    $startDate = Carbon::parse($subscription->start_date);
    $endDate = Carbon::parse($subscription->end_date);
    expect(abs($endDate->diffInDays($startDate)))->toEqual($plan->duration_days);
    
    $this->assertDatabaseHas('company_subscriptions', [
        'id_company' => $company->id,
        'id_plan' => $plan->id,
        'status' => 'inactive',
    ]);
});