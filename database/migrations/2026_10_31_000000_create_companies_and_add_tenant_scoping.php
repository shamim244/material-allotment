<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create companies table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'suspended', 'trial'])->default('active');
            $table->integer('max_workers')->default(50);
            $table->integer('max_meters')->default(100000);
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Insert Company #1 ("NCC Primary Agency") for all existing data
        $companyId = DB::table('companies')->insertGetId([
            'name' => 'NCC Primary Agency',
            'code' => 'NCC-001',
            'contact_person' => 'Primary Contractor',
            'email' => 'contractor@example.com',
            'phone' => '9876543210',
            'status' => 'active',
            'max_workers' => 100,
            'max_meters' => 500000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Add company_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        // Link existing users to Company #1
        DB::table('users')->update(['company_id' => $companyId]);

        // 4. Create master Super Admin user (company_id is null)
        $superAdminExists = DB::table('users')->where('email', 'superadmin@example.com')->exists();
        if (!$superAdminExists) {
            DB::table('users')->insert([
                'company_id' => null,
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'mobile' => '9999999999',
                'password' => Hash::make('password'),
                'plain_password' => Crypt::encryptString('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Add company_id to lots
        Schema::table('lots', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        DB::table('lots')->update(['company_id' => $companyId]);

        // 6. Add company_id to meters
        Schema::table('meters', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
            $table->index(['company_id', 'status'], 'meters_company_status_index');
        });
        DB::table('meters')->update(['company_id' => $companyId]);

        // 7. Add company_id to meter_groups
        Schema::table('meter_groups', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        DB::table('meter_groups')->update(['company_id' => $companyId]);

        // 8. Add company_id to assignments
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        DB::table('assignments')->update(['company_id' => $companyId]);

        // 9. Add company_id to meter_activations
        Schema::table('meter_activations', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
            $table->index(['company_id', 'mi_status'], 'activations_company_status_index');
        });
        DB::table('meter_activations')->update(['company_id' => $companyId]);

        // 10. Add company_id to installation_details
        Schema::table('installation_details', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        DB::table('installation_details')->update(['company_id' => $companyId]);

        // 11. Add company_id to return_requests
        Schema::table('return_requests', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        DB::table('return_requests')->update(['company_id' => $companyId]);

        // 12. Add company_id to accounting_ledger
        Schema::table('accounting_ledger', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        DB::table('accounting_ledger')->update(['company_id' => $companyId]);

        // 13. Add company_id to settings (nullable for global vs company settings)
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        DB::table('settings')->update(['company_id' => $companyId]);

        // 14. Add company_id to saved_reports
        Schema::table('saved_reports', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
        });
        DB::table('saved_reports')->update(['company_id' => $companyId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_reports', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('accounting_ledger', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('installation_details', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('meter_activations', function (Blueprint $table) {
            $table->dropIndex('activations_company_status_index');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('meter_groups', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('meters', function (Blueprint $table) {
            $table->dropIndex('meters_company_status_index');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('lots', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        DB::table('users')->where('email', 'superadmin@example.com')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::dropIfExists('companies');
    }
};
