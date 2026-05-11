<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only run on MySQL — SQLite has no ENUM type, so this is unnecessary there
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE meter_activations MODIFY COLUMN mi_status ENUM('pending', 'complete', 'issue', 'job_not_found') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE meter_activations MODIFY COLUMN mi_status ENUM('pending', 'complete', 'issue') DEFAULT 'pending'");
        }
    }
};
