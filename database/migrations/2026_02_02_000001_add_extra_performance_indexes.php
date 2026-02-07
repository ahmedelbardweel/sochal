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
        // 1. Optimize Follows - Crucial for "Following" feed and "Is Following" status
        Schema::table('follows', function (Blueprint $table) {
            $table->index(['follower_id', 'following_id'], 'idx_follows_pair_scalability');
        });

        // 2. Optimize Likes - Crucial for "Is Liked" status in feeds
        Schema::table('likes', function (Blueprint $table) {
            $table->index(['user_id', 'post_id'], 'idx_likes_lookup_scalability');
        });

        // 3. Optimize Story Views & Expiration
        Schema::table('stories', function (Blueprint $table) {
            $table->index(['expires_at', 'user_id'], 'idx_stories_active_lookup');
        });

        Schema::table('story_views', function (Blueprint $table) {
            $table->index(['story_id', 'user_id'], 'idx_story_views_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('follows', function (Blueprint $table) {
            $table->dropIndex('idx_follows_pair_scalability');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex('idx_likes_lookup_scalability');
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropIndex('idx_stories_active_lookup');
        });

        Schema::table('story_views', function (Blueprint $table) {
            $table->dropIndex('idx_story_views_lookup');
        });
    }
};
