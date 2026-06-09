<?php

namespace App\Console\Commands;

use App\Models\AiRecommendation;
use App\Models\Client;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Console\Command;

class ResetInsights extends Command
{
    protected $signature = 'insights:reset
                            {email? : Email user yang akan di-reset (kosongkan untuk semua)}
                            {--posts : Hapus juga social posts & accounts}';

    protected $description = 'Reset personalisasi bisnis untuk testing ulang';

    public function handle(): int
    {
        $email = $this->argument('email');
        $withPosts = $this->option('posts');

        if ($email) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $this->error("User dengan email '{$email}' tidak ditemukan.");
                return self::FAILURE;
            }

            $client = Client::where('user_id', $user->id)->first();

            if (! $client) {
                $this->warn("User '{$email}' belum punya client profile.");
                return self::SUCCESS;
            }

            $this->resetClient($client, $withPosts);
            $this->info("Reset selesai untuk: {$email}");
        } else {
            if (! $this->confirm('Reset personalisasi untuk SEMUA client? Ini tidak bisa dibatalkan.')) {
                $this->line('Dibatalkan.');
                return self::SUCCESS;
            }

            $clients = Client::all();
            foreach ($clients as $client) {
                $this->resetClient($client, $withPosts);
            }
            $this->info("Reset selesai untuk {$clients->count()} client.");
        }

        return self::SUCCESS;
    }

    private function resetClient(Client $client, bool $withPosts): void
    {
        // Clear personalization
        $client->update([
            'personalization_answers' => null,
            'personalized_at'         => null,
            'industry'                => null,
        ]);

        // Always delete cached AI recommendations
        AiRecommendation::where('client_id', $client->id)->delete();

        if ($withPosts) {
            SocialAccount::where('client_id', $client->id)->delete();
            $this->line("  - [{$client->company_name}] posts & accounts dihapus");
        } else {
            $this->line("  - [{$client->company_name}] personalisasi & AI cache di-reset");
        }
    }
}
