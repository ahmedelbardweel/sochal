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
        // Reports Table
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->string('reported_type', 100); // Post, User, Comment, Story
            $table->unsignedBigInteger('reported_id');
            $table->string('reason', 100); // spam, harassment, violence, etc.
            $table->text('details')->nullable();
            $table->string('status', 20)->default('pending'); // pending, in_review, resolved, dismissed
            $table->string('priority', 20)->default('medium'); // low, medium, high
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('reporter_id');
            $table->index(['reported_type', 'reported_id']);
            $table->index('assigned_to');
        });

        // Moderation Actions Table
        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('report_id')->nullable()->constrained('reports')->onDelete('set null');
            $table->string('target_type', 100); // Post, User, Comment, etc.
            $table->unsignedBigInteger('target_id');
            $table->string('action', 50); // warn, hide, delete, suspend, ban
            $table->text('reason'); // Required explanation
            $table->text('internal_note')->nullable();
            $table->jsonb('metadata')->nullable(); // Additional data
            
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('moderator_id');
            $table->index(['target_type', 'target_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_actions');
        Schema::dropIfExists('reports');
    }
};
