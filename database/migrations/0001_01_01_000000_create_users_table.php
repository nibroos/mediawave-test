<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->string('user_email')->unique();
            $table->string('user_username')->nullable()->unique();
            $table->string('user_address')->nullable();
            $table->string('user_phone', 50)->nullable();
            $table->string('user_token')->nullable();
            $table->uuid('user_uuid')->nullable()->unique();
            $table->timestamp('user_email_verified_at')->nullable();
            $table->string('user_password');
            $table->string('user_remember_token', 100)->nullable();
            $table->timestamp(User::CREATED_AT)->nullable();
            $table->timestamp(User::UPDATED_AT)->nullable();
            $table->timestamp(User::DELETED_AT)->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Schema::create('sessions', function (Blueprint $table) {
        //     $table->string('id')->primary();
        //     $table->foreignId('user_id')->nullable()->index();
        //     $table->string('ip_address', 45)->nullable();
        //     $table->text('user_agent')->nullable();
        //     $table->longText('payload');
        //     $table->integer('last_activity')->index();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        // Schema::dropIfExists('sessions');
    }
};
