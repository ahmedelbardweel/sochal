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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 50); // like, comment, follow, mention, story_reply, etc.
            $table->string('notifiable_type', 100)->nullable(); // Post, Comment, User, etc.
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->jsonb('data'); // Additional notification data
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('read_at');
            $table->index('type');
            $table->index('data', 'idx_notifications_data', 'gin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
