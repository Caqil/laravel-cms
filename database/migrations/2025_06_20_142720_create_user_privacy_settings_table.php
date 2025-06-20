<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_privacy_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Profile visibility
            $table->enum('profile_visibility', ['public', 'friends', 'private'])->default('public');
            $table->enum('email_visibility', ['public', 'friends', 'private'])->default('private');
            $table->enum('phone_visibility', ['public', 'friends', 'private'])->default('private');
            $table->enum('birth_date_visibility', ['public', 'friends', 'private'])->default('friends');
            
            // Social features
            $table->boolean('allow_friend_requests')->default(true);
            $table->boolean('allow_messages')->default(true);
            $table->boolean('allow_tags')->default(true);
            $table->boolean('show_online_status')->default(true);
            $table->boolean('show_last_seen')->default(true);
            
            // Content preferences
            $table->enum('default_post_visibility', ['public', 'friends', 'private'])->default('public');
            $table->boolean('allow_comments_on_posts')->default(true);
            $table->boolean('require_approval_for_tags')->default(false);
            
            // Notifications
            $table->json('notification_preferences')->nullable();
            $table->json('email_preferences')->nullable();
            
            // Search and discovery
            $table->boolean('searchable_by_email')->default(false);
            $table->boolean('searchable_by_phone')->default(false);
            $table->boolean('discoverable_by_search')->default(true);
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_privacy_settings');
    }
};
