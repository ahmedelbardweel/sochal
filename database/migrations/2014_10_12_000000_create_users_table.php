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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 30)->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 20)->nullable()->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('role', 20)->default('user'); // user, creator, moderator, admin
            $table->string('status', 20)->default('active'); // active, suspended, banned
            $table->timestamp('suspended_until')->nullable();

            // Profile info
            $table->string('display_name', 100)->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_url', 500)->nullable();
            $table->string('cover_url', 500)->nullable();
            $table->string('website')->nullable();
            $table->string('location', 100)->nullable();
            $table->date('birthday')->nullable();
            $table->string('gender', 20)->nullable();

            // Privacy settings
            $table->boolean('is_private')->default(false);
            $table->boolean('show_activity_status')->default(true);
            $table->string('allow_tags', 20)->default('everyone'); // everyone, following, none
            $table->string('allow_comments', 20)->default('everyone');
            $table->string('allow_messages', 20)->default('everyone');

            // Metadata
            $table->timestamp('last_active_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('role');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
