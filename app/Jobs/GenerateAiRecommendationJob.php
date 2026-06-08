<?php

namespace App\Jobs;

use App\Models\Client;
use App\Services\AiInsightService;
use App\Support\AiProgressLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateAiRecommendationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(private readonly Client $client) {}

    public function handle(AiInsightService $service): void
    {
        set_time_limit(300);

        // Don't call start() — controller already seeded the logger before dispatching
        $logger = new AiProgressLogger($this->client->id);

        $result = $service->refreshRecommendation($this->client, $logger);

        if ($result === null) {
            $logger->error('Generasi rekomendasi gagal. Coba klik Perbarui AI.');
            Log::warning("AI recommendation generation failed for client #{$this->client->id}");
        } else {
            Log::info("AI recommendation generated for client #{$this->client->id}");
        }
    }
}
