<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('cancelled_at');
            $table->foreignId('verification_1_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('verification_1_status');
            $table->timestamp('verification_1_approved_at')->nullable()->after('verification_1_approved_by');
            $table->foreignId('verification_2_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('verification_2_status');
            $table->timestamp('verification_2_approved_at')->nullable()->after('verification_2_approved_by');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_verified_by_admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->dropForeign(['verification_1_approved_by']);
            $table->dropForeign(['verification_2_approved_by']);
            $table->dropColumn([
                'completed_at',
                'verification_1_approved_by',
                'verification_1_approved_at',
                'verification_2_approved_by',
                'verification_2_approved_at',
                'payment_verified_at'
            ]);
        });
    }
};
