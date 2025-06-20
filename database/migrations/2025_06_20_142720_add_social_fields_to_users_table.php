<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Get existing columns first
            $existingColumns = collect(DB::select('DESCRIBE users'))->pluck('Field')->toArray();
            
            // Add columns only if they don't exist
            if (!in_array('username', $existingColumns)) {
                $table->string('username')->unique()->nullable()->after('name');
            }
            if (!in_array('avatar', $existingColumns)) {
                $table->string('avatar')->nullable()->after('email_verified_at');
            }
            if (!in_array('cover_photo', $existingColumns)) {
                $table->string('cover_photo')->nullable();
            }
            if (!in_array('is_active', $existingColumns)) {
                $table->boolean('is_active')->default(true);
            }
            if (!in_array('is_verified', $existingColumns)) {
                $table->boolean('is_verified')->default(false);
            }
            if (!in_array('account_status', $existingColumns)) {
                $table->enum('account_status', ['active', 'suspended', 'deactivated', 'pending'])->default('active');
            }
            if (!in_array('last_login_at', $existingColumns)) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!in_array('last_activity_at', $existingColumns)) {
                $table->timestamp('last_activity_at')->nullable();
            }
            if (!in_array('two_factor_enabled', $existingColumns)) {
                $table->boolean('two_factor_enabled')->default(false);
            }
            if (!in_array('two_factor_secret', $existingColumns)) {
                $table->text('two_factor_secret')->nullable();
            }
            if (!in_array('phone', $existingColumns)) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!in_array('phone_verified_at', $existingColumns)) {
                $table->timestamp('phone_verified_at')->nullable();
            }
        });
        
        // Add indexes safely
        $this->addIndexSafely('users', 'users_username_index', ['username']);
        $this->addIndexSafely('users', 'users_is_active_account_status_index', ['is_active', 'account_status']);
        $this->addIndexSafely('users', 'users_last_activity_at_index', ['last_activity_at']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $existingColumns = collect(DB::select('DESCRIBE users'))->pluck('Field')->toArray();
            
            $columnsToRemove = [
                'username', 'avatar', 'cover_photo', 'is_active', 'is_verified',
                'account_status', 'last_login_at', 'last_activity_at',
                'two_factor_enabled', 'two_factor_secret', 'phone', 'phone_verified_at'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (in_array($column, $existingColumns)) {
                    $table->dropColumn($column);
                }
            }
        });
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
