<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->unique()->constrained('meters')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('users')->onDelete('cascade');
            $table->enum('mi_status', ['pending', 'complete', 'issue'])->default('pending');
            $table->text('remark')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('meter_activation_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_activation_id')->constrained('meter_activations')->onDelete('cascade');
            $table->string('image_path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_activation_images');
        Schema::dropIfExists('meter_activations');
    }
};
