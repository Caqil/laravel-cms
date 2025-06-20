<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('version');
            $table->string('author')->nullable();
            $table->string('author_url')->nullable();
            $table->string('plugin_url')->nullable();
            $table->string('module_name')->nullable(); // Laravel Module name
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('auto_activate')->default(false);
            $table->json('dependencies')->nullable();
            $table->string('minimum_php_version')->default('8.1');
            $table->string('minimum_laravel_version')->default('11.0');
            $table->string('type')->default('plugin'); // plugin, theme, widget
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('module_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugins');
    }
};
