<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah estimated_amount di tasks untuk perkiraan nominal
        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('estimated_amount', 12, 2)->nullable()->after('priority_order');
        });

        // Tambah amount_change_reason di user_tasks untuk alasan perubahan nominal
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->text('amount_change_reason')->nullable()->after('payment_amount');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('estimated_amount');
        });

        Schema::table('user_tasks', function (Blueprint $table) {
            $table->dropColumn('amount_change_reason');
        });
    }
};
