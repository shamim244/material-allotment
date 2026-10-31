<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $contractor;

    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Alpha Electric Services',
            'code' => 'ALPHA-01',
            'referral_code' => 'NCC-REF-ALP01',
            'contact_person' => 'Ravi Shankar',
            'email' => 'ravi@alpha.com',
            'status' => 'active',
            'wallet_balance' => 500.00,
            'max_workers' => 20,
            'max_meters' => 50000,
        ]);

        $this->contractor = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'contractor',
            'is_active' => true,
        ]);

        $this->plan = Plan::create([
            'name' => 'Standard Grid',
            'slug' => 'standard-grid-lc',
            'max_meters' => 50000,
            'max_workers' => 20,
            'price_1m' => 1000.00,
            'price_3m' => 2800.00,
            'price_6m' => 5200.00,
            'price_1y' => 9500.00,
            'status' => 'active',
        ]);
    }

    public function test_contractor_sees_7_day_warning_banner_when_subscription_is_expiring_soon(): void
    {
        // Active subscription expiring in 5 days
        Subscription::create([
            'company_id' => $this->company->id,
            'plan_id' => $this->plan->id,
            'plan_name' => $this->plan->name,
            'billing_cycle' => '1m',
            'duration_days' => 30,
            'starts_at' => now()->subDays(25),
            'ends_at' => now()->addDays(5),
            'price' => 1000.00,
            'wallet_discount' => 0.00,
            'amount_paid' => 1000.00,
            'status' => 'active',
            'max_meters' => 50000,
            'max_workers' => 20,
        ]);

        $this->assertTrue($this->company->isSubscriptionExpiringSoon(7));

        // When visiting dashboard, top expiry alert banner should be visible
        $response = $this->actingAs($this->contractor)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Subscription Notice');
        $response->assertSee('expires in');
    }

    public function test_contractor_sees_expired_alert_when_subscription_is_expired(): void
    {
        // Subscription that expired 2 days ago
        Subscription::create([
            'company_id' => $this->company->id,
            'plan_id' => $this->plan->id,
            'plan_name' => $this->plan->name,
            'billing_cycle' => '1m',
            'duration_days' => 30,
            'starts_at' => now()->subDays(32),
            'ends_at' => now()->subDays(2),
            'price' => 1000.00,
            'wallet_discount' => 0.00,
            'amount_paid' => 1000.00,
            'status' => 'expired',
            'max_meters' => 50000,
            'max_workers' => 20,
        ]);

        $this->assertTrue($this->company->isSubscriptionExpired());

        // Visiting dashboard shows expired warning
        $response = $this->actingAs($this->contractor)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Subscription Expired');
        $response->assertSee('Renew Plan');

        // Visiting /billing shows graceful expiry renewal card
        $resBilling = $this->actingAs($this->contractor)->get('/billing');
        $resBilling->assertStatus(200);
        $resBilling->assertSee('Your Subscription has Expired');
    }

    public function test_contractor_sees_welcome_activation_banner_when_no_active_subscription(): void
    {
        // No subscription created for this company at all
        $this->assertFalse($this->company->hasActiveSubscription());

        // Visiting dashboard shows No Active Plan banner
        $response = $this->actingAs($this->contractor)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('No Active Plan');
        $response->assertSee('Choose Plan');

        // Visiting /billing shows activate agency subscription card
        $resBilling = $this->actingAs($this->contractor)->get('/billing');
        $resBilling->assertStatus(200);
        $resBilling->assertSee('Activate Your Agency Subscription');
    }
}
