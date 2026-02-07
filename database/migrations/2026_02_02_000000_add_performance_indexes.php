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
        // 1. Indexing Posts for faster type-based routing and discovery
        Schema::table('posts', function (Blueprint $table) {
            $table->index('type', 'idx_posts_type_scalability');
            $table->index(['status', 'type', 'created_at'], 'idx_posts_feed_lookup');
        });

        // 2. Indexing Media for status monitoring and processing
        Schema::table('post_media', function (Blueprint $table) {
            $table->index(['status', 'type'], 'idx_media_processing_lookup');
        });

        // 3. Indexing Users for faster verified content filtering
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_verified', 'idx_users_verification_scalability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('idx_posts_type_scalability');
            $table->dropIndex('idx_posts_feed_lookup');
        });

        Schema::table('post_media', function (Blueprint $table) {
            $table->dropIndex('idx_media_processing_lookup');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_verification_scalability');
        });
    }
};
