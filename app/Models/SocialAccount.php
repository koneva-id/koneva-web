<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialAccount extends Model
{
    protected $fillable = [
        'client_id',
        'platform',
        'username',
        'display_name',
        'profile_pic_url',
        'follower_count',
        'last_synced_at',
        'is_business_account',
        'ai_analysis',
        'ai_analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_synced_at'       => 'datetime',
            'follower_count'       => 'integer',
            'is_business_account'  => 'boolean',
            'ai_analysis'          => 'array',
            'ai_analyzed_at'       => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }

    public function profileUrl(): string
    {
        return match ($this->platform) {
            'instagram' => "https://instagram.com/{$this->username}",
            'tiktok'    => "https://tiktok.com/@{$this->username}",
            default     => '#',
        };
    }
}
