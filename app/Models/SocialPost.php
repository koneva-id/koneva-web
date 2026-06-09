<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends Model
{
    protected $fillable = [
        'social_account_id',
        'platform_post_id',
        'type',
        'caption',
        'hashtags',
        'thumbnail_url',
        'video_url',
        'view_count',
        'like_count',
        'comment_count',
        'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'hashtags'  => 'array',
            'posted_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id');
    }

    public function engagementRate(): float
    {
        $followers = $this->account?->follower_count ?? 1;
        if ($followers === 0) {
            return 0.0;
        }

        return round((($this->like_count + $this->comment_count) / $followers) * 100, 2);
    }
}
