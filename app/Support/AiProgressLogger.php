<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class AiProgressLogger
{
    private const TTL_MINUTES = 30;

    private string $cacheKey;

    public function __construct(int $id, string $prefix = 'ai_progress')
    {
        $this->cacheKey = "{$prefix}_{$id}";
    }

    public function start(): void
    {
        Cache::put($this->cacheKey, [], now()->addMinutes(self::TTL_MINUTES));
    }

    public function log(string $message, string $type = 'info'): void
    {
        $entries = Cache::get($this->cacheKey, []);
        $entries[] = [
            'message' => $message,
            'type'    => $type, // info | success | error | thinking
            'time'    => now()->format('H:i:s'),
        ];
        Cache::put($this->cacheKey, $entries, now()->addMinutes(self::TTL_MINUTES));
    }

    public function success(string $message): void
    {
        $this->log($message, 'success');
    }

    public function error(string $message): void
    {
        $this->log($message, 'error');
    }

    public function thinking(string $message): void
    {
        $this->log($message, 'thinking');
    }

    public function get(): array
    {
        return Cache::get($this->cacheKey, []);
    }

    public function clear(): void
    {
        Cache::forget($this->cacheKey);
    }
}
