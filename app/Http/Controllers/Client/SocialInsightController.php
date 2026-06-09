<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeSocialAccountJob;
use App\Jobs\FetchSocialPostsJob;
use App\Jobs\GenerateAiRecommendationJob;
use App\Models\AiRecommendation;
use App\Models\Client;
use App\Models\SocialAccount;
use App\Services\AiInsightService;
use App\Services\SocialInsightService;
use App\Support\AiProgressLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocialInsightController extends Controller
{
    public function index(Request $request, AiInsightService $aiService): View|RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        // Redirect to personalization form if not yet completed
        if (! $client->personalized_at) {
            return redirect()->route('client.insights.personalize');
        }

        $accounts = SocialAccount::where('client_id', $client->id)
            ->with(['posts' => fn ($q) => $q->orderByDesc('like_count')])
            ->get();

        $allPosts = $accounts->flatMap(fn ($a) => $a->posts);

        // Top 3 posts by engagement (likes + comments)
        $topPosts = $allPosts->sortByDesc(fn ($p) => $p->like_count + $p->comment_count)->take(3)->values();

        // Group remaining posts by their most prominent hashtag
        $postsByHashtag = $this->groupByHashtag($allPosts->all());

        // AI recommendations — driven by personalization form, not social posts
        $aiRecommendation = null;
        if ($client->personalized_at && config('services.deepseek.key')) {
            $aiRecommendation = $aiService->getRecommendation($client);

            // Auto-refresh in background if expired (non-blocking — runs after response)
            if ($aiService->isRecommendationExpired($client)) {
                $logger = new AiProgressLogger($client->id);
                $logger->start();
                GenerateAiRecommendationJob::dispatchAfterResponse($client);
            }
        }

        return view('client.insights', [
            'client'           => $client,
            'accounts'         => $accounts,
            'topPosts'         => $topPosts,
            'postsByHashtag'   => $postsByHashtag,
            'rapidApiEnabled'  => (bool) config('services.rapidapi.key'),
            'aiRecommendation' => $aiRecommendation,
            'deepseekEnabled'  => (bool) config('services.deepseek.key'),
        ]);
    }

    public function aiStatus(Request $request): JsonResponse
    {
        $client = $this->resolveClientProfile($request);
        $logger = new AiProgressLogger($client->id);

        $ready = AiRecommendation::where('client_id', $client->id)
            ->where('expires_at', '>', now())
            ->exists();

        return response()->json([
            'ready' => $ready,
            'logs'  => $logger->get(),
        ]);
    }

    public function refreshAiRecommendations(Request $request): RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        $logger = new AiProgressLogger($client->id);
        $logger->start();
        $logger->thinking('Memulai pembaruan rekomendasi AI...');

        GenerateAiRecommendationJob::dispatchAfterResponse($client);

        return redirect()->route('client.insights.index')
            ->with('status', 'Rekomendasi AI sedang diperbarui — muat ulang halaman dalam beberapa saat.');
    }

    public function showPersonalize(Request $request): View|RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        if ($client->personalized_at) {
            return redirect()->route('client.insights.index');
        }

        return view('client.personalize', compact('client'));
    }

    public function submitPersonalize(Request $request): RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        $validated = $request->validate([
            'company_name'    => ['required', 'string', 'max:100'],
            'industry'        => ['required', 'string', 'in:food,fashion,beauty,technology,education,health,property,retail,finance,creative'],
            'description'     => ['required', 'string', 'max:500'],
            'target_audience' => ['required', 'string', 'max:300'],
            'goals'           => ['nullable', 'array'],
            'goals.*'         => ['string'],
            'platforms'       => ['nullable', 'array'],
            'platforms.*'     => ['string'],
            'content_tone'    => ['nullable', 'string'],
            // Produk
            'product_featured'    => ['nullable', 'string', 'max:400'],
            'product_usp'         => ['nullable', 'string', 'max:300'],
            'product_advantages'  => ['nullable', 'string', 'max:300'],
            'production_capacity' => ['nullable', 'string', 'max:150'],
            'years_in_market'     => ['nullable', 'string', 'in:baru,6-12,1-2,2-5,5+'],
            // Owner
            'owner_gender'      => ['nullable', 'string', 'max:20'],
            'owner_age'         => ['nullable', 'string', 'max:10'],
            'owner_personality' => ['nullable', 'string', 'max:400'],
            'owner_values'      => ['nullable', 'string', 'max:300'],
            'owner_habits'      => ['nullable', 'string', 'max:300'],
            'owner_comm_style'  => ['nullable', 'string', 'max:30'],
            'owner_interests'   => ['nullable', 'string', 'max:200'],
            // Customer
            'customer_buy_reason'    => ['nullable', 'string', 'max:400'],
            'customer_repeat_reason' => ['nullable', 'string', 'max:400'],
            'customer_need_reason'   => ['nullable', 'string', 'max:400'],
            'customer_persona'       => ['nullable', 'string', 'max:500'],
            // Bisnis & keuangan
            'monthly_revenue'          => ['nullable', 'string', 'max:20'],
            'competitors'              => ['nullable', 'string', 'max:300'],
            'unique_value'             => ['nullable', 'string', 'max:300'],
            'customer_testimonials'    => ['nullable', 'string', 'max:500'],
        ]);

        $answers = [
            'company_name'          => $validated['company_name'],
            'industry'              => $validated['industry'],
            'description'           => $validated['description'],
            'target_audience'       => $validated['target_audience'],
            'goals'                 => $validated['goals'] ?? [],
            'platforms'             => $validated['platforms'] ?? [],
            'content_tone'          => $validated['content_tone'] ?? '',
            // Produk
            'product_featured'    => $validated['product_featured'] ?? '',
            'product_usp'         => $validated['product_usp'] ?? '',
            'product_advantages'  => $validated['product_advantages'] ?? '',
            'production_capacity' => $validated['production_capacity'] ?? '',
            'years_in_market'     => $validated['years_in_market'] ?? '',
            // Owner
            'owner_gender'      => $validated['owner_gender'] ?? '',
            'owner_age'         => $validated['owner_age'] ?? '',
            'owner_personality' => $validated['owner_personality'] ?? '',
            'owner_values'      => $validated['owner_values'] ?? '',
            'owner_habits'      => $validated['owner_habits'] ?? '',
            'owner_comm_style'  => $validated['owner_comm_style'] ?? '',
            'owner_interests'   => $validated['owner_interests'] ?? '',
            // Customer
            'customer_buy_reason'    => $validated['customer_buy_reason'] ?? '',
            'customer_repeat_reason' => $validated['customer_repeat_reason'] ?? '',
            'customer_need_reason'   => $validated['customer_need_reason'] ?? '',
            'customer_persona'       => $validated['customer_persona'] ?? '',
            // Bisnis & keuangan
            'monthly_revenue'          => $validated['monthly_revenue'] ?? '',
            'competitors'              => $validated['competitors'] ?? '',
            'unique_value'             => $validated['unique_value'] ?? '',
            'customer_testimonials'    => $validated['customer_testimonials'] ?? '',
        ];

        $client->update([
            'company_name'            => $validated['company_name'],
            'industry'                => $validated['industry'],
            'personalization_answers' => $answers,
            'personalized_at'         => now(),
        ]);

        // Seed progress logger before job so insights page shows it immediately on redirect
        if (config('services.deepseek.key')) {
            $logger = new AiProgressLogger($client->id);
            $logger->start();
            $logger->thinking('Profil bisnis tersimpan. Memulai analisis AI...');
            GenerateAiRecommendationJob::dispatchAfterResponse($client);
        }

        return redirect()->route('client.insights.index')
            ->with('status', 'Profil bisnis tersimpan! Rekomendasi AI sedang disiapkan — muat ulang halaman dalam beberapa saat.');
    }

    public function connect(Request $request): RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        $validated = $request->validate([
            'platform'            => ['required', 'in:instagram,tiktok'],
            'username'            => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z0-9._]+$/'],
            'is_business_account' => ['nullable', 'boolean'],
        ]);

        $account = SocialAccount::updateOrCreate(
            ['client_id' => $client->id, 'platform' => $validated['platform']],
            [
                'username'            => ltrim($validated['username'], '@'),
                'is_business_account' => $request->boolean('is_business_account', true),
            ]
        );

        FetchSocialPostsJob::dispatchAfterResponse($account);

        return redirect()->route('client.insights.index')
            ->with('status', "Akun {$validated['platform']} berhasil terhubung. Data sedang diambil.");
    }

    public function refresh(Request $request, SocialAccount $account): RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        if ($account->client_id !== $client->id) {
            abort(403);
        }

        FetchSocialPostsJob::dispatchAfterResponse($account);

        return redirect()->route('client.insights.index')
            ->with('status', 'Refresh data dijadwalkan.');
    }

    public function analyzeAccount(Request $request, SocialAccount $account): RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        if ($account->client_id !== $client->id) {
            abort(403);
        }

        $logger = new AiProgressLogger($account->id, 'ai_analysis');
        $logger->start();
        $logger->thinking("Memulai analisis AI untuk @{$account->username}...");

        AnalyzeSocialAccountJob::dispatchAfterResponse($account);

        return redirect()->route('client.insights.index')
            ->with('status', 'Analisis AI dijadwalkan — muat ulang halaman dalam beberapa saat.');
    }

    public function analysisStatus(Request $request, SocialAccount $account): JsonResponse
    {
        $client = $this->resolveClientProfile($request);

        if ($account->client_id !== $client->id) {
            abort(403);
        }

        $logger = new AiProgressLogger($account->id, 'ai_analysis');
        $account->refresh();

        return response()->json([
            'ready' => $account->ai_analysis !== null,
            'logs'  => $logger->get(),
        ]);
    }

    public function updateAccountType(Request $request, SocialAccount $account): RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        if ($account->client_id !== $client->id) {
            abort(403);
        }

        $account->update([
            'is_business_account' => $request->boolean('is_business_account'),
        ]);

        $type = $account->is_business_account ? 'bisnis' : 'personal';

        return redirect()->route('client.insights.index')
            ->with('status', "Akun @{$account->username} ditandai sebagai akun {$type}.");
    }

    public function disconnect(Request $request, SocialAccount $account): RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        if ($account->client_id !== $client->id) {
            abort(403);
        }

        $account->delete();

        return redirect()->route('client.insights.index')
            ->with('status', 'Akun berhasil diputus.');
    }

    public function updateIndustry(Request $request): RedirectResponse
    {
        $client = $this->resolveClientProfile($request);

        $validated = $request->validate([
            'industry' => ['required', 'string', 'max:100'],
        ]);

        $client->update(['industry' => $validated['industry']]);

        return redirect()->route('client.insights.index')
            ->with('status', 'Industri berhasil disimpan. Rekomendasi akun serupa akan muncul di bawah.');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Group posts by their most prominent hashtag.
     * Posts without hashtags go to "Lainnya".
     *
     * @param  \App\Models\SocialPost[]  $posts
     * @return array<string, \App\Models\SocialPost[]>
     */
    private function groupByHashtag(array $posts): array
    {
        $groups = [];

        foreach ($posts as $post) {
            $hashtags = $post->hashtags ?? [];
            $tag      = ! empty($hashtags) ? '#' . $hashtags[0] : 'Lainnya';
            $groups[$tag][] = $post;
        }

        // Sort groups by total likes descending
        uasort($groups, fn ($a, $b) =>
            array_sum(array_column($b, 'like_count')) <=> array_sum(array_column($a, 'like_count'))
        );

        return $groups;
    }

    private function resolveClientProfile(Request $request): Client
    {
        $user = $request->user();

        return Client::firstOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $user->organization ?: $user->name,
                'status'       => 'active',
            ]
        );
    }
}
