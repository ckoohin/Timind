<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->string('avatar_url')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('last_login')->nullable();
            $table->json('preferences')->nullable();
            $table->string('timezone')->default('Asia/Ho_Chi_Minh');
            $table->enum('notification_settings', ['all', 'important', 'none'])->default('all');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'avatar_url', 'status', 'last_login', 'preferences', 'timezone', 'notification_settings']);
        });
    }
};
