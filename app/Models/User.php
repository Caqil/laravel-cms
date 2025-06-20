<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'avatar',
        'cover_photo',
        'is_active',
        'is_verified',
        'account_status',
        'last_login_at',
        'last_activity_at',
        'two_factor_enabled',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        $casts = [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
        
        // Only add casts for columns that exist
        if (Schema::hasColumn('users', 'is_active')) {
            $casts['is_active'] = 'boolean';
        }
        if (Schema::hasColumn('users', 'is_verified')) {
            $casts['is_verified'] = 'boolean';
        }
        if (Schema::hasColumn('users', 'two_factor_enabled')) {
            $casts['two_factor_enabled'] = 'boolean';
        }
        if (Schema::hasColumn('users', 'last_login_at')) {
            $casts['last_login_at'] = 'datetime';
        }
        if (Schema::hasColumn('users', 'last_activity_at')) {
            $casts['last_activity_at'] = 'datetime';
        }
        
        return $casts;
    }

    // Relationships
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function privacySettings(): HasOne
    {
        return $this->hasOne(UserPrivacySetting::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    // Following relationships
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_relationships', 'follower_id', 'following_id')
                    ->withPivot(['status', 'type', 'created_at'])
                    ->wherePivot('type', 'follow')
                    ->wherePivot('status', 'accepted');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_relationships', 'following_id', 'follower_id')
                    ->withPivot(['status', 'type', 'created_at'])
                    ->wherePivot('type', 'follow')
                    ->wherePivot('status', 'accepted');
    }

    public function blocked(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_relationships', 'follower_id', 'following_id')
                    ->withPivot(['status', 'type', 'created_at'])
                    ->wherePivot('type', 'block');
    }

    public function muted(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_relationships', 'follower_id', 'following_id')
                    ->withPivot(['status', 'type', 'created_at'])
                    ->wherePivot('type', 'mute');
    }

    // Helper methods
    public function updateLastLogin(): void
    {
        if (Schema::hasColumn('users', 'last_login_at')) {
            $this->update(['last_login_at' => now()]);
        }
    }

    public function updateActivity(): void
    {
        if (Schema::hasColumn('users', 'last_activity_at')) {
            $this->update(['last_activity_at' => now()]);
        }
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function isFollowedBy(User $user): bool
    {
        return $this->followers()->where('follower_id', $user->id)->exists();
    }

    public function hasBlocked(User $user): bool
    {
        return $this->blocked()->where('following_id', $user->id)->exists();
    }

    public function hasMuted(User $user): bool
    {
        return $this->muted()->where('following_id', $user->id)->exists();
    }

    public function follow(User $user): void
    {
        if ($this->id !== $user->id && !$this->isFollowing($user)) {
            $this->following()->attach($user->id, [
                'type' => 'follow',
                'status' => 'accepted',
                'created_at' => now(),
            ]);
        }
    }

    public function unfollow(User $user): void
    {
        $this->following()->detach($user->id);
    }

    public function block(User $user): void
    {
        if ($this->id !== $user->id) {
            // Remove follow relationship if exists
            $this->unfollow($user);
            $user->unfollow($this);
            
            // Add block relationship
            $this->blocked()->syncWithoutDetaching([$user->id => [
                'type' => 'block',
                'status' => 'accepted',
                'created_at' => now(),
            ]]);
        }
    }

    public function unblock(User $user): void
    {
        $this->blocked()->detach($user->id);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->username ?: $this->name;
    }

    public function getProfileUrlAttribute(): string
    {
        return url("/profile/{$this->username}");
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset("storage/{$this->avatar}");
        }
        
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=3b82f6&color=fff";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('account_status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('username', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
