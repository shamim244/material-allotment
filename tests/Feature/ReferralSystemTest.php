<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Referral;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Company $referrerCompany;

    protected Company $referredCompany;

    protected User $superAdmin;

    protected Plan $plan;

    protected SubscriptionService $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriptionService = app(SubscriptionService::class);

        // Referrer agency
        $this->referrerCompany = Company::create([
            'name' => 'Bihar Power Contractors Ltd',
            'code' => 'BPC-001',
            'referral_code' => 'NCC-REF-BPC01',
            'contact_person' => 'Rameshwar Roy',
            'email' => 'rameshwar@bpc.com',
            'status' => 'active',
            'wallet_balance' => 0.00,
            'max_workers' => 20,
            'max_meters' => 50000,
        ]);

        // Super Admin user
        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Default plan
        $this->plan = Plan::create([
            'name' => 'Standard Starter',
            'slug' => 'starter-test',
            'max_meters' => 50000,
            'max_workers' => 20,
            'price_1m' => 1000.00,
            'price_3m' => 2700.00,
            'price_6m' => 5000.00,
            'price_1y' => 9000.00,
            'status' => 'active',
        ]);
    }

    public function test_company_has_unique_referral_code(): void
    {
        $code = Company::generateUniqueReferralCode('NCC-REF');
        $this->assertStringStartsWith('NCC-REF-', $code);

        $newCompany = Company::create([
            'name' => 'New Agency',
            'code' => 'NEW-001',
            'referral_code' => $code,
            'contact_person' => 'Anil',
            'email' => 'anil@new.com',
            'status' => 'active',
            'max_workers' => 10,
            'max_meters' => 10000,
        ]);

        $this->assertEquals($code, $newCompany->referral_code);
    }

    public function test_super_admin_onboarding_with_referral_code_creates_pending_referral(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/companies', [
            'name' => 'Koshi Grid Solutions',
            'code' => 'KOSHI-01',
            'referred_by_code' => $this->referrerCompany->referral_code,
            'contact_person' => 'Suresh Jha',
            'email' => 'suresh@koshi.com',
            'phone' => '9876543210',
            'max_workers' => 30,
            'max_meters' => 40000,
            'admin_name' => 'Suresh Jha',
            'admin_email' => 'suresh.admin@koshi.com',
            'admin_password' => 'SecretPass123',
        ]);

        $response->assertRedirect('/super-admin/companies');
        $response->assertSessionHas('success');

        $koshi = Company::where('code', 'KOSHI-01')->firstOrFail();
        $this->assertEquals($this->referrerCompany->id, $koshi->referred_by_id);
        $this->assertNotNull($koshi->referral_code);

        // Assert pending referral was recorded
        $referral = Referral::where('referred_company_id', $koshi->id)->first();
        $this->assertNotNull($referral);
        $this->assertEquals($this->referrerCompany->id, $referral->referrer_company_id);
        $this->assertEquals('pending', $referral->status);
        $this->assertEquals(500.00, $referral->reward_amount);
    }

    public function test_referrer_automatically_gets_reward_credited_upon_referred_company_subscription(): void
    {
        // 1. Create referred company linked to referrer
        $referredCompany = Company::create([
            'name' => 'Mithila Metering Agency',
            'code' => 'MMA-001',
            'referral_code' => 'NCC-REF-MMA01',
            'referred_by_id' => $this->referrerCompany->id,
            'contact_person' => 'Manoj Mishra',
            'email' => 'manoj@mma.com',
            'status' => 'active',
            'wallet_balance' => 0.00,
            'max_workers' => 10,
            'max_meters' => 10000,
        ]);

        Referral::create([
            'referrer_company_id' => $this->referrerCompany->id,
            'referred_company_id' => $referredCompany->id,
            'reward_amount' => 500.00,
            'status' => 'pending',
        ]);

        $this->assertEquals(0.00, $this->referrerCompany->wallet_balance);

        // 2. Referred company subscribes to a plan
        $this->subscriptionService->subscribe($referredCompany, $this->plan, '1m', false);

        // 3. Check referrer company wallet: should be credited ₹500
        $this->referrerCompany->refresh();
        $this->assertEquals(500.00, $this->referrerCompany->wallet_balance);

        // 4. Check wallet transaction ledger entry for referrer
        $tx = WalletTransaction::where('company_id', $this->referrerCompany->id)->first();
        $this->assertNotNull($tx);
        $this->assertEquals('credit', $tx->type);
        $this->assertEquals(500.00, $tx->amount);
        $this->assertEquals(500.00, $tx->balance_after);
        $this->assertStringContainsString('Mithila Metering Agency', $tx->reason);

        // 5. Check referral record status updated to rewarded
        $referral = Referral::where('referred_company_id', $referredCompany->id)->first();
        $this->assertEquals('rewarded', $referral->status);
        $this->assertNotNull($referral->rewarded_at);
        $this->assertEquals($tx->id, $referral->wallet_transaction_id);

        // 6. If referred company renews or subscribes again, referrer is NOT rewarded twice
        $this->subscriptionService->subscribe($referredCompany, $this->plan, '3m', false);

        $this->referrerCompany->refresh();
        $this->assertEquals(500.00, $this->referrerCompany->wallet_balance);
        $this->assertCount(1, WalletTransaction::where('company_id', $this->referrerCompany->id)->get());
    }

    public function test_public_registration_with_referral_code_creates_contractor_agency_and_pending_referral(): void
    {
        $response = $this->post('/register', [
            'name' => 'Rohan Contractor',
            'email' => 'rohan@magadh.com',
            'company_name' => 'Magadh Infra Services',
            'referral_code' => $this->referrerCompany->referral_code,
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $response->assertRedirect('/billing');
        $this->assertAuthenticated();

        $newCompany = Company::where('name', 'Magadh Infra Services')->firstOrFail();
        $this->assertEquals($this->referrerCompany->id, $newCompany->referred_by_id);
        $this->assertNotNull($newCompany->referral_code);

        // Pending referral created
        $referral = Referral::where('referred_company_id', $newCompany->id)->firstOrFail();
        $this->assertEquals('pending', $referral->status);
        $this->assertEquals($this->referrerCompany->id, $referral->referrer_company_id);

        // When the newly registered agency subscribes on billing, referrer gets rewarded
        $subResponse = $this->post('/billing/subscribe', [
            'plan_id' => $this->plan->id,
            'cycle' => '1m',
            'use_wallet' => 0,
        ]);
        $subResponse->assertRedirect('/billing');

        $this->referrerCompany->refresh();
        $this->assertEquals(500.00, $this->referrerCompany->wallet_balance);
        $referral->refresh();
        $this->assertEquals('rewarded', $referral->status);
    }

    public function test_self_referral_is_prevented(): void
    {
        // Company referring itself
        $this->referrerCompany->referred_by_id = $this->referrerCompany->id;
        $this->referrerCompany->save();

        $result = $this->subscriptionService->checkAndRewardReferral($this->referrerCompany);
        $this->assertNull($result);
        $this->assertEquals(0.00, $this->referrerCompany->wallet_balance);
    }
}
