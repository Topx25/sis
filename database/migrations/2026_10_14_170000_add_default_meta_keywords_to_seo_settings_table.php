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
        if (Schema::hasTable('seo_settings') && !Schema::hasColumn('seo_settings', 'default_meta_keywords')) {
            Schema::table('seo_settings', function (Blueprint $table) {
                $table->json('default_meta_keywords')->nullable()->after('default_meta_description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seo_settings') && Schema::hasColumn('seo_settings', 'default_meta_keywords')) {
            Schema::table('seo_settings', function (Blueprint $table) {
                $table->dropColumn('default_meta_keywords');
            });
        }
    }
};
