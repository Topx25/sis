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
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();

            // Canonical & verification configuration
            $table->string('canonical_base_url', 255)->default('https://sis.iptvpro.org');
            $table->string('google_site_verification', 255)->nullable();
            $table->string('bing_site_verification', 255)->nullable();
            $table->boolean('default_noindex')->default(false);

            // Translatable global defaults (bilingual JSON: en/ar)
            $table->json('site_name');
            $table->json('title_suffix');
            $table->json('default_meta_description');
            $table->string('default_og_image', 500)->nullable();

            // Organization schema graph data
            $table->string('organization_legal_name', 255)->nullable();
            $table->string('organization_phone', 100)->nullable();
            $table->string('organization_email', 255)->nullable();
            $table->json('social_links')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
