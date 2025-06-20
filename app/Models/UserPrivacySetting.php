<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPrivacySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_visibility',
        'email_visibility',
        'phone_visibility',
        'birth_date_visibility',
        'allow_friend_requests',
        'allow_messages',
        'allow_tags',
        'show_online_status',
        'show_last_seen',
        'default_post_visibility',
        'allow_comments_on_posts',
        'require_approval_for_tags',
        'notification_preferences',
        'email_preferences',
        'searchable_by_email',
        'searchable_by_phone',
        'discoverable_by_search',
    ];

    protected $casts = [
        'allow_friend_requests' => 'boolean',
        'allow_messages' => 'boolean',
        'allow_tags' => 'boolean',
        'show_online_status' => 'boolean',
        'show_last_seen' => 'boolean',
        'allow_comments_on_posts' => 'boolean',
        'require_approval_for_tags' => 'boolean',
        'notification_preferences' => 'array',
        'email_preferences' => 'array',
        'searchable_by_email' => 'boolean',
        'searchable_by_phone' => 'boolean',
        'discoverable_by_search' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function defaultSettings(): array
    {
        return [
            'profile_visibility' => 'public',
            'email_visibility' => 'private',
            'phone_visibility' => 'private',
            'birth_date_visibility' => 'friends',
            'allow_friend_requests' => true,
            'allow_messages' => true,
            'allow_tags' => true,
            'show_online_status' => true,
            'show_last_seen' => true,
            'default_post_visibility' => 'public',
            'allow_comments_on_posts' => true,
            'require_approval_for_tags' => false,
            'searchable_by_email' => false,
            'searchable_by_phone' => false,
            'discoverable_by_search' => true,
            'notification_preferences' => [
                'email_on_follow' => true,
                'email_on_comment' => true,
                'email_on_like' => false,
                'push_on_message' => true,
            ],
            'email_preferences' => [
                'weekly_digest' => true,
                'marketing_emails' => false,
            ],
        ];
    }
}
