<?php

namespace App\Jobs;

use App\Models\SocialAccount;
use App\Services\AiInsightService;
use App\Support\AiProgressLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeSocialAccountJob implements ShouldQueue
{
    use Queueable;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(private readonly SocialAccount $account) {}

    public function handle(AiInsightService $service): void
    {
        set_time_limit(300);

        Log::info("Analyzing social account {$this->account->platform}/@{$this->account->username}");

        // Don't call start() — controller already seeded the logger before dispatching
        $logger = new AiProgressLogger($this->account->id, 'ai_analysis');

        $service->analyzeAccount($this->account, $logger);
    }
}
