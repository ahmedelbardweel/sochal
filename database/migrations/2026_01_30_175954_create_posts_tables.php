<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Posts Table
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('caption')->nullable();
            $table->string('privacy', 20)->default('public'); // public, followers, private
            $table->boolean('comments_disabled')->default(false);
            $table->boolean('hide_like_count')->default(false);
            $table->string('location')->nullable();
            
            // Counters
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('views_count')->default(0);
            
            // Moderation
            $table->string('status', 20)->default('active'); // active, hidden, deleted
            $table->text('moderation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('privacy');
            $table->index(['created_at', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        // Full Text Search Index for Posts
        DB::statement("CREATE INDEX idx_posts_caption_fts ON posts USING GIN(to_tsvector('english', coalesce(caption, '')))");

        // Post Media Table
        Schema::create('post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->string('type', 20); // image, video
            $table->string('url', 500);
            $table->string('thumbnail_url', 500)->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('duration')->nullable(); // seconds
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->text('alt_text')->nullable();
            
            $table->string('status', 20)->default('pending'); // pending, processing, processed, failed
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('post_id');
            $table->index('type');
        });

        // Likes Table
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['user_id', 'post_id']);
            $table->index('created_at');
        });

        // Comments Table
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->text('comment');
            
            $table->integer('likes_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['post_id', 'created_at']);
            $table->index('user_id');
        });

        // Full Text Search Index for Comments
        DB::statement("CREATE INDEX idx_comments_search ON comments USING GIN(to_tsvector('english', coalesce(comment, '')))");

        // Comment Likes Table
        Schema::create('comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['user_id', 'comment_id']);
        });

        // Bookmarks Table
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['user_id', 'post_id']);
            $table->index(['user_id', 'created_at']);
        });

        // Hashtags Table
        Schema::create('hashtags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->integer('posts_count')->default(0);
            $table->decimal('trending_score', 10, 2)->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index('name');
            $table->index(['trending_score', 'posts_count']);
        });

        // Post Hashtags Table
        Schema::create('post_hashtags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('hashtag_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['post_id', 'hashtag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_hashtags');
        Schema::dropIfExists('hashtags');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('comment_likes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('post_media');
        Schema::dropIfExists('posts');
    }
};
