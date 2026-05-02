<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix double-encoded JSON data in verification_files columns.
     * Previous code used json_encode() manually, causing double encoding.
     * This migration decodes and re-encodes properly so Laravel Model Cast works.
     */
    public function up(): void
    {
        // Skip if columns don't exist yet (for fresh migrations)
        if (!Schema::hasColumn('user_tasks', 'verification_1_files')) {
            return;
        }

        // Fix verification_1_files - use Eloquent Model so Cast works properly
        $tasks = DB::table('user_tasks')
            ->whereNotNull('verification_1_files')
            ->where('verification_1_files', '!=', '')
            ->get();

        foreach ($tasks as $taskData) {
            // Get via Eloquent to use Model Cast
            $task = \App\Models\UserTask::find($taskData->id);

            if ($task && $task->verification_1_files) {
                // If it's still a string (double-encoded), decode and re-save
                if (is_string($task->verification_1_files)) {
                    $decoded = json_decode($task->verification_1_files, true);
                    if (is_array($decoded)) {
                        // Re-save using Eloquent - Model Cast will handle encoding properly
                        $task->verification_1_files = $decoded;
                        $task->saveQuietly(); // Save without triggering events
                    }
                }
            }
        }

        // Fix verification_2_files
        $tasks = DB::table('user_tasks')
            ->whereNotNull('verification_2_files')
            ->where('verification_2_files', '!=', '')
            ->get();

        foreach ($tasks as $taskData) {
            $task = \App\Models\UserTask::find($taskData->id);

            if ($task && $task->verification_2_files) {
                if (is_string($task->verification_2_files)) {
                    $decoded = json_decode($task->verification_2_files, true);
                    if (is_array($decoded)) {
                        $task->verification_2_files = $decoded;
                        $task->saveQuietly();
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - data fix is permanent
    }
};
