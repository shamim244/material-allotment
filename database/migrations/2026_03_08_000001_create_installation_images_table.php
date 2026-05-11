<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installation_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_detail_id')->constrained('installation_details')->onDelete('cascade');
            $table->string('image_path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installation_images');
    }
};
