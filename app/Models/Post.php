<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'content',
        'content_html',
        'type',
        'visibility',
        'status',
        'published_at',
        'scheduled_at',
        'likes_count',
        'comments_count',
        'shares_count',
        'views_count',
        'location',
        'metadata',
        'tags',
        'is_pinned',
        'comments_enabled',
        'is_reported',
        'moderated_at',
        'moderated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'moderated_at' => 'datetime',
        'metadata' => 'array',
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'comments_enabled' => 'boolean',
        'is_reported' => 'boolean',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'views_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeFriends($query)
    {
        return $query->where('visibility', 'friends');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNotReported($query)
    {
        return $query->where('is_reported', false);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('content', 'like', "%{$term}%");
    }

    // Helper methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
    }

    public function incrementComments(): void
    {
        $this->increment('comments_count');
    }

    public function decrementComments(): void
    {
        $this->decrement('comments_count');
    }

    public function incrementShares(): void
    {
        $this->increment('shares_count');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at <= now();
    }

    public function isVisible(): bool
    {
        return $this->isPublished() && !$this->is_reported;
    }

    public function getExcerptAttribute(): string
    {
        return substr(strip_tags($this->content), 0, 150) . '...';
    }

    public function getUrlAttribute(): string
    {
        return url("/posts/{$this->id}");
    }
}
