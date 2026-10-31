<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminSubscriptionOverrideTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $regularContractor;

    protected Company $company;

    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'name' => 'Platform Director',
            'email' => 'director@superadmin.com',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->company = Company::create([
            'name' => 'Delta Infrastructures',
            'code' => 'DELTA-01',
            'referral_code' => 'NCC-REF-DLT01',
            'contact_person' => 'Sunil Verma',
            'email' => 'sunil@delta.com',
            'status' => 'active',
            'wallet_balance' => 200.00,
            'max_workers' => 15,
            'max_meters' => 20000,
        ]);

        $this->regularContractor = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'contractor',
            'is_active' => true,
        ]);

        $this->plan = Plan::create([
            'name' => 'Standard Grid',
            'slug' => 'standard-grid',
            'max_meters' => 50000,
            'max_workers' => 25,
            'price_1m' => 1000.00,
            'price_3m' => 2800.00,
            'price_6m' => 5200.00,
            'price_1y' => 9500.00,
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_assign_custom_deal_override_without_cluttering_plans(): void
    {
        $initialPlanCount = Plan::count();

        // 45-day custom deal, price ₹3500, custom quota: 65,000 meters, 35 workers
        $response = $this->actingAs($this->superAdmin)->post(
            "/super-admin/companies/{$this->company->id}/override-subscription",
            [
                'plan_id' => $this->plan->id,
                'duration_days' => 45,
                'price' => 3500.00,
                'max_meters' => 65000,
                'max_workers' => 35,
                'notes' => 'Special 45-day mid-cycle seasonal deal approved by Super Admin.',
            ]
        );

        $response->assertRedirect('/super-admin/companies');
        $response->assertSessionHas('success');

        // Master plans count should remain untouched (no clutter in plans table)
        $this->assertEquals($initialPlanCount, Plan::count());

        // Company quotas updated
        $this->company->refresh();
        $this->assertEquals(65000, $this->company->max_meters);
        $this->assertEquals(35, $this->company->max_workers);

        // Subscription created with custom deal flags
        $sub = Subscription::where('company_id', $this->company->id)->latest()->first();
        $this->assertNotNull($sub);
        $this->assertTrue($sub->is_custom_deal);
        $this->assertEquals('custom', $sub->billing_cycle);
        $this->assertEquals(45, $sub->duration_days);
        $this->assertEquals(3500.00, $sub->price);
        $this->assertEquals(3500.00, $sub->amount_paid);
        $this->assertEquals('super_admin_override', $sub->payment_method);
        $this->assertEquals('Special 45-day mid-cycle seasonal deal approved by Super Admin.', $sub->custom_deal_notes);
        $this->assertEquals($this->superAdmin->id, $sub->created_by);
    }

    public function test_super_admin_can_gift_and_adjust_agency_wallet_with_audit_reason(): void
    {
        // Gift ₹1,500.00 credit
        $response = $this->actingAs($this->superAdmin)->post(
            "/super-admin/wallets/{$this->company->id}/adjust",
            [
                'type' => 'credit',
                'amount' => 1500.00,
                'reason' => 'Diwali Platform Gift Bonus',
            ]
        );

        $response->assertSessionHas('success');

        $this->company->refresh();
        // 200 + 1500 = 1700
        $this->assertEquals(1700.00, $this->company->wallet_balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'company_id' => $this->company->id,
            'type' => 'credit',
            'amount' => 1500.00,
            'balance_after' => 1700.00,
            'reason' => '[Admin Adjustment] Diwali Platform Gift Bonus',
            'created_by' => $this->superAdmin->id,
        ]);

        // Debit ₹400.00
        $response2 = $this->actingAs($this->superAdmin)->post(
            "/super-admin/wallets/{$this->company->id}/adjust",
            [
                'type' => 'debit',
                'amount' => 400.00,
                'reason' => 'Correction for duplicate billing voucher',
            ]
        );

        $response2->assertSessionHas('success');

        $this->company->refresh();
        // 1700 - 400 = 1300
        $this->assertEquals(1300.00, $this->company->wallet_balance);
    }

    public function test_super_admin_can_view_wallets_plans_and_referrals_pages(): void
    {
        $resWallets = $this->actingAs($this->superAdmin)->get('/super-admin/wallets');
        $resWallets->assertStatus(200);
        $resWallets->assertSee('Agency Wallets &amp; Platform Credits', false);
        $resWallets->assertSee('Delta Infrastructures');

        $resPlans = $this->actingAs($this->superAdmin)->get('/super-admin/plans');
        $resPlans->assertStatus(200);
        $resPlans->assertSee('Subscription Plans Master');
        $resPlans->assertSee('Standard Grid');

        $resReferrals = $this->actingAs($this->superAdmin)->get('/super-admin/referrals');
        $resReferrals->assertStatus(200);
        $resReferrals->assertSee('Platform Referrals Ledger');
    }

    public function test_regular_contractor_is_forbidden_from_super_admin_routes(): void
    {
        $response = $this->actingAs($this->regularContractor)->get('/super-admin/wallets');
        $response->assertStatus(403);

        $response2 = $this->actingAs($this->regularContractor)->get('/super-admin/plans');
        $response2->assertStatus(403);
    }

    public function test_super_admin_can_toggle_plan_status_between_archived_and_active(): void
    {
        $promoPlan = Plan::create([
            'name' => 'Special Promo Tier',
            'slug' => 'promo-tier',
            'max_meters' => 30000,
            'max_workers' => 20,
            'price_1m' => 800.00,
            'price_3m' => 2200.00,
            'price_6m' => 4000.00,
            'price_1y' => 7500.00,
            'status' => 'promo',
        ]);

        // Toggling non-archived plan (promo) should archive it
        $resArchive = $this->actingAs($this->superAdmin)->patch("/super-admin/plans/{$promoPlan->id}/toggle");
        $resArchive->assertSessionHas('success');
        $promoPlan->refresh();
        $this->assertEquals('archived', $promoPlan->status);

        // Toggling archived plan should activate it
        $resActivate = $this->actingAs($this->superAdmin)->patch("/super-admin/plans/{$promoPlan->id}/toggle");
        $resActivate->assertSessionHas('success');
        $promoPlan->refresh();
        $this->assertEquals('active', $promoPlan->status);
    }
}
