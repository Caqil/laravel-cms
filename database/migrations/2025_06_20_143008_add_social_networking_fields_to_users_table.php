<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Basic profile fields
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'cover_photo')) {
                $table->string('cover_photo')->nullable()->after('avatar');
            }
            
            // Status and verification
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('cover_photo');
            }
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('users', 'account_status')) {
                $table->enum('account_status', ['active', 'suspended', 'deactivated', 'pending'])->default('active')->after('is_verified');
            }
            
            // Activity tracking
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('account_status');
            }
            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            }
            
            // Security
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('last_activity_at');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            }
            
            // Verification
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('phone');
            }
        });
        
        // Add indexes in a separate statement to avoid issues
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username') && !collect(Schema::getIndexes('users'))->pluck('name')->contains('users_username_index')) {
                $table->index(['username']);
            }
            if (Schema::hasColumn('users', 'is_active') && !collect(Schema::getIndexes('users'))->pluck('name')->contains('users_is_active_account_status_index')) {
                $table->index(['is_active', 'account_status']);
            }
            if (Schema::hasColumn('users', 'last_activity_at') && !collect(Schema::getIndexes('users'))->pluck('name')->contains('users_last_activity_at_index')) {
                $table->index(['last_activity_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['is_active', 'account_status']);
            $table->dropIndex(['last_activity_at']);
            
            $table->dropColumn([
                'username', 'avatar', 'cover_photo', 'is_active', 'is_verified', 
                'account_status', 'last_login_at', 'last_activity_at', 
                'two_factor_enabled', 'two_factor_secret', 'phone', 'phone_verified_at'
            ]);
        });
    }
};
