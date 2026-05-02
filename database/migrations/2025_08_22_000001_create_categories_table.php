<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamp('expired_at')->default(now()->addDays(3));
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_admin_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['created_by_admin_id']);
            $table->index(['is_active', 'expired_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
