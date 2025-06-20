<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Content
            $table->text('content')->nullable();
            $table->text('content_html')->nullable(); // Processed/formatted content
            $table->enum('type', ['text', 'image', 'video', 'link', 'poll', 'story'])->default('text');
            
            // Visibility and status
            $table->enum('visibility', ['public', 'friends', 'private', 'unlisted'])->default('public');
            $table->enum('status', ['published', 'draft', 'scheduled', 'deleted'])->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            
            // Engagement
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            
            // Location and metadata
            $table->string('location')->nullable();
            $table->json('metadata')->nullable(); // For plugin extensibility
            $table->json('tags')->nullable();
            
            // Moderation
            $table->boolean('is_pinned')->default(false);
            $table->boolean('comments_enabled')->default(true);
            $table->boolean('is_reported')->default(false);
            $table->timestamp('moderated_at')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users');
            
            $table->timestamps();
        });
        
        // Add indexes safely
        $this->addIndexSafely('posts', 'posts_user_id_status_published_at_index', ['user_id', 'status', 'published_at']);
        $this->addIndexSafely('posts', 'posts_visibility_status_published_at_index', ['visibility', 'status', 'published_at']);
        $this->addIndexSafely('posts', 'posts_type_status_index', ['type', 'status']);
        $this->addIndexSafely('posts', 'posts_is_pinned_published_at_index', ['is_pinned', 'published_at']);
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
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
