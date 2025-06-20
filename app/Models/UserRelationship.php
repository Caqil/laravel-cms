<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'follower_id',
        'following_id',
        'status',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    public function scopeFollow($query)
    {
        return $query->where('type', 'follow');
    }

    public function scopeBlock($query)
    {
        return $query->where('type', 'block');
    }

    public function scopeMute($query)
    {
        return $query->where('type', 'mute');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
