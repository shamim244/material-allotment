<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $contractor;

    protected Plan $plan;

    protected SubscriptionService $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Apex Power Systems',
            'code' => 'NCC-APX-01',
            'referral_code' => 'NCC-REF-APX88',
            'contact_person' => 'Vikram Sharma',
            'email' => 'vikram@apexpower.com',
            'status' => 'active',
            'wallet_balance' => 3000.00,
            'max_workers' => 10,
            'max_meters' => 10000,
        ]);

        $this->contractor = User::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Vikram Sharma',
            'email' => 'vikram@apexpower.com',
            'role' => 'contractor',
            'is_active' => true,
        ]);

        $this->plan = Plan::create([
            'name' => 'Pro Agency Tier',
            'slug' => 'pro-agency',
            'badge' => 'Most Popular',
            'max_meters' => 150000,
            'max_workers' => 75,
            'price_1m' => 2500.00,
            'price_3m' => 6750.00,
            'price_6m' => 12500.00,
            'price_1y' => 22000.00,
            'features' => ['150,000 meters', '75 workers', 'Priority support'],
            'status' => 'active',
            'is_featured' => true,
        ]);

        $this->subscriptionService = app(SubscriptionService::class);
    }

    public function test_contractor_can_view_billing_page_with_plans_and_wallet_passbook(): void
    {
        $response = $this->actingAs($this->contractor)->get('/billing');

        $response->assertStatus(200);
        $response->assertSee('Billing &amp; Agency Wallet', false);
        $response->assertSee('Pro Agency Tier');
        $response->assertSee('3,000.00'); // Wallet balance
        $response->assertSee('NCC-REF-APX88'); // Referral code
    }

    public function test_contractor_can_subscribe_with_full_wallet_payment(): void
    {
        // 1-month plan is ₹2500. Wallet balance is ₹3000.
        // Full price should be covered by wallet.
        $response = $this->actingAs($this->contractor)->post('/billing/subscribe', [
            'plan_id' => $this->plan->id,
            'cycle' => '1m',
            'use_wallet' => 1,
        ]);

        $response->assertRedirect('/billing');
        $response->assertSessionHas('success');

        $this->company->refresh();
        // Wallet should be deducted by ₹2500 -> remaining ₹500
        $this->assertEquals(500.00, $this->company->wallet_balance);

        // Quotas should be updated
        $this->assertEquals(150000, $this->company->max_meters);
        $this->assertEquals(75, $this->company->max_workers);

        // Subscription record should be created
        $sub = Subscription::where('company_id', $this->company->id)->latest()->first();
        $this->assertNotNull($sub);
        $this->assertEquals('Pro Agency Tier', $sub->plan_name);
        $this->assertEquals('1m', $sub->billing_cycle);
        $this->assertEquals(2500.00, $sub->price);
        $this->assertEquals(2500.00, $sub->wallet_discount);
        $this->assertEquals(0.00, $sub->amount_paid);
        $this->assertEquals('wallet', $sub->payment_method);
        $this->assertEquals('active', $sub->status);
        $this->assertTrue($sub->ends_at->isFuture());

        // Wallet ledger entry created
        $this->assertDatabaseHas('wallet_transactions', [
            'company_id' => $this->company->id,
            'type' => 'debit',
            'amount' => 2500.00,
            'balance_after' => 500.00,
        ]);
    }

    public function test_contractor_can_subscribe_with_partial_wallet_payment(): void
    {
        // 3-month plan is ₹6750. Wallet balance is ₹3000.
        // Wallet discount: ₹3000, Net payable: ₹3750.
        $response = $this->actingAs($this->contractor)->post('/billing/subscribe', [
            'plan_id' => $this->plan->id,
            'cycle' => '3m',
            'use_wallet' => 1,
        ]);

        $response->assertRedirect('/billing');
        $response->assertSessionHas('success');

        $this->company->refresh();
        $this->assertEquals(0.00, $this->company->wallet_balance);

        $sub = Subscription::where('company_id', $this->company->id)->latest()->first();
        $this->assertNotNull($sub);
        $this->assertEquals(6750.00, $sub->price);
        $this->assertEquals(3000.00, $sub->wallet_discount);
        $this->assertEquals(3750.00, $sub->amount_paid);
        $this->assertEquals('manual', $sub->payment_method);
    }

    public function test_contractor_can_subscribe_without_wallet_deduction(): void
    {
        $response = $this->actingAs($this->contractor)->post('/billing/subscribe', [
            'plan_id' => $this->plan->id,
            'cycle' => '1m',
            'use_wallet' => 0,
        ]);

        $response->assertRedirect('/billing');

        $this->company->refresh();
        // Wallet balance remains untouched at 3000
        $this->assertEquals(3000.00, $this->company->wallet_balance);

        $sub = Subscription::where('company_id', $this->company->id)->latest()->first();
        $this->assertEquals(0.00, $sub->wallet_discount);
        $this->assertEquals(2500.00, $sub->amount_paid);
    }

    public function test_subscribing_while_active_extends_expiry_date(): void
    {
        // Subscribe first for 1 month (30 days)
        $sub1 = $this->subscriptionService->subscribe($this->company, $this->plan, '1m', false);
        $firstEndsAt = $sub1->ends_at;

        // Immediately renew for 3 months (90 days)
        $sub2 = $this->subscriptionService->subscribe($this->company, $this->plan, '3m', false);

        // New subscription starts when first one ends and extends by 90 days
        $this->assertEquals(
            $firstEndsAt->format('Y-m-d H:i'),
            $sub2->starts_at->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $firstEndsAt->copy()->addDays(90)->format('Y-m-d H:i'),
            $sub2->ends_at->format('Y-m-d H:i')
        );
    }

    public function test_contractor_cannot_subscribe_to_archived_plan(): void
    {
        $archivedPlan = Plan::create([
            'name' => 'Legacy Tier',
            'slug' => 'legacy-tier',
            'max_meters' => 5000,
            'max_workers' => 5,
            'price_1m' => 500.00,
            'price_3m' => 1400.00,
            'price_6m' => 2600.00,
            'price_1y' => 4800.00,
            'status' => 'archived',
        ]);

        $response = $this->actingAs($this->contractor)->post('/billing/subscribe', [
            'plan_id' => $archivedPlan->id,
            'cycle' => '1m',
            'use_wallet' => 1,
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('archived', session('error'));

        // No subscription created
        $this->assertDatabaseMissing('subscriptions', [
            'company_id' => $this->company->id,
            'plan_id' => $archivedPlan->id,
        ]);
    }

    public function test_subscription_with_invalid_cycle_is_rejected(): void
    {
        $response = $this->actingAs($this->contractor)->post('/billing/subscribe', [
            'plan_id' => $this->plan->id,
            'cycle' => '10y', // Invalid cycle
            'use_wallet' => 1,
        ]);

        $response->assertSessionHasErrors(['cycle']);
    }

    public function test_subscription_service_throws_exception_on_invalid_cycle(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subscriptionService->subscribe($this->company, $this->plan, 'bad_cycle', false);
    }

    public function test_billing_page_isolates_meters_and_workers_count_per_company(): void
    {
        // Add 3 meters and 2 workers to this company
        for ($i = 1; $i <= 3; $i++) {
            \App\Models\Meter::create([
                'company_id' => $this->company->id,
                'meter_number' => "APX-MTR-00{$i}",
                'status' => 'stock',
            ]);
        }
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'company_id' => $this->company->id,
                'name' => "Worker {$i}",
                'email' => "worker{$i}@apexpower.com",
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'plain_password' => 'password',
                'role' => 'worker',
                'is_active' => true,
            ]);
        }

        // Create ANOTHER company with 50 meters and 10 workers
        $otherCompany = Company::create([
            'name' => 'Other Power Corp',
            'code' => 'OTHER-01',
            'referral_code' => 'NCC-REF-OTH01',
            'contact_person' => 'Other Admin',
            'email' => 'other@power.com',
            'status' => 'active',
            'wallet_balance' => 0.00,
            'max_workers' => 10,
            'max_meters' => 10000,
        ]);
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\Meter::create([
                'company_id' => $otherCompany->id,
                'meter_number' => "OTH-MTR-00{$i}",
                'status' => 'stock',
            ]);
        }

        $response = $this->actingAs($this->contractor)->get('/billing');
        $response->assertStatus(200);

        // Should see Apex Power's 3 meters, NOT the 13 total platform meters
        $response->assertSee('3 / 10,000');
        // Should see Apex Power's 2 workers, NOT other company's
        $response->assertSee('2 / 10');
    }
}
