<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\File;

class Plugin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'author_url',
        'plugin_url',
        'file_path',
        'config',
        'is_active',
        'auto_activate',
        'dependencies',
        'minimum_php_version',
        'minimum_laravel_version',
    ];

    protected $casts = [
        'config' => 'array',
        'dependencies' => 'array',
        'is_active' => 'boolean',
        'auto_activate' => 'boolean',
    ];

    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    public function getMainFile(): string
    {
        return storage_path("app/plugins/{$this->slug}/{$this->file_path}");
    }

    public function hasRequiredDependencies(): bool
    {
        if (empty($this->dependencies)) {
            return true;
        }

        foreach ($this->dependencies as $dependency) {
            if (!static::where('slug', $dependency)->where('is_active', true)->exists()) {
                return false;
            }
        }

        return true;
    }
}
