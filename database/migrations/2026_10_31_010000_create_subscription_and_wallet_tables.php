<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add wallet and referral fields to companies table
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('status');
            $table->string('referral_code', 50)->nullable()->unique()->after('code');
            $table->foreignId('referred_by_id')->nullable()->after('referral_code')->constrained('companies')->nullOnDelete();
        });

        // 2. Create master plans table (Tier + Multi-Cycle structure)
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('badge')->nullable(); // e.g. 'Most Popular', 'Best Value'
            $table->unsignedInteger('max_meters')->default(50000);
            $table->unsignedInteger('max_workers')->default(25);
            // Multi-cycle pricing
            $table->decimal('price_1m', 10, 2)->default(0.00);  // 30 days
            $table->decimal('price_3m', 10, 2)->default(0.00);  // 90 days
            $table->decimal('price_6m', 10, 2)->default(0.00);  // 180 days
            $table->decimal('price_1y', 10, 2)->default(0.00);  // 365 days
            $table->json('features')->nullable();
            $table->enum('status', ['active', 'promo', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Create subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->string('plan_name', 150);
            $table->enum('billing_cycle', ['1m', '3m', '6m', '1y', 'custom'])->default('1m');
            $table->unsignedInteger('duration_days')->default(30);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->decimal('price', 10, 2)->default(0.00); // Standard plan cycle price or deal price
            $table->decimal('wallet_discount', 10, 2)->default(0.00); // Paid using wallet balance
            $table->decimal('amount_paid', 10, 2)->default(0.00); // Net paid (price - wallet_discount)
            $table->enum('payment_method', ['wallet', 'manual', 'super_admin_override'])->default('wallet');
            $table->enum('status', ['active', 'expired', 'cancelled', 'renewed'])->default('active');
            $table->unsignedInteger('max_meters')->default(50000);
            $table->unsignedInteger('max_workers')->default(25);
            $table->boolean('is_custom_deal')->default(false);
            $table->text('custom_deal_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'status', 'ends_at'], 'subs_company_status_expiry_idx');
        });

        // 4. Create immutable wallet transactions ledger
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('reason', 255);
            $table->string('reference_id', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['company_id', 'created_at'], 'wallet_tx_company_date_idx');
        });

        // 5. Create referrals ledger table
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('referred_company_id')->constrained('companies')->onDelete('cascade');
            $table->decimal('reward_amount', 10, 2)->default(500.00);
            $table->enum('status', ['pending', 'rewarded', 'rejected'])->default('pending');
            $table->timestamp('rewarded_at')->nullable();
            $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete();
            $table->timestamps();

            $table->unique(['referred_company_id'], 'unique_company_referral');
            $table->index(['referrer_company_id', 'status'], 'referral_referrer_status_idx');
        });

        // 6. Backfill existing companies with referral code
        $companies = DB::table('companies')->get();
        foreach ($companies as $company) {
            $code = 'NCC-'.strtoupper(Str::random(3)).'-'.str_pad($company->id, 3, '0', STR_PAD_LEFT);
            DB::table('companies')->where('id', $company->id)->update([
                'referral_code' => $code,
                'wallet_balance' => 1500.00, // Gift initial welcome balance to test agency #1
            ]);

            // Add an initial credit ledger transaction for existing company
            DB::table('wallet_transactions')->insert([
                'company_id' => $company->id,
                'type' => 'credit',
                'amount' => 1500.00,
                'balance_after' => 1500.00,
                'reason' => 'Agency Welcome Bonus & Platform Launch Credit',
                'reference_id' => 'BONUS-INIT-'.$company->id,
                'created_at' => now(),
            ]);
        }

        // 7. Seed standard multi-cycle subscription plans
        $now = now();
        $starterId = DB::table('plans')->insertGetId([
            'name' => 'Starter Agency',
            'slug' => 'starter',
            'description' => 'Essential package for growing meter installation contractors and local teams.',
            'badge' => 'Starter Choice',
            'max_meters' => 25000,
            'max_workers' => 15,
            'price_1m' => 999.00,
            'price_3m' => 2699.00,  // ~10% off
            'price_6m' => 4999.00,  // ~16% off
            'price_1y' => 8999.00,  // ~25% off
            'features' => json_encode([
                'Up to 25,000 meters management',
                'Up to 15 field workers',
                'Real-time box & lot tracking',
                'Daily installation reports',
                'Standard email support',
            ]),
            'status' => 'active',
            'is_featured' => false,
            'sort_order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $proId = DB::table('plans')->insertGetId([
            'name' => 'Professional',
            'slug' => 'professional',
            'description' => 'Comprehensive platform power for established agencies managing multi-lot projects.',
            'badge' => 'Most Popular',
            'max_meters' => 100000,
            'max_workers' => 60,
            'price_1m' => 2499.00,
            'price_3m' => 6699.00,  // ~10% off
            'price_6m' => 12499.00, // ~16% off
            'price_1y' => 21999.00, // ~26% off
            'features' => json_encode([
                'Up to 100,000 meters management',
                'Up to 60 field workers',
                'Automated MI issue detection & challans',
                'Installation map & geo-analytics',
                'Full accounting & wage ledger',
                'Priority ticket & chat support',
            ]),
            'status' => 'active',
            'is_featured' => true,
            'sort_order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $entId = DB::table('plans')->insertGetId([
            'name' => 'Enterprise Grid',
            'slug' => 'enterprise',
            'description' => 'Maximum scale, custom quotas, and dedicated platform throughput for top-tier DISCOM contractors.',
            'badge' => 'High Scale',
            'max_meters' => 500000,
            'max_workers' => 250,
            'price_1m' => 5999.00,
            'price_3m' => 15999.00, // ~11% off
            'price_6m' => 29999.00, // ~16% off
            'price_1y' => 49999.00, // ~30% off
            'features' => json_encode([
                'Up to 500,000 meters management',
                'Up to 250 field workers',
                'High-speed bulk imports & exports',
                'Dedicated account manager',
                'Custom integrations & custom reports',
                '24/7 SLA platform uptime guarantee',
            ]),
            'status' => 'active',
            'is_featured' => false,
            'sort_order' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Seed an active Pro subscription for the primary company so it is active
        $primaryCompany = DB::table('companies')->first();
        if ($primaryCompany) {
            DB::table('subscriptions')->insert([
                'company_id' => $primaryCompany->id,
                'plan_id' => $proId,
                'plan_name' => 'Professional',
                'billing_cycle' => '1y',
                'duration_days' => 365,
                'starts_at' => $now,
                'ends_at' => $now->copy()->addDays(365),
                'price' => 21999.00,
                'wallet_discount' => 0.00,
                'amount_paid' => 21999.00,
                'payment_method' => 'super_admin_override',
                'status' => 'active',
                'max_meters' => 100000,
                'max_workers' => 60,
                'is_custom_deal' => false,
                'custom_deal_notes' => 'Initial platform rollout active subscription',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');

        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['referred_by_id']);
            $table->dropColumn(['wallet_balance', 'referral_code', 'referred_by_id']);
        });
    }
};
