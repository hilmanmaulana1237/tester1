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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('ewallet_type', ['gopay', 'ovo', 'dana', 'shopeepay', 'linkaja'])->nullable()->after('email');
            $table->string('ewallet_number')->nullable()->after('ewallet_type');
            $table->string('ewallet_name')->nullable()->after('ewallet_number');

            // Add index for faster queries
            $table->index('ewallet_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['ewallet_type']);
            $table->dropColumn(['ewallet_type', 'ewallet_number', 'ewallet_name']);
        });
    }
};
