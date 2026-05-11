<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // e.g. Box 1, or Group A
            $table->string('type'); // box, normal, large
            $table->timestamps();
        });

        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->string('meter_number')->unique();
            $table->foreignId('group_id')->nullable()->constrained('meter_groups')->onDelete('set null');
            $table->string('status')->default('stock'); // stock, assigned, installed, issue, returned
            $table->foreignId('current_worker_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained('meters')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamp('assigned_at');
            $table->timestamps();
        });

        Schema::create('meter_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained('meters')->onDelete('cascade');
            $table->string('action'); // assigned, status_change, return_request, etc.
            $table->foreignId('user_id')->constrained('users');
            $table->text('remark')->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();
        });

        Schema::create('accounting_ledger', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // debit, credit
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('description');
            $table->unsignedBigInteger('related_id')->nullable(); // assignment_id or return_id
            $table->string('related_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ledger');
        Schema::dropIfExists('meter_logs');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('meters');
        Schema::dropIfExists('meter_groups');
    }
};
