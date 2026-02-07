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
        // Chats Table
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('direct'); // direct, group
            $table->string('name', 100)->nullable(); // For group chats
            $table->string('avatar_url', 500)->nullable(); // For group chats
            
            $table->unsignedBigInteger('last_message_id')->nullable(); // Foreign key added later to circular dependency
            $table->timestamp('last_message_at')->nullable();
            
            $table->timestamps();
            
            $table->index('last_message_at');
        });

        // Chat Members Table
        Schema::create('chat_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role', 20)->default('member'); // member, admin
            $table->boolean('is_muted')->default(false);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            
            $table->unique(['chat_id', 'user_id']);
        });

        // Messages Table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->string('type', 20)->default('text'); // text, image, video, voice, post_share
            $table->text('content')->nullable();
            $table->string('media_url', 500)->nullable();
            $table->foreignId('shared_post_id')->nullable()->constrained('posts')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('deleted_for_sender')->default(false);
            $table->boolean('deleted_for_everyone')->default(false);
            
            $table->index(['chat_id', 'created_at']);
        });

        // Message Reads Table
        Schema::create('message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('read_at')->useCurrent();
            
            $table->unique(['message_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_reads');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chat_members');
        Schema::dropIfExists('chats');
    }
};
