<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained('meters')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('users')->onDelete('cascade');
            $table->string('request_number')->nullable();
            $table->string('mobile_number')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        Schema::create('installation_field_configs', function (Blueprint $table) {
            $table->id();
            $table->string('field_name')->unique();
            $table->string('label');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installation_field_configs');
        Schema::dropIfExists('installation_details');
    }
};
