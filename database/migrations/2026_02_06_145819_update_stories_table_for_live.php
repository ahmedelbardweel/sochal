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
        // Add published_at column if not exists
        if (!Schema::hasColumn('stories', 'published_at')) {
            Schema::table('stories', function (Blueprint $table) {
                // In PostgreSQL, we can use DB::statement for complex changes if needed
                $table->timestamp('published_at')->useCurrent();
            });
        }

        // Add source_live_id column
        Schema::table('stories', function (Blueprint $table) {
            $table->unsignedBigInteger('source_live_id')->nullable();
            $table->foreign('source_live_id')->references('id')->on('live_sessions')->onDelete('set null');
            
            // Allow media_url and thumbnail_url to be text (longer than string(500))
            $table->text('media_url')->change();
            $table->text('thumbnail_url')->change();
        });

        // Add index
        Schema::table('stories', function (Blueprint $table) {
            $table->index(['user_id', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropForeign(['source_live_id']);
            $table->dropColumn('source_live_id');
            $table->dropColumn('published_at');
            $table->string('media_url', 500)->change();
            $table->string('thumbnail_url', 500)->change();
        });
    }
};
