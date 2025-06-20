<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic information
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('occupation')->nullable();
            $table->string('education')->nullable();
            
            // Social links
            $table->json('social_links')->nullable(); // Twitter, Instagram, LinkedIn, etc.
            
            // Interests and preferences
            $table->json('interests')->nullable();
            $table->string('language', 10)->default('en');
            $table->string('timezone')->nullable();
            
            // Profile visibility
            $table->enum('profile_visibility', ['public', 'friends', 'private'])->default('public');
            
            // Statistics (will be updated by events/jobs)
            $table->unsignedInteger('posts_count')->default(0);
            $table->unsignedInteger('followers_count')->default(0);
            $table->unsignedInteger('following_count')->default(0);
            
            // Metadata
            $table->json('custom_fields')->nullable(); // For plugin extensibility
            
            $table->timestamps();
        });
        
        // Add indexes safely
        $this->addIndexSafely('user_profiles', 'user_profiles_profile_visibility_index', ['profile_visibility']);
        $this->addIndexSafely('user_profiles', 'user_profiles_location_index', ['location']);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
    
    private function addIndexSafely(string $table, string $indexName, array $columns): void
    {
        try {
            $existingIndexes = collect(DB::select("SHOW INDEX FROM {$table}"))
                ->pluck('Key_name')
                ->toArray();
                
            if (!in_array($indexName, $existingIndexes)) {
                $columnList = implode(',', $columns);
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columnList})");
            }
        } catch (\Exception $e) {
            // Index creation failed, continue
        }
    }
};
