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
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        // Rename created_by_admin_id to created_by in categories for consistency
        if (Schema::hasColumn('categories', 'created_by_admin_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->renameColumn('created_by_admin_id', 'created_by');
            });
        } elseif (!Schema::hasColumn('categories', 'created_by')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        if (Schema::hasColumn('categories', 'created_by')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->renameColumn('created_by', 'created_by_admin_id');
            });
        }
    }
};
