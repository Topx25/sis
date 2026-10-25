<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('tags');
            $table->json('metrics')->nullable()->after('is_featured');
            $table->json('scope')->nullable()->after('metrics');
            $table->json('technologies')->nullable()->after('scope');
            $table->json('results')->nullable()->after('technologies');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'metrics', 'scope', 'technologies', 'results']);
        });
    }
};
