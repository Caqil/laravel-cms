<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Activity details
            $table->string('type'); // login, post_created, profile_updated, etc.
            $table->string('description')->nullable();
            $table->morphs('subject'); // The object the activity is about
            
            // Context
            $table->json('properties')->nullable(); // Additional data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
