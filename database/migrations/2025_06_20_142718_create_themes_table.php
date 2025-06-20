<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('version');
            $table->string('author')->nullable();
            $table->string('author_url')->nullable();
            $table->string('theme_url')->nullable();
            $table->string('screenshot')->nullable();
            $table->string('module_name')->nullable(); // Laravel Module name
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('type')->default('frontend'); // frontend, admin, both
            $table->json('customization_options')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('module_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
