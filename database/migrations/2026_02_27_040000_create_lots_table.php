<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number')->unique();
            $table->string('description')->nullable();
            $table->date('import_date')->nullable();
            $table->timestamps();
        });

        Schema::table('meters', function (Blueprint $table) {
            $table->foreignId('lot_id')->nullable()->after('group_id')->constrained('lots')->onDelete('set null');
        });

        Schema::table('meter_groups', function (Blueprint $table) {
            $table->foreignId('lot_id')->nullable()->after('type')->constrained('lots')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->dropColumn('lot_id');
        });

        Schema::table('meter_groups', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->dropColumn('lot_id');
        });

        Schema::dropIfExists('lots');
    }
};
