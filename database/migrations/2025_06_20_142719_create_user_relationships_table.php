<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('accepted');
            $table->enum('type', ['follow', 'friend', 'block', 'mute'])->default('follow');
            $table->timestamp('created_at')->useCurrent();
            
            // Prevent duplicate relationships
            $table->unique(['follower_id', 'following_id', 'type']);
        });
        
        // Add indexes safely
        $this->addIndexSafely('user_relationships', 'user_relationships_follower_id_status_index', ['follower_id', 'status']);
        $this->addIndexSafely('user_relationships', 'user_relationships_following_id_status_index', ['following_id', 'status']);
        $this->addIndexSafely('user_relationships', 'user_relationships_type_status_index', ['type', 'status']);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_relationships');
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
