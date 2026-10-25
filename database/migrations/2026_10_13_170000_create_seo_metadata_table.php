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
        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();

            // Explicit polymorphic columns to avoid redundant indexes
            $table->string('seoable_type', 150);
            $table->unsignedBigInteger('seoable_id');

            // Translatable fields stored as JSON (en/ar)
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->json('og_title')->nullable();
            $table->json('og_description')->nullable();

            // Media and canonical URL directives
            $table->string('og_image', 500)->nullable();
            $table->string('canonical_url', 500)->nullable();

            // Search engine robots directives
            $table->boolean('is_noindex')->default(false);
            $table->boolean('is_nofollow')->default(false);

            // Schema.org structured data overrides
            $table->string('schema_type', 50)->nullable();
            $table->json('schema_overrides')->nullable();

            $table->timestamps();

            // Strict 1-to-1 relationship per entity with optimized composite unique index
            $table->unique(['seoable_type', 'seoable_id'], 'seo_metadata_seoable_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_metadata');
    }
};
