<?php

namespace App\Jobs;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Services\SocialInsightService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Jobs\AnalyzeSocialAccountJob;

class FetchSocialPostsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(private readonly SocialAccount $account) {}

    public function handle(SocialInsightService $service): void
    {
        set_time_limit(120);

        $username = $this->account->username;
        $platform = $this->account->platform;

        // Fetch & update profile metadata
        $profile = match ($platform) {
            'instagram' => $service->fetchInstagramProfile($username),
            'tiktok'    => $service->fetchTikTokProfile($username),
            default     => [],
        };
        if (! empty($profile)) {
            $this->account->update($profile);
        }

        // Fetch posts
        $posts = match ($platform) {
            'instagram' => $service->fetchInstagramPosts($username),
            'tiktok'    => $service->fetchTikTokPosts($username),
            default     => [],
        };

        foreach ($posts as $post) {
            if (empty($post['platform_post_id'])) {
                continue;
            }

            SocialPost::updateOrCreate(
                [
                    'social_account_id' => $this->account->id,
                    'platform_post_id'  => $post['platform_post_id'],
                ],
                array_merge($post, ['social_account_id' => $this->account->id])
            );
        }

        $this->account->update(['last_synced_at' => now()]);

        Log::info("Fetched " . count($posts) . " posts for {$platform}/@{$username}");

        // Trigger AI analysis after posts are fetched (if DeepSeek is configured)
        if (count($posts) > 0 && config('services.deepseek.key')) {
            AnalyzeSocialAccountJob::dispatch($this->account);
        }
    }
}
