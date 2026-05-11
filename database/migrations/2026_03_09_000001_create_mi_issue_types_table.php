<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mi_issue_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default issue types
        DB::table('mi_issue_types')->insert([
            ['name' => 'QR Code Issue', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meter Damaged', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Location Mismatch', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Consumer Not Available', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('meter_activations', function (Blueprint $table) {
            $table->foreignId('issue_type_id')->nullable()->after('mi_status')->constrained('mi_issue_types')->nullOnDelete();
            $table->text('issue_detail')->nullable()->after('issue_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('meter_activations', function (Blueprint $table) {
            $table->dropForeign(['issue_type_id']);
            $table->dropColumn(['issue_type_id', 'issue_detail']);
        });
        Schema::dropIfExists('mi_issue_types');
    }
};
