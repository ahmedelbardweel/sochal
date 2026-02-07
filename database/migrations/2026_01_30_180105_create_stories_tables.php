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
        // Stories Table
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 20); // image, video, text
            $table->string('media_url', 500)->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->text('text_content')->nullable();
            $table->string('background_color', 20)->nullable(); // For text stories
            $table->integer('duration')->default(5); // Display duration (seconds)
            
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at'); // 24 hours from creation
            
            $table->index('user_id');
            $table->index('expires_at');
            $table->index(['created_at', 'expires_at']);
        });

        // Story Views Table
        Schema::create('story_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['story_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_views');
        Schema::dropIfExists('stories');
    }
};
