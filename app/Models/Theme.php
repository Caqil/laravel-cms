<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'author_url',
        'theme_url',
        'screenshot',
        'file_path',
        'config',
        'is_active',
        'type',
        'customization_options',
    ];

    protected $casts = [
        'config' => 'array',
        'customization_options' => 'array',
        'is_active' => 'boolean',
    ];

    public function activate(): bool
    {
        static::where('type', $this->type)
              ->where('id', '!=', $this->id)
              ->update(['is_active' => false]);

        return $this->update(['is_active' => true]);
    }

    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    public function getStylesheetPath(): string
    {
        return public_path("themes/{$this->slug}/style.css");
    }

    public function getConfigPath(): string
    {
        return storage_path("app/themes/{$this->slug}/theme.json");
    }
}
