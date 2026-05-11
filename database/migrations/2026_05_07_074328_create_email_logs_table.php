<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to');                        // Recipient address(es), comma-separated
            $table->string('subject')->nullable();
            $table->string('mailer')->default('smtp');   // Which driver was used
            $table->string('from')->nullable();
            $table->string('type')->nullable();          // e.g. WelcomeUserMail, ResetPassword
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error')->nullable();           // Error message if failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
