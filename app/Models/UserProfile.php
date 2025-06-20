<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'location',
        'website',
        'birth_date',
        'gender',
        'occupation',
        'education',
        'social_links',
        'interests',
        'language',
        'timezone',
        'profile_visibility',
        'posts_count',
        'followers_count',
        'following_count',
        'custom_fields',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'social_links' => 'array',
        'interests' => 'array',
        'custom_fields' => 'array',
        'posts_count' => 'integer',
        'followers_count' => 'integer',
        'following_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function incrementPostsCount(): void
    {
        $this->increment('posts_count');
    }

    public function decrementPostsCount(): void
    {
        $this->decrement('posts_count');
    }

    public function updateFollowersCount(): void
    {
        $this->update([
            'followers_count' => $this->user->followers()->count()
        ]);
    }

    public function updateFollowingCount(): void
    {
        $this->update([
            'following_count' => $this->user->following()->count()
        ]);
    }
}
