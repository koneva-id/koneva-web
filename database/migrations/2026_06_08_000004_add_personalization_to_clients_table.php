<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->json('personalization_answers')->nullable()->after('industry');
            $table->timestamp('personalized_at')->nullable()->after('personalization_answers');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['personalization_answers', 'personalized_at']);
        });
    }
};
