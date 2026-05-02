<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_thread_id')->constrained('support_threads')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->string('sender_role')->default('admin');
            $table->text('message');
            $table->json('meta')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['support_thread_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }
};
