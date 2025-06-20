<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mediable_type');
            $table->unsignedBigInteger('mediable_id');
            
            // File information
            $table->string('filename');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('disk')->default('public');
            
            // Media specific
            $table->enum('type', ['image', 'video', 'audio', 'document', 'other'])->default('other');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration')->nullable(); // For video/audio in seconds
            
            // Processing status
            $table->enum('status', ['uploading', 'processing', 'ready', 'failed'])->default('uploading');
            $table->json('variants')->nullable(); // Thumbnails, different sizes, etc.
            
            // Metadata
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->json('exif_data')->nullable();
            
            // Organization
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->timestamps();
        });
        
        // Add indexes safely after table creation
        $this->addIndexSafely('media', 'media_mediable_type_mediable_id_index', ['mediable_type', 'mediable_id']);
        $this->addIndexSafely('media', 'media_user_id_type_index', ['user_id', 'type']);
        $this->addIndexSafely('media', 'media_status_index', ['status']);
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
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
