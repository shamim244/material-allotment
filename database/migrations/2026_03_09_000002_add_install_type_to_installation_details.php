<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installation_details', function (Blueprint $table) {
            $table->enum('install_type', ['nsc', 'replacement'])->default('nsc')->after('worker_id');
            $table->string('ca_number')->nullable()->after('request_number');
            $table->string('old_meter_number')->nullable()->after('ca_number');
        });
    }

    public function down(): void
    {
        Schema::table('installation_details', function (Blueprint $table) {
            $table->dropColumn(['install_type', 'ca_number', 'old_meter_number']);
        });
    }
};
