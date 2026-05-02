<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('vcf_data')->nullable();
            $table->text('description');
            $table->string('whatsapp_group_link')->nullable();
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('easy');
            $table->timestamp('expired_at')->default(now()->addDays(7));
            $table->boolean('is_expired')->default(false);
            $table->integer('priority_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['category_id']);
            $table->index(['admin_id']);
            $table->index(['is_expired', 'expired_at']);
            $table->index(['difficulty_level']);
            $table->index(['priority_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
