<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            // Ubah verification status dari varchar(255) ke TEXT untuk support file paths panjang
            $table->text('verification_1_status')->nullable()->change();
            $table->text('verification_2_status')->nullable()->change();

            // Tambah kolom baru untuk menyimpan file paths terpisah (lebih clean)
            $table->text('verification_1_files')->nullable()->after('verification_1_status');
            $table->text('verification_2_files')->nullable()->after('verification_2_status');
        });
    }

    public function down(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->string('verification_1_status')->nullable()->change();
            $table->string('verification_2_status')->nullable()->change();
            $table->dropColumn(['verification_1_files', 'verification_2_files']);
        });
    }
};
