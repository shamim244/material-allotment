<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installation_field_configs', function (Blueprint $table) {
            $table->enum('install_type', ['shared', 'nsc', 'replacement'])->default('shared')->after('field_name');
        });

        // Mark existing fields with correct install_type
        DB::table('installation_field_configs')
            ->where('field_name', 'request_number')
            ->update(['install_type' => 'nsc']);

        // Seed replacement-specific fields
        $maxOrder = DB::table('installation_field_configs')->max('sort_order') ?? 0;

        DB::table('installation_field_configs')->insert([
            [
                'field_name' => 'ca_number',
                'label' => 'CA Number',
                'install_type' => 'replacement',
                'is_required' => true,
                'is_enabled' => true,
                'sort_order' => $maxOrder + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'field_name' => 'old_meter_number',
                'label' => 'Old Meter Number',
                'install_type' => 'replacement',
                'is_required' => true,
                'is_enabled' => true,
                'sort_order' => $maxOrder + 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('installation_field_configs')
            ->whereIn('field_name', ['ca_number', 'old_meter_number'])
            ->delete();

        Schema::table('installation_field_configs', function (Blueprint $table) {
            $table->dropColumn('install_type');
        });
    }
};
