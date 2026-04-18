<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('description');
            $table->string('file_type', 120)->nullable()->after('file_name');
            $table->string('storage_path')->nullable()->after('file_type');
            $table->boolean('is_visible_to_client')->default(false)->after('status');
            $table->timestamp('published_at')->nullable()->after('is_visible_to_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->dropColumn([
                'file_name',
                'file_type',
                'storage_path',
                'is_visible_to_client',
                'published_at',
            ]);
        });
    }
};
