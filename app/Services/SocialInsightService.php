<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialInsightService
{
    // APIs the user subscribed to on RapidAPI
    private const INSTAGRAM_HOST        = 'instagram120.p.rapidapi.com';
    private const INSTAGRAM_SEARCH_HOST = 'instagram-looter2.p.rapidapi.com';
    private const TIKTOK_HOST           = 'tiktok-api23.p.rapidapi.com';

    private const INDUSTRY_HASHTAGS = [
        'food'        => ['foodbusiness', 'kuliner', 'foodentrepreneur', 'fnb'],
        'fashion'     => ['fashion', 'ootd', 'fashionbrand', 'clothingbrand'],
        'beauty'      => ['beauty', 'skincare', 'beautycare', 'kecantikan'],
        'technology'  => ['tech', 'teknologi', 'startup', 'digitalbusiness'],
        'education'   => ['education', 'edtech', 'belajar', 'onlinecourse'],
        'health'      => ['health', 'wellness', 'healthylifestyle', 'medis'],
        'property'    => ['property', 'realestate', 'properti', 'rumah'],
        'retail'      => ['retail', 'onlineshop', 'jualan', 'marketplace'],
        'finance'     => ['finance', 'keuangan', 'investasi', 'bisnis'],
        'creative'    => ['creative', 'design', 'contentcreator', 'digitalmarketing'],
    ];

    // ─── Instagram (instagram120.p.rapidapi.com) ─────────────────────────────────
    // Uses POST requests with JSON body: {"username": "...", "maxId": ""}

    /**
     * Search Instagram accounts by keyword — returns up to $limit real accounts.
     * Used to pre-populate real candidates before passing to AI for selection.
     */
    public function searchInstagramAccountsByKeyword(string $keyword, int $limit = 10): array
    {
        try {
            $response = Http::withHeaders($this->instagramSearchHeaders())
                ->timeout(15)
                ->get('https://instagram-looter2.p.rapidapi.com/search', [
                    'query' => $keyword,
                ]);

            if (! $response->successful()) {
                Log::warning("Instagram keyword search failed [{$keyword}]: " . $response->status());
                return [];
            }

            $users    = $response->json('users', []);
            $accounts = [];

            foreach ($users as $item) {
                $user = $item['user'] ?? $item;
                if (! is_array($user) || empty($user['username'])) {
                    continue;
                }

                $accounts[] = [
                    'username'        => $user['username'],
                    'display_name'    => $user['full_name'] ?? $user['username'],
                    'profile_pic_url' => $user['profile_pic_url'] ?? null,
                    'is_verified'     => (bool) ($user['is_verified'] ?? false),
                    'ig_verified'     => true,
                ];

                if (count($accounts) >= $limit) {
                    break;
                }
            }

            return $accounts;
        } catch (ConnectionException $e) {
            Log::error("Instagram keyword search failed [{$keyword}]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search for an Instagram account by exact username.
     * Returns real profile data if exact match found, null otherwise.
     */
    public function searchInstagramAccount(string $username): ?array
    {
        $results = $this->searchInstagramAccountsByKeyword($username, 20);

        foreach ($results as $acc) {
            if (strtolower($acc['username']) === strtolower($username)) {
                return $acc;
            }
        }

        return null;
    }

    /**
     * Search for real Instagram accounts via Google Custom Search API.
     * Requires GOOGLE_API_KEY and GOOGLE_CSE_ID (configured to search instagram.com).
     * Returns accounts guaranteed to exist since Google indexes only real pages.
     */
    public function searchInstagramViaGoogle(string $keyword, int $limit = 10): array
    {
        $apiKey = config('services.google.api_key');
        $cseId  = config('services.google.cse_id');

        if (! $apiKey || ! $cseId) {
            return [];
        }

        // Skip pages that aren't user profiles
        $skipPaths = ['explore', 'p', 'reel', 'reels', 'tv', 'stories', 'accounts', 'about',
                      'legal', 'privacy', 'help', 'direct', 'ar', 'challenge', 'web'];

        try {
            $response = Http::timeout(10)->get('https://www.googleapis.com/customsearch/v1', [
                'key'        => $apiKey,
                'cx'         => $cseId,
                'q'          => $keyword,
                'num'        => 10,
                'siteSearch' => 'instagram.com',
            ]);

            if (! $response->successful()) {
                $errMsg = $response->json('error.message', $response->body());
                Log::warning("Google Custom Search failed [{$keyword}]: HTTP {$response->status()} — {$errMsg}");
                return [];
            }

            $items    = $response->json('items', []);
            $accounts = [];

            foreach ($items as $item) {
                $link = $item['link'] ?? '';
                // Match profile URLs: instagram.com/username or instagram.com/username/
                if (! preg_match('#instagram\.com/([a-zA-Z0-9._]+)/?(?:\?.*)?$#', $link, $m)) {
                    continue;
                }

                $username = $m[1];

                if (in_array(strtolower($username), $skipPaths, true)) {
                    continue;
                }

                // Extract display name from Google title: "Name (@handle) • Instagram..."
                $title       = $item['title'] ?? $username;
                $displayName = preg_match('/^(.+?)\s*\(/', $title, $tm) ? trim($tm[1]) : $username;

                $accounts[] = [
                    'username'        => $username,
                    'display_name'    => $displayName,
                    'profile_pic_url' => null,
                    'is_verified'     => false,
                    'ig_verified'     => true,
                ];

                if (count($accounts) >= $limit) {
                    break;
                }
            }

            return $accounts;
        } catch (ConnectionException $e) {
            Log::warning("Google Custom Search connection failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search for real Instagram accounts via Bing Web Search API.
     * Free tier: 1000 calls/month on Azure F0, no billing required.
     * Requires BING_API_KEY in .env.
     */
    public function searchInstagramViaBing(string $keyword, int $limit = 10): array
    {
        $apiKey = config('services.bing.api_key');

        if (! $apiKey) {
            return [];
        }

        $skipPaths = ['explore', 'p', 'reel', 'reels', 'tv', 'stories', 'accounts',
                      'about', 'legal', 'privacy', 'help', 'direct', 'ar', 'challenge'];

        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $apiKey,
                'Accept'                    => 'application/json',
            ])->timeout(10)->get('https://api.bing.microsoft.com/v7.0/search', [
                'q'           => "site:instagram.com/  {$keyword}",
                'count'       => 10,
                'mkt'         => 'id-ID',
                'responseFilter' => 'Webpages',
            ]);

            if (! $response->successful()) {
                $errMsg = $response->json('error.message', $response->body());
                Log::warning("Bing Search failed [{$keyword}]: HTTP {$response->status()} — {$errMsg}");
                return [];
            }

            $webResults = $response->json('webPages.value', []);
            $accounts   = [];

            foreach ($webResults as $item) {
                $url = $item['url'] ?? '';
                if (! preg_match('#instagram\.com/([a-zA-Z0-9._]+)/?(?:\?.*)?$#', $url, $m)) {
                    continue;
                }

                $username = $m[1];
                if (in_array(strtolower($username), $skipPaths, true)) {
                    continue;
                }

                // Extract display name from Bing snippet title
                $title       = $item['name'] ?? $username;
                $displayName = preg_match('/^(.+?)\s*[•·(@]/', $title, $tm) ? trim($tm[1]) : $username;

                $accounts[] = [
                    'username'        => $username,
                    'display_name'    => $displayName,
                    'profile_pic_url' => null,
                    'is_verified'     => false,
                    'ig_verified'     => true,
                ];

                if (count($accounts) >= $limit) {
                    break;
                }
            }

            return $accounts;
        } catch (ConnectionException $e) {
            Log::warning("Bing Search connection failed: " . $e->getMessage());
            return [];
        }
    }

    public function fetchInstagramProfile(string $username): array
    {
        try {
            // instagram120 profile endpoint: POST /api/instagram/profile → result.*
            $response = Http::withHeaders($this->instagramHeaders())
                ->timeout(15)
                ->post('https://instagram120.p.rapidapi.com/api/instagram/profile', [
                    'username' => $username,
                ]);

            if (! $response->successful()) {
                Log::warning("Instagram profile error for @{$username}: " . $response->status());
                return [];
            }

            $data = $response->json('result', []);

            return array_filter([
                'display_name'    => $data['full_name'] ?? $data['fullName'] ?? null,
                'profile_pic_url' => $data['profile_pic_url'] ?? $data['profilePicUrl'] ?? null,
                'follower_count'  => $data['follower_count'] ?? $data['followersCount'] ?? null,
            ], fn ($v) => $v !== null);
        } catch (ConnectionException $e) {
            Log::error("Instagram profile fetch failed for @{$username}: " . $e->getMessage());
            return [];
        }
    }

    public function fetchInstagramPosts(string $username): array
    {
        try {
            $response = Http::withHeaders($this->instagramHeaders())
                ->timeout(20)
                ->post('https://instagram120.p.rapidapi.com/api/instagram/posts', [
                    'username' => $username,
                    'maxId'    => '',
                ]);

            if (! $response->successful()) {
                Log::warning("Instagram posts error for @{$username}: " . $response->status() . ' body: ' . $response->body());
                return [];
            }

            // instagram120 returns: { result: { edges: [ { node: {...} } ] } }
            $edges = $response->json('result.edges', []);
            if (! is_array($edges) || empty($edges)) {
                Log::warning("Instagram posts empty for @{$username}. Keys: " . json_encode(array_keys($response->json() ?? [])));
                return [];
            }

            return $this->normalizeInstagramPosts($edges);
        } catch (ConnectionException $e) {
            Log::error("Instagram posts fetch failed for @{$username}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find similar accounts using TikTok video search by industry keywords.
     * Extracts unique content creators from search results.
     *
     * @return array<int, array{username: string, display_name: string, profile_pic_url: string|null, platform: string, profile_url: string}>
     */
    public function findSimilarAccounts(string $industry, array $extraHashtags = []): array
    {
        $keywords = array_merge(
            self::INDUSTRY_HASHTAGS[strtolower($industry)] ?? ['bisnis', 'brand'],
            $extraHashtags
        );

        // Use top 2 keywords joined for a richer search
        $keyword = implode(' ', array_slice($keywords, 0, 2));

        try {
            $response = Http::withHeaders($this->tiktokHeaders())
                ->timeout(15)
                ->get('https://tiktok-api23.p.rapidapi.com/api/search/video', [
                    'keyword' => $keyword,
                    'count'   => 20,
                    'cursor'  => 0,
                ]);

            if (! $response->successful()) {
                Log::warning("TikTok search failed for keyword [{$keyword}]: " . $response->status());
                return [];
            }

            $items    = $response->json('item_list', []);
            $seen     = [];
            $accounts = [];

            foreach ($items as $item) {
                $author   = $item['author'] ?? null;
                $uniqueId = $author['uniqueId'] ?? null;

                if (! $uniqueId || in_array($uniqueId, $seen, true)) {
                    continue;
                }

                $seen[]     = $uniqueId;
                $accounts[] = [
                    'username'        => $uniqueId,
                    'display_name'    => $author['nickname'] ?? $uniqueId,
                    'profile_pic_url' => $author['avatarThumb'] ?? $author['avatarMedium'] ?? null,
                    'follower_count'  => null,
                    'platform'        => 'tiktok',
                    'profile_url'     => "https://tiktok.com/@{$uniqueId}",
                ];

                if (count($accounts) >= 6) {
                    break;
                }
            }

            return $accounts;
        } catch (ConnectionException $e) {
            Log::error("Similar accounts fetch failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search TikTok for a real example post matching a keyword.
     * Returns url, thumbnail_url, author, and description of the top result.
     */
    public function searchExamplePost(string $keyword): ?array
    {
        try {
            $response = Http::withHeaders($this->tiktokHeaders())
                ->timeout(15)
                ->get('https://tiktok-api23.p.rapidapi.com/api/search/video', [
                    'keyword' => $keyword,
                    'count'   => 5,
                    'cursor'  => 0,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $items = $response->json('item_list', []);

            foreach ($items as $item) {
                $author   = $item['author'] ?? [];
                $uniqueId = $author['uniqueId'] ?? null;
                $videoId  = $item['id'] ?? null;

                if (! $uniqueId || ! $videoId) {
                    continue;
                }

                return [
                    'url'          => "https://tiktok.com/@{$uniqueId}/video/{$videoId}",
                    'platform'     => 'tiktok',
                    'thumbnail'    => $item['video']['cover'] ?? $item['video']['dynamicCover'] ?? null,
                    'author'       => $uniqueId,
                    'author_name'  => $author['nickname'] ?? $uniqueId,
                    'author_avatar'=> $author['avatarThumb'] ?? $author['avatarMedium'] ?? null,
                    'description'  => mb_substr($item['desc'] ?? '', 0, 100),
                    'like_count'   => (int) ($item['stats']['diggCount'] ?? 0),
                    'view_count'   => (int) ($item['stats']['playCount'] ?? 0),
                ];
            }

            return null;
        } catch (ConnectionException $e) {
            Log::error("TikTok example post search failed for [{$keyword}]: " . $e->getMessage());
            return null;
        }
    }

    // ─── TikTok (tiktok-api23.p.rapidapi.com) ───────────────────────────────────
    // Uses GET requests. Posts require secUid — fetched first via user info endpoint.

    public function fetchTikTokProfile(string $username): array
    {
        $info = $this->fetchTikTokUserInfo($username);
        if (empty($info)) {
            return [];
        }

        return array_filter([
            'display_name'    => $info['nickname'] ?? null,
            'profile_pic_url' => $info['avatarMedium'] ?? $info['avatar_medium'] ?? null,
            'follower_count'  => $info['followerCount'] ?? $info['stats']['followerCount'] ?? null,
        ], fn ($v) => $v !== null);
    }

    public function fetchTikTokPosts(string $username): array
    {
        $secUid = $this->resolveTikTokSecUid($username);
        if (! $secUid) {
            return [];
        }

        try {
            $response = Http::withHeaders($this->tiktokHeaders())
                ->timeout(20)
                ->get('https://tiktok-api23.p.rapidapi.com/api/user/posts', [
                    'secUid' => $secUid,
                    'count'  => 20,
                    'cursor' => 0,
                ]);

            if (! $response->successful()) {
                Log::warning("TikTok posts error for @{$username}: " . $response->status());
                return [];
            }

            $videos = $response->json('data.itemList', $response->json('itemList', []));

            return $this->normalizeTikTokPosts(is_array($videos) ? $videos : []);
        } catch (ConnectionException $e) {
            Log::error("TikTok posts fetch failed for @{$username}: " . $e->getMessage());
            return [];
        }
    }

    // ─── Private helpers ─────────────────────────────────────────────────────────

    /**
     * Verify a TikTok account exists and return real profile data.
     * Returns null if the account does not exist.
     */
    public function verifyTikTokAccount(string $username): ?array
    {
        $info = $this->fetchTikTokUserInfo($username);

        if (empty($info) || empty($info['secUid'] ?? $info['sec_uid'] ?? null)) {
            return null;
        }

        return [
            'display_name'    => $info['nickname'] ?? $username,
            'follower_count'  => $info['followerCount'] ?? $info['stats']['followerCount'] ?? null,
            'profile_pic_url' => $info['avatarMedium'] ?? $info['avatarThumb'] ?? null,
        ];
    }

    private function fetchTikTokUserInfo(string $username): array
    {
        try {
            $response = Http::withHeaders($this->tiktokHeaders())
                ->timeout(15)
                ->get('https://tiktok-api23.p.rapidapi.com/api/user/info', [
                    'username' => $username,
                ]);

            if (! $response->successful()) {
                return [];
            }

            // Response: { data: { user: {...}, stats: {...} } }
            $user  = $response->json('data.user', []);
            $stats = $response->json('data.stats', []);

            return array_merge($user ?? [], $stats ?? []);
        } catch (ConnectionException $e) {
            Log::error("TikTok user info fetch failed for @{$username}: " . $e->getMessage());
            return [];
        }
    }

    private function resolveTikTokSecUid(string $username): ?string
    {
        $info = $this->fetchTikTokUserInfo($username);

        return $info['secUid'] ?? $info['sec_uid'] ?? null;
    }

    private function normalizeInstagramPosts(array $edges): array
    {
        $normalized = [];

        foreach ($edges as $edge) {
            // instagram120 wraps each post in { node: {...} }
            $item = $edge['node'] ?? $edge;
            if (! is_array($item)) {
                continue;
            }

            // Caption can be null, string, or { text: "..." }
            $caption = $item['caption'] ?? '';
            if (is_array($caption)) {
                $caption = $caption['text'] ?? '';
            }
            $caption = (string) $caption;

            // Determine type: has video_versions → video, else image
            $isVideo    = ! empty($item['video_versions']);
            $isCarousel = ! empty($item['carousel_media']);
            $type       = $isVideo ? 'video' : ($isCarousel ? 'carousel' : 'image');

            // Thumbnail: first candidate from image_versions2
            $thumbnail = $item['image_versions2']['candidates'][0]['url']
                ?? $item['display_url']
                ?? $item['thumbnailSrc']
                ?? null;

            // Video URL: first video_version
            $videoUrl = $item['video_versions'][0]['url'] ?? null;

            $postId = (string) ($item['id'] ?? $item['pk'] ?? $item['code'] ?? '');

            if ($postId === '') {
                continue;
            }

            $normalized[] = [
                'platform_post_id' => $postId,
                'type'             => $type,
                'caption'          => $caption,
                'hashtags'         => $this->extractHashtags($caption),
                'thumbnail_url'    => $thumbnail,
                'video_url'        => $videoUrl,
                'view_count'       => (int) ($item['view_count'] ?? $item['play_count'] ?? 0),
                'like_count'       => (int) ($item['like_count'] ?? 0),
                'comment_count'    => (int) ($item['comment_count'] ?? 0),
                'posted_at'        => isset($item['taken_at']) ? date('Y-m-d H:i:s', $item['taken_at']) : null,
            ];
        }

        return $normalized;
    }

    private function normalizeTikTokPosts(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $desc = $item['desc'] ?? $item['title'] ?? '';

            $normalized[] = [
                'platform_post_id' => (string) ($item['id'] ?? ''),
                'type'             => 'video',
                'caption'          => $desc,
                'hashtags'         => $this->extractHashtags($desc),
                'thumbnail_url'    => $item['video']['cover'] ?? $item['cover'] ?? null,
                'video_url'        => $item['video']['playAddr'] ?? $item['play_url'] ?? null,
                'view_count'       => (int) ($item['stats']['playCount'] ?? $item['play'] ?? 0),
                'like_count'       => (int) ($item['stats']['diggCount'] ?? $item['digg'] ?? 0),
                'comment_count'    => (int) ($item['stats']['commentCount'] ?? $item['comment'] ?? 0),
                'posted_at'        => isset($item['createTime']) ? date('Y-m-d H:i:s', $item['createTime']) : null,
            ];
        }

        return $normalized;
    }

    private function extractHashtags(string $text): array
    {
        preg_match_all('/#(\w+)/u', $text, $matches);

        return array_unique(array_map('strtolower', $matches[1] ?? []));
    }

    private function instagramHeaders(): array
    {
        return [
            'Content-Type'    => 'application/json',
            'X-RapidAPI-Key'  => config('services.rapidapi.key'),
            'X-RapidAPI-Host' => self::INSTAGRAM_HOST,
        ];
    }

    private function instagramSearchHeaders(): array
    {
        return [
            'Content-Type'    => 'application/json',
            'X-RapidAPI-Key'  => config('services.rapidapi.key'),
            'X-RapidAPI-Host' => self::INSTAGRAM_SEARCH_HOST,
        ];
    }

    private function tiktokHeaders(): array
    {
        return [
            'X-RapidAPI-Key'  => config('services.rapidapi.key'),
            'X-RapidAPI-Host' => self::TIKTOK_HOST,
        ];
    }
}
