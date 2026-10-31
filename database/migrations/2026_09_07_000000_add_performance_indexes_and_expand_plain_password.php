<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expand plain_password on users table
        Schema::table('users', function (Blueprint $table) {
            $table->text('plain_password')->nullable()->change();
        });

        // 2. Encrypt any existing plaintext passwords
        $users = DB::table('users')->whereNotNull('plain_password')->get();
        foreach ($users as $user) {
            if (! empty($user->plain_password)) {
                try {
                    // Check if already encrypted
                    Crypt::decryptString($user->plain_password);
                } catch (DecryptException $e) {
                    // Not encrypted yet; encrypt it now
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['plain_password' => Crypt::encryptString($user->plain_password)]);
                }
            }
        }

        // 3. Performance indexes on meters
        Schema::table('meters', function (Blueprint $table) {
            $table->index('status', 'meters_status_index');
            $table->index('created_at', 'meters_created_at_index');
            $table->index(['current_worker_id', 'status'], 'meters_worker_status_index');
        });

        // 4. Performance indexes on assignments
        Schema::table('assignments', function (Blueprint $table) {
            $table->index('assigned_at', 'assignments_assigned_at_index');
        });

        // 5. Performance indexes on meter_activations
        Schema::table('meter_activations', function (Blueprint $table) {
            $table->index('mi_status', 'meter_activations_mi_status_index');
            $table->index('completed_at', 'meter_activations_completed_at_index');
        });

        // 6. Performance indexes on return_requests
        Schema::table('return_requests', function (Blueprint $table) {
            $table->index('status', 'return_requests_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropIndex('return_requests_status_index');
        });

        Schema::table('meter_activations', function (Blueprint $table) {
            $table->dropIndex('meter_activations_mi_status_index');
            $table->dropIndex('meter_activations_completed_at_index');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex('assignments_assigned_at_index');
        });

        Schema::table('meters', function (Blueprint $table) {
            $table->dropIndex('meters_status_index');
            $table->dropIndex('meters_created_at_index');
            $table->dropIndex('meters_worker_status_index');
        });

        // Decrypt passwords if reverting
        $users = DB::table('users')->whereNotNull('plain_password')->get();
        foreach ($users as $user) {
            if (! empty($user->plain_password)) {
                try {
                    $decrypted = Crypt::decryptString($user->plain_password);
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['plain_password' => $decrypted]);
                } catch (DecryptException $e) {
                    // Ignored if already decrypted or invalid
                }
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('plain_password')->nullable()->change();
        });
    }
};
