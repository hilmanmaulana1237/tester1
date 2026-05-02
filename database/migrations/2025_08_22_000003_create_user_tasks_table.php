<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', [
                'taken',
                'pending_verification_1',
                'pending_verification_2',
                'completed',
                'cancelled',
                'failed',
                'banned'
            ])->default('taken');
            $table->timestamp('taken_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->integer('failed_count')->default(0);
            $table->string('verification_1_status')->nullable();
            $table->string('verification_2_status')->nullable();
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');
            $table->decimal('payment_amount', 12, 2)->nullable();
            $table->foreignId('payment_verified_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['task_id', 'status']);
            $table->index(['deadline_at']);

            // Composite unique constraint: only one active task per task_id
            // This allows multiple completed/cancelled tasks for the same task_id
            $table->unique(['task_id', 'user_id'], 'unique_user_task_per_task');
        });

        // Add partial unique constraint for active tasks only (SQLite doesn't support partial indexes)
        // For now, we'll handle this logic in the application layer
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tasks');
    }
};
