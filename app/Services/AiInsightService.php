<?php

namespace App\Services;

use App\Models\AiRecommendation;
use App\Models\Client;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Services\BrandDna\BrandDnaEngine;
use App\Support\AiProgressLogger;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiInsightService
{
    private const DEEPSEEK_URL = 'https://api.deepseek.com/v1/chat/completions';
    private const CACHE_HOURS  = 24;

    public function getRecommendation(Client $client): ?array
    {
        $cached = AiRecommendation::where('client_id', $client->id)->first();

        // Always return cached data immediately — never generate synchronously on GET.
        // Refresh is handled by GenerateAiRecommendationJob dispatched after response.
        return $cached?->data;
    }

    public function isRecommendationExpired(Client $client): bool
    {
        $cached = AiRecommendation::where('client_id', $client->id)->first();
        return ! $cached || $cached->isExpired();
    }

    public function refreshRecommendation(Client $client, ?AiProgressLogger $logger = null): ?array
    {
        $data = $this->generate($client, $logger);

        if ($data === null) {
            return null;
        }

        AiRecommendation::updateOrCreate(
            ['client_id' => $client->id],
            ['data' => $data, 'expires_at' => now()->addHours(self::CACHE_HOURS)]
        );

        return $data;
    }

    // ─── Post Analysis ──────────────────────────────────────────────────────────

    public function analyzeAccount(SocialAccount $account, ?AiProgressLogger $logger = null): ?array
    {
        $apiKey = config('services.deepseek.key');

        if (! $apiKey) {
            $logger?->error('DEEPSEEK_API_KEY belum dikonfigurasi.');
            return null;
        }

        $logger?->thinking("Mengambil data postingan dari database...");

        $posts = SocialPost::where('social_account_id', $account->id)
            ->orderByDesc('like_count')
            ->limit(20)
            ->get();

        if ($posts->isEmpty()) {
            $logger?->error('Belum ada postingan yang diambil untuk dianalisis.');
            return null;
        }

        $platform    = ucfirst($account->platform);
        $username    = $account->username;
        $postCount   = $posts->count();
        $totalLikes  = $posts->sum('like_count');
        $totalCmt    = $posts->sum('comment_count');
        $avgLikes    = round($totalLikes / $postCount);
        $avgComments = round($totalCmt / $postCount);

        $logger?->log("Ditemukan {$postCount} postingan · rata-rata {$avgLikes} likes · {$avgComments} komentar");
        $logger?->thinking("Mempersiapkan data untuk analisis AI mendalam...");

        $postsSummary = $posts->map(fn ($p) => [
            'caption'   => mb_substr($p->caption ?? '', 0, 120),
            'hashtags'  => array_slice($p->hashtags ?? [], 0, 5),
            'type'      => $p->type,
            'likes'     => $p->like_count,
            'comments'  => $p->comment_count,
            'views'     => $p->view_count,
        ])->toArray();

        $prompt = <<<PROMPT
Kamu adalah analis strategi konten media sosial profesional. Analisis performa akun {$platform} berikut:

Username: @{$username}
Platform: {$platform}
Total postingan dianalisis: {$postCount}
Rata-rata likes: {$avgLikes}
Rata-rata komentar: {$avgComments}

Data postingan (diurutkan dari yang paling banyak likes):
{$this->jsonEncode($postsSummary)}

Berikan analisis mendalam dalam format JSON berikut (tanpa markdown, tanpa teks di luar JSON):
{
  "overall_score": 75,
  "grade": "B+",
  "summary": "Ringkasan singkat performa akun secara keseluruhan (2-3 kalimat)",
  "metrics": [
    {
      "name": "Engagement Rate",
      "score": 70,
      "label": "Cukup Baik",
      "explanation": "Penjelasan spesifik berdasarkan data likes & komentar",
      "benchmark": "Rata-rata industri untuk platform ini adalah X%"
    },
    {
      "name": "Konsistensi Konten",
      "score": 65,
      "label": "Perlu Ditingkatkan",
      "explanation": "Analisis pola konten, variasi format, tema",
      "benchmark": "Konten terbaik biasanya memiliki tema yang konsisten"
    },
    {
      "name": "Strategi Hashtag",
      "score": 60,
      "label": "Kurang Optimal",
      "explanation": "Analisis penggunaan hashtag dari data postingan",
      "benchmark": "Disarankan 5-10 hashtag campuran besar dan niche"
    },
    {
      "name": "Kualitas Caption",
      "score": 80,
      "label": "Baik",
      "explanation": "Analisis gaya penulisan caption, hook, call-to-action",
      "benchmark": "Caption dengan pertanyaan atau CTA meningkatkan komentar 3x"
    }
  ],
  "strengths": [
    "Kelebihan spesifik berdasarkan data (3-4 poin)",
    "..."
  ],
  "improvements": [
    {
      "priority": "Tinggi",
      "action": "Tindakan konkret yang bisa langsung dilakukan",
      "reason": "Alasan berdasarkan data postingan"
    },
    {
      "priority": "Sedang",
      "action": "...",
      "reason": "..."
    },
    {
      "priority": "Sedang",
      "action": "...",
      "reason": "..."
    }
  ],
  "best_post_insight": "Analisis mengapa postingan teratas berhasil dan apa yang bisa direplikasi",
  "content_type_breakdown": {
    "dominant_type": "video/foto/carousel",
    "recommendation": "Saran tipe konten berdasarkan performa"
  }
}

Semua teks dalam Bahasa Indonesia. Skor 0-100. Label: Luar Biasa (90+), Sangat Baik (80-89), Baik (70-79), Cukup Baik (60-69), Perlu Ditingkatkan (50-59), Kurang Optimal (<50).
PROMPT;

        $logger?->thinking("Mengirim data ke DeepSeek AI untuk analisis mendalam...");

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(60)->post(self::DEEPSEEK_URL, [
                'model'       => 'deepseek-chat',
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Kamu adalah analis performa media sosial profesional. Selalu jawab dalam format JSON valid tanpa markdown. Analisis berdasarkan data nyata yang diberikan.',
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens'  => 2000,
                'temperature' => 0.5,
            ]);

            if (! $response->successful()) {
                $logger?->error('DeepSeek API error: HTTP ' . $response->status());
                return null;
            }

            $logger?->thinking("Memproses dan memvalidasi respons AI...");

            $text = $response->json('choices.0.message.content', '');
            $data = $this->parseJson($text);

            if ($data === null) {
                $logger?->error('Format respons AI tidak valid.');
                return null;
            }

            $score = $data['overall_score'] ?? '?';
            $grade = $data['grade'] ?? '';
            $account->update([
                'ai_analysis'    => $data,
                'ai_analyzed_at' => now(),
            ]);

            $logger?->success("Analisis selesai! Skor @{$username}: {$score}/100 ({$grade})");

            return $data;
        } catch (ConnectionException $e) {
            $logger?->error('Koneksi ke DeepSeek gagal: ' . $e->getMessage());
            Log::error('DeepSeek account analysis failed: ' . $e->getMessage());
            return null;
        }
    }

    // ─── Private ────────────────────────────────────────────────────────────────

    private function generate(Client $client, ?AiProgressLogger $logger = null): ?array
    {
        $apiKey = config('services.deepseek.key');

        if (! $apiKey) {
            $logger?->error('DEEPSEEK_API_KEY belum dikonfigurasi di .env');
            return null;
        }

        $logger?->log('Membaca data profil bisnis...');

        $answers  = $client->personalization_answers ?? [];
        $company  = $answers['company_name'] ?? $client->company_name ?? 'bisnis';
        $industry = $answers['industry'] ?? $client->industry ?? '';

        // ── Brand DNA Engine (deterministic, no AI) ──────────────────
        $logger?->log('Menjalankan Koneva Brand DNA Engine...');
        $dnaEngine = App::make(BrandDnaEngine::class);
        $dnaResult = $dnaEngine->analyze($answers);
        $primaryDna = $dnaResult['primary_dna'];
        $dnaScore   = $dnaResult['scores'][$primaryDna] ?? 0;
        $logger?->success("Brand DNA teridentifikasi: {$dnaResult['primary_info']['name']} (Skor: {$dnaScore})");

        // Pre-fetch real accounts from social APIs so AI can only SELECT, not invent
        $realAccounts = $this->fetchRealAccountCandidates($client, $logger);

        $prompt = $this->buildPrompt($client, $logger, $realAccounts, $dnaResult);

        if ($prompt === null) {
            $logger?->error('Data personalisasi tidak lengkap. Isi ulang form personalisasi.');
            return null;
        }

        $logger?->thinking("Mengirim data \"{$company}\" (industri: {$industry}) ke DeepSeek AI...");

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(60)->post(self::DEEPSEEK_URL, [
                'model'       => 'deepseek-chat',
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Kamu adalah konsultan strategi media sosial yang berpengalaman untuk bisnis Indonesia. Selalu jawab dalam format JSON valid yang diminta tanpa markdown code block. Jangan tambahkan teks apapun di luar JSON.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens'  => 2500,
                'temperature' => 0.7,
            ]);

            if (! $response->successful()) {
                $logger?->error('DeepSeek API error: HTTP ' . $response->status());
                Log::warning('DeepSeek API error: ' . $response->status() . ' ' . $response->body());
                return null;
            }

            $logger?->success('DeepSeek AI selesai menganalisis bisnis Anda');
            $logger?->log('Memproses dan memvalidasi hasil rekomendasi...');

            $text = $response->json('choices.0.message.content', '');
            $data = $this->parseJson($text);

            if ($data === null) {
                $logger?->error('Format respons AI tidak valid, coba lagi.');
                return null;
            }

            // Merge Brand DNA result (engine output, not AI output)
            $data['brand_dna'] = $dnaResult;

            // Instagram: ALWAYS use pre-fetched real accounts — never trust AI-generated usernames
            $igReal = $realAccounts['instagram'] ?? [];
            if (! empty($igReal)) {
                $data['instagram_accounts'] = array_map(fn ($acc) => [
                    'username'        => $acc['username'],
                    'display_name'    => $acc['display_name'] ?? $acc['username'],
                    'profile_pic_url' => $acc['profile_pic_url'] ?? null,
                    'why_follow'      => 'Akun Instagram nyata dengan konten relevan di industri ini',
                    'reference_posts' => [],
                    'ig_verified'     => true,
                    'is_verified'     => $acc['is_verified'] ?? false,
                ], array_slice($igReal, 0, 3));
                $logger?->success(count($data['instagram_accounts']) . ' akun Instagram nyata dari search digunakan');
            } else {
                $data['instagram_accounts'] = [];
                $logger?->log('Tidak ada akun Instagram ditemukan via search — tampilkan kosong');
            }

            // TikTok: will be overridden below from video example authors

            // Enrich each content idea with a real TikTok example post
            if (! empty($data['content_ideas']) && config('services.rapidapi.key')) {
                $logger?->log('Mencari video referensi TikTok untuk setiap ide konten...');
                $data['content_ideas'] = $this->enrichWithExamplePosts($data['content_ideas'], $logger);

                // Replace AI-generated TikTok accounts with the real authors from those videos
                $ttFromVideos = $this->extractTikTokAccountsFromIdeas($data['content_ideas']);
                if (! empty($ttFromVideos)) {
                    $data['tiktok_accounts'] = $ttFromVideos;
                    $logger?->success('Top TikTok: ' . count($ttFromVideos) . ' akun diambil dari video konten nyata');
                }
            }

            $logger?->success('Semua rekomendasi berhasil dibuat! ✓');

            return $data;
        } catch (ConnectionException $e) {
            $logger?->error('Koneksi ke DeepSeek gagal: ' . $e->getMessage());
            Log::error('DeepSeek connection failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Match AI-selected usernames back to the pre-fetched real candidates
     * to attach real profile data (profile_pic_url, verified flag, etc.).
     * Falls back gracefully if AI deviated from the provided list.
     */
    private function enrichFromRealCandidates(
        array $aiAccounts,
        array $realCandidates,
        string $platform,
        ?AiProgressLogger $logger = null
    ): array {
        // Build lookup map username → real data
        $map = [];
        foreach ($realCandidates as $real) {
            $map[strtolower($real['username'] ?? '')] = $real;
        }

        $enriched = [];
        foreach ($aiAccounts as $acc) {
            $username = strtolower($acc['username'] ?? '');
            $real     = $map[$username] ?? null;

            if ($real) {
                if ($platform === 'ig') {
                    $acc['display_name']    = $real['display_name'];
                    $acc['profile_pic_url'] = $real['profile_pic_url'];
                    $acc['is_verified']     = $real['is_verified'];
                    $acc['ig_verified']     = true;
                } else {
                    $acc['display_name'] = $real['display_name'];
                    $acc['verified']     = true;
                }
                $logger?->success("  ✓ @{$acc['username']} — data real terlampir");
            } else {
                // AI used a username outside the provided list — mark unverified
                $acc[$platform === 'ig' ? 'ig_verified' : 'verified'] = false;
                $logger?->log("  ? @{$acc['username']} — tidak ada di daftar kandidat, tandai saran AI");
                Log::warning("AI deviated from real account list: @{$acc['username']} ({$platform})");
            }

            $enriched[] = $acc;
        }

        return $enriched;
    }

    private function fetchRealAccountCandidates(Client $client, ?AiProgressLogger $logger = null): array
    {
        if (! config('services.rapidapi.key')) {
            return [];
        }

        $answers  = $client->personalization_answers ?? [];
        $industry = $answers['industry'] ?? $client->industry ?? '';

        if (! $industry) {
            return [];
        }

        $social = App::make(SocialInsightService::class);

        // TikTok accounts are derived from real content idea videos after enrichWithExamplePosts(),
        // so no pre-fetch needed here. Pass empty array — AI prompt won't get a TikTok constraint.
        $ttAccounts = [];

        // Instagram: Bing → Google → Looter2 fallback chain
        $logger?->log('Mencari akun Instagram nyata di industri ini...');
        $igAccounts    = [];
        $igAccountsMap = [];

        // 1. Bing Web Search (free 1000/month, no billing needed)
        if (empty($igAccounts) && config('services.bing.api_key')) {
            $logger?->log('  → Menggunakan Bing Search...');
            foreach (["{$industry} indonesia", $industry] as $kw) {
                foreach ($social->searchInstagramViaBing($kw, 6) as $acc) {
                    $igAccountsMap[$acc['username']] = $acc;
                }
            }
            $igAccounts = array_values(array_slice($igAccountsMap, 0, 12));
            if (! empty($igAccounts)) {
                $logger?->log('  → Bing: ditemukan ' . count($igAccounts) . ' akun Instagram nyata');
            }
        }

        // 2. Google Custom Search fallback
        if (empty($igAccounts) && config('services.google.api_key') && config('services.google.cse_id')) {
            $logger?->log('  → Menggunakan Google Search...');
            foreach (["{$industry} indonesia", $industry] as $kw) {
                foreach ($social->searchInstagramViaGoogle($kw, 6) as $acc) {
                    $igAccountsMap[$acc['username']] = $acc;
                }
            }
            $igAccounts = array_values(array_slice($igAccountsMap, 0, 12));
            if (! empty($igAccounts)) {
                $logger?->log('  → Google: ditemukan ' . count($igAccounts) . ' akun Instagram nyata');
            }
        }

        // 3. Instagram Looter2 fallback
        if (empty($igAccounts)) {
            $logger?->log('  → Fallback ke Instagram Looter2...');
            foreach (["{$industry} indonesia", $industry] as $kw) {
                foreach ($social->searchInstagramAccountsByKeyword($kw, 12) as $acc) {
                    $igAccountsMap[$acc['username']] = $acc;
                    if (count($igAccountsMap) >= 12) break 2;
                }
            }
            $igAccounts = array_values($igAccountsMap);
            $logger?->log('  → Looter2: ditemukan ' . count($igAccounts) . ' akun Instagram');
        }

        return [
            'instagram' => $igAccounts,
            'tiktok'    => $ttAccounts,
        ];
    }

    private function verifyInstagramAccounts(array $accounts, ?AiProgressLogger $logger = null): array
    {
        $social   = App::make(SocialInsightService::class);
        $verified = [];
        $total    = count($accounts);

        $logger?->log("Memverifikasi {$total} akun Instagram...");

        foreach ($accounts as $acc) {
            $username = $acc['username'] ?? '';
            if (! $username) {
                continue;
            }

            $profile = $social->searchInstagramAccount($username);

            if ($profile === null) {
                Log::info("Instagram search: @{$username} not found, keeping as unverified");
                $logger?->log("  ? @{$username} — tidak ditemukan via search, disimpan sebagai saran AI");
                // Keep the account but mark as unverified rather than dropping it
                $acc['ig_verified'] = false;
                $verified[] = $acc;
                continue;
            }

            // Replace AI-generated data with real data from the platform
            $acc['display_name']    = $profile['display_name'];
            $acc['profile_pic_url'] = $profile['profile_pic_url'];
            $acc['is_verified']     = $profile['is_verified'];
            $acc['ig_verified']     = true;

            $badge = $profile['is_verified'] ? ' ✓ centang biru' : '';
            $logger?->success("  ✓ @{$username} — ditemukan{$badge}");
            Log::info("Instagram search: @{$username} verified" . ($profile['is_verified'] ? ' (blue tick)' : ''));

            $verified[] = $acc;
        }

        return $verified;
    }

    private function verifyTikTokAccounts(array $accounts, ?AiProgressLogger $logger = null): array
    {
        $social   = App::make(SocialInsightService::class);
        $verified = [];
        $total    = count($accounts);

        $logger?->log("Memverifikasi {$total} akun TikTok...");

        foreach ($accounts as $acc) {
            $username = $acc['username'] ?? '';
            if (! $username) {
                continue;
            }

            $profile = $social->verifyTikTokAccount($username);

            if ($profile === null) {
                Log::info("TikTok verify: @{$username} not found, keeping as unverified");
                $logger?->log("  ? @{$username} — tidak ditemukan via API, disimpan sebagai saran AI");
                $acc['verified'] = false;
                $verified[] = $acc;
                continue;
            }

            if (! empty($profile['display_name'])) {
                $acc['display_name'] = $profile['display_name'];
            }
            $acc['verified'] = true;

            $logger?->success("  ✓ @{$username} — terverifikasi");
            Log::info("TikTok verify: @{$username} verified");
            $verified[] = $acc;
        }

        return $verified;
    }

    private function enrichWithExamplePosts(array $ideas, ?AiProgressLogger $logger = null): array
    {
        $social      = App::make(SocialInsightService::class);
        $total       = count($ideas);
        $videoFormats = ['video', 'reel', 'reels', 'short'];

        foreach ($ideas as $i => &$idea) {
            $format  = strtolower($idea['format'] ?? '');
            $keyword = $idea['search_keyword'] ?? $idea['title'] ?? '';
            $num     = $i + 1;

            // Only search TikTok for video/reel formats — carousel & foto are images,
            // so attaching a TikTok video would mislead the user about the content type.
            if (! in_array($format, $videoFormats, true)) {
                $logger?->log("Ide konten #{$num} ({$format}) — lewati pencarian video");
                continue;
            }

            if (! $keyword) {
                continue;
            }

            $logger?->log("Mencari video TikTok ({$num}/{$total}): \"{$keyword}\"");

            $post = $social->searchExamplePost($keyword);

            if ($post) {
                $idea['example_post'] = $post;
                $logger?->success("  → Ditemukan: @{$post['author']}");
            } else {
                $logger?->log("  → Tidak ditemukan untuk keyword ini");
            }
        }
        unset($idea);

        return $ideas;
    }

    /**
     * Extract unique TikTok creators from content idea example posts.
     * These are guaranteed-real accounts since they come from actual TikTok video search results.
     */
    private function extractTikTokAccountsFromIdeas(array $ideas): array
    {
        $seen     = [];
        $accounts = [];

        foreach ($ideas as $idea) {
            $post = $idea['example_post'] ?? null;
            if (! $post || empty($post['author'])) {
                continue;
            }

            $username = $post['author'];
            if (isset($seen[$username])) {
                continue;
            }

            $seen[$username] = true;
            $accounts[] = [
                'username'        => $username,
                'display_name'    => $post['author_name'] ?? $username,
                'profile_pic_url' => $post['author_avatar'] ?? null,
                'why_follow'      => 'Kreator konten nyata yang membuat video relevan dengan industri Anda',
                'reference_posts' => [],
                'tt_verified'     => true,
            ];

            if (count($accounts) >= 3) {
                break;
            }
        }

        return $accounts;
    }

    private function buildPrompt(Client $client, ?AiProgressLogger $logger = null, array $realAccounts = [], array $dnaResult = []): ?string
    {
        $answers = $client->personalization_answers ?? [];

        if (empty($answers)) {
            return null;
        }

        $company        = $answers['company_name'] ?? $client->company_name ?? 'bisnis ini';
        $industry       = $answers['industry'] ?? $client->industry ?? 'umum';
        $description    = $answers['description'] ?? '';
        $targetAudience = $answers['target_audience'] ?? '';
        $goals          = implode(', ', (array) ($answers['goals'] ?? []));
        $platforms      = implode(', ', (array) ($answers['platforms'] ?? []));
        $contentTone    = $answers['content_tone'] ?? '';
        $competitors    = $answers['competitors'] ?? '';
        $uniqueValue    = $answers['unique_value'] ?? '';
        $testimonials   = $answers['customer_testimonials'] ?? '';

        // Produk
        $productFeatured   = $answers['product_featured'] ?? '';
        $productUsp        = $answers['product_usp'] ?? '';
        $productAdvantages = $answers['product_advantages'] ?? '';
        $prodCapacity      = $answers['production_capacity'] ?? '';
        $yearsInMarket     = $answers['years_in_market'] ?? '';

        // Owner
        $ownerGender    = $answers['owner_gender'] ?? '';
        $ownerAge       = $answers['owner_age'] ?? '';
        $ownerPersonal  = $answers['owner_personality'] ?? '';
        $ownerValues    = $answers['owner_values'] ?? '';
        $ownerHabits    = $answers['owner_habits'] ?? '';
        $ownerCommStyle = $answers['owner_comm_style'] ?? '';
        $ownerInterests = $answers['owner_interests'] ?? '';

        // Customer
        $custBuyReason    = $answers['customer_buy_reason'] ?? '';
        $custRepeatReason = $answers['customer_repeat_reason'] ?? '';
        $custNeedReason   = $answers['customer_need_reason'] ?? '';
        $custPersona      = $answers['customer_persona'] ?? '';

        // Revenue
        $monthlyRevenue    = $answers['monthly_revenue'] ?? '';
        $marketingBudget   = $answers['marketing_budget'] ?? '';

        // User profile data (from linked account)
        $client->loadMissing('user');
        $ownerName         = $client->user?->name ?? '';
        $ownerOrganization = $client->user?->organization ?? '';

        $postsSection = '';
        $socialAccounts = SocialAccount::where('client_id', $client->id)
            ->with('posts')
            ->get();

        if ($socialAccounts->isNotEmpty()) {
            $totalPostCount = $socialAccounts->sum(fn ($a) => $a->posts->count());
            $businessCount  = $socialAccounts->where('is_business_account', true)->count();
            $personalCount  = $socialAccounts->where('is_business_account', false)->count();
            $logger?->log("Menyertakan data dari {$socialAccounts->count()} akun ({$businessCount} bisnis, {$personalCount} personal)...");

            $postsSection = "\n\n=== DATA AKUN SOSIAL MEDIA ===";
            $postsSection .= "\nCATATAN PENTING: Akun yang ditandai PERSONAL kemungkinan tidak memposting konten bisnis. ";
            $postsSection .= "PRIORITASKAN brief bisnis dari form di atas. Gunakan data akun personal hanya sebagai referensi gaya komunikasi owner, BUKAN sebagai dasar diagnosis produk atau strategi bisnis.";

            foreach ($socialAccounts as $account) {
                $posts = $account->posts;
                if ($posts->isEmpty()) {
                    continue;
                }

                $postCount   = $posts->count();
                $totalLikes  = $posts->sum('like_count');
                $totalCmt    = $posts->sum('comment_count');
                $totalViews  = $posts->sum('view_count');
                $avgLikes    = round($totalLikes / $postCount);
                $avgComments = round($totalCmt / $postCount);
                $avgViews    = $totalViews > 0 ? round($totalViews / $postCount) : null;

                $followers = $account->follower_count ?? 0;
                $engRate   = $followers > 0
                    ? round((($totalLikes + $totalCmt) / $postCount / $followers) * 100, 2)
                    : null;

                $typeBreakdown = $posts->groupBy('type')
                    ->map->count()
                    ->map(fn ($c, $t) => "{$t}: {$c}")
                    ->values()->implode(', ');

                $topHashtags = $posts->flatMap(fn ($p) => $p->hashtags ?? [])
                    ->countBy()->sortDesc()->keys()->take(10)->implode(', ');

                $topPosts = $posts->sortByDesc(fn ($p) => $p->like_count + $p->comment_count)
                    ->take(5)->values()
                    ->map(fn ($p) => array_filter([
                        'type'     => $p->type,
                        'caption'  => mb_substr($p->caption ?? '', 0, 200),
                        'likes'    => $p->like_count,
                        'comments' => $p->comment_count,
                        'views'    => $p->view_count ?: null,
                    ], fn ($v) => $v !== null && $v !== ''));

                $worstPosts = $posts->sortBy(fn ($p) => $p->like_count + $p->comment_count)
                    ->take(2)->values()
                    ->map(fn ($p) => [
                        'type'     => $p->type,
                        'caption'  => mb_substr($p->caption ?? '', 0, 100),
                        'likes'    => $p->like_count,
                        'comments' => $p->comment_count,
                    ]);

                $accountLabel = $account->is_business_account ? 'AKUN BISNIS' : 'AKUN PERSONAL (bukan konten bisnis — gunakan hanya untuk memahami gaya komunikasi owner)';
                $postsSection .= "\n\n--- {$accountLabel} | {$account->platform}: @{$account->username} ---";
                $postsSection .= "\nFollowers: " . ($followers ? number_format($followers) : 'tidak diketahui');
                $postsSection .= "\nTotal postingan dianalisis: {$postCount}";
                $postsSection .= "\nRata-rata: {$avgLikes} likes, {$avgComments} komentar" . ($avgViews ? ", {$avgViews} views" : '');
                if ($engRate !== null) {
                    $postsSection .= "\nEngagement rate: {$engRate}%";
                }
                $postsSection .= "\nTipe konten: {$typeBreakdown}";
                $postsSection .= "\nHashtag paling sering dipakai: {$topHashtags}";
                $postsSection .= "\nTop 5 postingan terbaik: " . json_encode($topPosts->toArray(), JSON_UNESCAPED_UNICODE);
                $postsSection .= "\nPostingan performa terendah: " . json_encode($worstPosts->toArray(), JSON_UNESCAPED_UNICODE);

                // Include previous AI analysis if available
                if (! empty($account->ai_analysis)) {
                    $aiScore   = $account->ai_analysis['overall_score'] ?? null;
                    $aiGrade   = $account->ai_analysis['grade'] ?? null;
                    $aiSummary = $account->ai_analysis['summary'] ?? null;
                    if ($aiScore !== null) {
                        $postsSection .= "\nHasil analisis AI sebelumnya: Skor {$aiScore}/100 ({$aiGrade})";
                        if ($aiSummary) {
                            $postsSection .= " — {$aiSummary}";
                        }
                    }
                }
            }

            $postsSection .= "\n=== AKHIR DATA SOSIAL MEDIA ===\n";
        }

        // Build conditional sections only when filled
        $productSection = '';
        if ($productFeatured || $productUsp || $productAdvantages || $prodCapacity || $yearsInMarket) {
            $productSection .= "\n\n--- PRODUK UNGGULAN ---";
            if ($productFeatured)   $productSection .= "\nProduk/Layanan Unggulan: {$productFeatured}";
            if ($productUsp)        $productSection .= "\nUSP (Unique Selling Point): {$productUsp}";
            if ($productAdvantages) $productSection .= "\nKelebihan Produk: {$productAdvantages}";
            if ($prodCapacity)      $productSection .= "\nKapasitas Produksi/Stok: {$prodCapacity}";
            if ($yearsInMarket)     $productSection .= "\nLama di Market: {$yearsInMarket}";
        }

        $ownerSection = '';
        if ($ownerPersonal || $ownerValues || $ownerHabits || $ownerCommStyle || $ownerInterests || $ownerGender || $ownerAge) {
            $ownerSection .= "\n\n--- PROFIL OWNER ---";
            if ($ownerName)     $ownerSection .= "\nNama: {$ownerName}";
            if ($ownerGender)   $ownerSection .= "\nGender: {$ownerGender}";
            if ($ownerAge)      $ownerSection .= "\nUsia: {$ownerAge}";
            if ($ownerPersonal) $ownerSection .= "\nKarakteristik & Kepribadian: {$ownerPersonal}";
            if ($ownerValues)   $ownerSection .= "\nValue/Prinsip Berbisnis: {$ownerValues}";
            if ($ownerHabits)   $ownerSection .= "\nHabit Berbisnis: {$ownerHabits}";
            if ($ownerCommStyle) $ownerSection .= "\nGaya Komunikasi: {$ownerCommStyle}";
            if ($ownerInterests) $ownerSection .= "\nKesukaan/Hobi: {$ownerInterests}";
        }

        $customerSection = '';
        if ($custBuyReason || $custRepeatReason || $custNeedReason || $custPersona) {
            $customerSection .= "\n\n--- PROFIL CUSTOMER ---";
            if ($custBuyReason)    $customerSection .= "\nAlasan Pertama Membeli: {$custBuyReason}";
            if ($custRepeatReason) $customerSection .= "\nAlasan Repeat Order: {$custRepeatReason}";
            if ($custNeedReason)   $customerSection .= "\nMengapa Butuh Produk Ini: {$custNeedReason}";
            if ($custPersona)      $customerSection .= "\nCustomer Persona: {$custPersona}";
        }

        $revenueSection = '';
        if ($monthlyRevenue || $marketingBudget) {
            $revenueSection = "\n\nData Keuangan:";
            if ($monthlyRevenue)  $revenueSection .= "\nEstimasi Omzet/Bulan: {$monthlyRevenue}";
            if ($marketingBudget) $revenueSection .= "\nBiaya Marketing yang tersedia/Bulan: {$marketingBudget} (gunakan ini sebagai acuan utama rekomendasi budget iklan, BUKAN hitung persentase dari omzet)";
            if (!$marketingBudget && $monthlyRevenue) $revenueSection .= " (gunakan untuk menentukan rekomendasi budget iklan yang realistis)";
        }

        // Build DNA context block
        $dnaContextBlock = '';
        if (! empty($dnaResult)) {
            $dnaEngine       = App::make(BrandDnaEngine::class);
            $dnaContextBlock = "\n\n" . $dnaEngine->toPromptContext($dnaResult);
        }

        return <<<PROMPT
Kamu adalah konsultan strategi brand & media sosial berpengalaman. Tugasmu: tulis analisis naratif mendalam berdasarkan Brand DNA dan data bisnis yang sudah disediakan.

PENTING: Brand DNA sudah ditentukan oleh Koneva Scoring Engine (sistem rule-based, bukan AI). Kamu TIDAK boleh mengubah, mengabaikan, atau mempertanyakan DNA yang sudah ditentukan. Tugasmu adalah MENJELASKAN dan MENGEMBANGKAN insight berdasarkan DNA tersebut.
{$dnaContextBlock}

=== BRIEF BISNIS (dari form pemilik) ===
Nama Bisnis: {$company}
Industri: {$industry}
Organisasi/Lembaga: {$ownerOrganization}
Deskripsi Bisnis: {$description}
Target Audiens: {$targetAudience}
Tujuan Media Sosial: {$goals}
Platform yang Digunakan: {$platforms}
Gaya/Tone Konten yang Diinginkan: {$contentTone}
Kompetitor: {$competitors}
Keunggulan Unik: {$uniqueValue}
Testimoni Pelanggan: {$testimonials}
{$productSection}
{$ownerSection}
{$customerSection}
{$revenueSection}
{$postsSection}
=== INSTRUKSI ===
LANGKAH 1 — AUDIT KONTEN AKTUAL (dari data sosial media jika ada):
- Postingan mana yang mendapat engagement terbaik dan mengapa? Kaitkan dengan DNA yang teridentifikasi
- Pola caption/hashtag/format yang dipakai — apakah sudah selaras dengan DNA?
- Gap: apa yang perlu diubah agar konten lebih konsisten dengan DNA tersebut?

LANGKAH 2 — DIAGNOSIS BRAND (berdasarkan DNA + data bisnis):
- Setiap poin harus SPESIFIK: kutip detail dari brief atau data postingan
- Kaitkan kekuatan/tantangan dengan karakteristik DNA yang teridentifikasi
- Owner profile dan customer profile harus mencerminkan DNA tersebut

Berikan output dalam format JSON berikut persis (tanpa markdown, tanpa teks di luar JSON):
{
  "content_audit": {
    "top_performing_pattern": "Pola spesifik dari postingan yang mendapat engagement terbaik — format, topik, gaya caption yang berhasil. Jika tidak ada data: tulis 'Belum ada data postingan tersedia'",
    "current_gap": "Gap utama antara konten yang diposting saat ini dan konten ideal untuk bisnis ini — spesifik berdasarkan data",
    "owner_communication_style": "Gaya komunikasi owner berdasarkan postingan — formal/informal, informatif/promosi, storytelling/transaksional"
  },
  "diagnosis": {
    "product_strengths": [
      "Kelebihan produk/layanan — spesifik berdasarkan deskripsi bisnis atau testimoni (bukan generik)",
      "..."
    ],
    "product_weaknesses": [
      "Kekurangan atau tantangan — spesifik berdasarkan data postingan atau brief (misal: 'caption cenderung promosi langsung tanpa hook, rata-rata X likes lebih rendah dari konten edukatif')",
      "..."
    ],
    "market_fit": "Analisis daya jual produk ini di tren industri saat ini — berikan konteks tren spesifik yang relevan dengan industri dan lokasi (Indonesia)",
    "owner_profile": "Tipe owner berdasarkan brief + pola komunikasi dari postingan — karakter, kekuatan personal, dan apa yang membuat mereka unik",
    "owner_content_angle": "Apa yang HARUS ditonjolkan dari karakter owner pada konten — berikan rekomendasi konkret berdasarkan profil owner dan jenis bisnis",
    "customer_profile": "Tipe customer paling potensial — psikografis, kebiasaan, pain point, dan motivasi beli yang spesifik untuk produk/industri ini",
    "customer_content_trigger": "Berdasarkan tipe customer ini, konten SEPERTI APA yang terbukti memicu mereka tertarik dan membeli — berikan contoh konkret yang relevan",
    "main_problem": "Masalah utama dari brand ini adalah ...",
    "solution": "Masalah tersebut dapat diatasi jika ..."
  },
  "instagram_accounts": [],
  "tiktok_accounts": [],
  "content_ideas": [
    {
      "title": "Judul ide konten yang spesifik dan relevan dengan brand ini",
      "description": "Deskripsi detail apa yang dibuat, angle unik berdasarkan diagnosis di atas, dan hook opening yang menarik untuk target audiens ini",
      "format": "video",
      "search_keyword": "kata kunci bahasa Indonesia untuk mencari contoh di TikTok (hanya diisi jika format video/reel, kosongkan jika foto/carousel)",
      "suggested_hashtags": ["tag1", "tag2", "tag3", "tag4", "tag5"]
    }
  ],
  "strategy_insight": "Rekomendasi strategis berdasarkan audit konten + diagnosis: frekuensi posting yang realistis, waktu terbaik sesuai target audiens, pendekatan konten yang membedakan dari kompetitor, dan tindakan prioritas pertama yang harus dilakukan.",
  "ad_budget_recommendation": {
    "monthly_budget": "Rekomendasi budget iklan Meta/TikTok per bulan yang realistis berdasarkan omzet (jika omzet tidak diketahui, sarankan range 5-10% estimasi omzet UMKM sejenis)",
    "platform_split": "Alokasi ke platform mana dengan persentase berapa dan alasannya",
    "objective": "Tujuan iklan yang paling tepat untuk kondisi bisnis ini (awareness/traffic/conversion)",
    "expected_result": "Target hasil yang realistis dari budget tersebut dalam 30 hari"
  }
}

Aturan wajib:
- content_audit WAJIB diisi dan dikaitkan dengan Brand DNA yang teridentifikasi
- diagnosis WAJIB spesifik — setiap poin harus merujuk ke DNA, brief, atau data postingan
- owner_profile dan owner_content_angle harus konsisten dengan DNA dan karakteristik owner yang diisi
- customer_profile dan customer_content_trigger harus merujuk ke alasan beli, repeat reason, dan customer need
- content_ideas HARUS sesuai dengan Communication Strategy Template yang sudah diberikan (konten mix dan pilar konten dari DNA)
- main_problem dimulai "Masalah utama dari brand ini adalah ..."
- solution dimulai "Masalah tersebut dapat diatasi jika ..."
- instagram_accounts dan tiktok_accounts WAJIB array kosong []
- content_ideas TEPAT 6 dengan variasi: ide #1 = "video", ide #2 = "carousel", ide #3 = "foto", ide #4 = "reel", ide #5 = "video", ide #6 = "carousel"
- search_keyword HANYA diisi untuk "video" dan "reel"
- ad_budget_recommendation WAJIB diisi — jika omzet tidak diketahui tetap berikan estimasi berdasarkan skala bisnis
- Semua teks dalam Bahasa Indonesia
PROMPT;
    }

    private function jsonEncode(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function parseJson(string $text): ?array
    {
        $text    = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $text    = preg_replace('/\s*```$/', '', $text);
        $decoded = json_decode(trim($text), true);

        if (! is_array($decoded)) {
            Log::warning('DeepSeek returned non-JSON: ' . mb_substr($text, 0, 300));
            return null;
        }

        return $decoded;
    }
}
